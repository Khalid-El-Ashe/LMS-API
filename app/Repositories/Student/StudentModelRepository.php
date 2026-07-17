<?php

namespace App\Repositories\Student;

use App\Http\Resources\Student\StudentResource;
use App\Models\Admin;
use App\Models\Mentor;
use App\Models\Student;
use App\Models\Task;
use App\Models\TaskSubmission;
use App\Notifications\NotifyNewStudent;
use App\Notifications\WelcomeMessage;
use App\Services\CountryService;
use App\Services\FileUploadService;
use App\Services\MajorService;
use App\Services\UniversityService;
use App\Traits\ApiResponseTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use function PHPUnit\Framework\throwException;

class StudentModelRepository implements StudentRepository
{
    use ApiResponseTrait;

    public function __construct(private readonly FileUploadService $fileUploadService)
    {
    }

    // this function for admin
    public function getAllStudents()
    {
        $students = Student::withoutTrashed()->with('courses')->latest()->paginate(10);

        return $students->map(fn($student) => [
            'full_name' => $student->full_name,
            'email' => $student->email,
            'courses' => $student->courses->pluck('name'),
        ]);
    }

    public function getAllStudentsForMentor(Mentor $mentor)
    {
        return Student::query()->with('courses:id,name')->whereHas('courses.mentors', function ($query) use ($mentor) {
            $query->where('mentors.id', $mentor->id);
        })->latest()
            ->paginate(10)->through(fn($student) => [
                'full_name' => $student->full_name,
                'slug' => $student->slug,
                'email' => $student->email,
                'profile_photo' => asset('storage/' . $student->profile_image) ?? null,
                'courses' => $student->courses->pluck('name')->values(),
            ]);
    }

    public function getStudentInfoForMentor(Student $student)
    {
//        $student->load('courses:id,name');
//
//        return [
//            'slug' => $student->slug,
//            'full_name' => $student->full_name,
//            'email' => $student->email,
//            'profile_image' => $student->profile_image
//                ? asset('storage/' . $student->profile_image)
//                : null,
//            'university_name' => $student->university_name,
//            'university_major' => $student->university_major,
//            'mobile_number' => $student->code_mobile . $student->mobile_number,
//            'gender' => $student->gender,
//            'courses' => $student->courses->map(fn ($course) => [
//                'id' => $course->id,
//                'name' => $course->name,
//            ]),
//        ];

        $student->load([
            'courses.mentors',
            'courses.tasks',
            'submissions'
        ]);

        $totalTasks = $student->courses
            ->flatMap->tasks
            ->unique('id')
            ->count();

        $answeredTasks = $student->submissions
            ->pluck('task_id')
            ->unique()
            ->count();


        $student->total_tasks = $totalTasks;
        $student->answered_tasks = $answeredTasks;

        return $student;
    }

    public function getAllStudentsIsTrashed()
    {
        $students = Student::onlyTrashed()->with('courses')->latest()->paginate(10); // Eger Loading
        return $students->map(fn($student) => [
            'full_name' => $student->full_name,
            'email' => $student->email,
            'profile_photo' => asset('storage/' . $student->profile_image) ?? null,
        ]);
    }

    public function register(array $data)
    {
        // generate the username
        $basename = strtolower(str_replace(' ', '', $data['full_name'])); // removing the spaces
        $uniqueNum = Str::random(5);
        $data['username'] = $basename . '_' . $uniqueNum;

        $country = CountryService::getCodeByIso($data['country_iso']);
        $data['code_mobile'] = $country['countryCode'] ?? null;

        $mobile = ltrim($data['mobile_number'], '0');
        $data['mobile_number'] = $mobile;

        $university_major = MajorService::exists($data['university_major']);
        $data['university_major'] = $university_major['name'] ?? null;

        $university_name = UniversityService::exists($data['university_name']);
        $data['university_name'] = $university_name['un_name'] ?? null;

        // $data['course_id'] =
        $student = Student::query()->create($data);

        // now I need to check if the course_id is sent in the request or not if sent, I will attach the student to the course
        if (!empty($data['course_id'])) {
            $student->courses()->syncWithoutDetaching($data['course_id']);
        }

        // need get the email admins to send the emails
        $email_admins = Admin::query()->pluck('email');
        if (count($email_admins)) {
            Notification::route('mail', $email_admins)->notify(new NotifyNewStudent($data['full_name']));
        }

        $student->notify(new WelcomeMessage()); // email student
//        return new StudentResource($student->load('courses'));
        return [
            'full_name' => $student->full_name,
            'username' => $student->username,
            'slug' => $student->slug,
            'role' => 'student',
        ];
    }

    public function login(array $data)
    {

        $student = Student::query()->where('username', '=', $data['username'])->first();

        if (!$student) {
            throw ValidationException::withMessages([
                'username' => ['Invalid credentials'],
            ]);
        }

        // now I need to check if the account is Active or not
        if (!$student->is_active) {
            throw ValidationException::withMessages([
                'account' => ['Account is inactive']
            ]);
        }

        Auth::guard('student')->login($student);

        # this is to forbid the Session Fixation Attack
        \request()->session()->regenerate();
        session([
            'device_hash' => hash('sha256', request()->userAgent()),
            'ip' => request()->ip(),
            'last_activity' => now(),
        ]);

        return [
            'user' => [
                'slug' => $student->slug,
                'name' => $student->full_name,
                'email' => $student->email,
                'mentor' => $student->courses()->with('mentors')->get()
                    ->flatMap(fn($course) => $course->mentors)
                    ->unique('id')
                    ->values()
                    ->map(fn($mentor) => $mentor->name),
                'role' => 'student'
            ],
        ];
    }

    public function logout()
    {
        $student = Auth::guard('student')->user();

        if (!$student) {
            return throw ValidationException::withMessages([
                'user' => ['User not authenticated'],
            ]);
        }

        // Delete the current access token directly
//        $user->currentAccessToken()?->delete();
//        Cache::forget('student_token_' . $user->id);
        Auth::guard('student')->logout();
        \request()->session()->invalidate();
        \request()->session()->regenerateToken();
        return true;
    }

    public function updateStudentData(Student $student, array $data)
    {
//        $student = Student::query()->findOrFail($data['student_id']);
        $student->update($data);
        return $student->fresh();
    }

    # this function for student to get his info
    public function getStudentProfile()
    {
        return auth()->guard('student')->user()
            ->load([
                'courses.videos.tasks',
                'courses.mentors',
                'videoProgress'
            ]);
    }

    // this function for admin to enable the student account
    public function enableStudent(Student $student)
    {
        // now need to enable the student account by update the is_active column to true
        return $student->update(['is_active' => true]);
    }

    // this function for admin to disable the student account
    public function disableStudent(Student $student)
    {
        // now need to disable the student account by update the is_active column to false
        return $student->update(['is_active' => false]);
    }

    public function deleteStudent(Student $student)
    {
        // now need to check if the student have a token or not if you have a token will delete it and then will delete the student
        if ($student->tokens()->exists()) {
            $student->tokens()->delete();
        }
        // now need to use the softdelete to add this student in the trash
        return $student->delete();
    }

    // this function for admin to restore the student from the trash
    public function restoreStudent(Student $student)
    {
        // now need to restore the student from the trash
        $student = $student->onlyTrashed()->find($student->id);
        return $student->restore();
    }

    // this function for admin to delete the student from database permanently
    public function forceDeleteStudent(Student $student)
    {
        // now need to check if the student have a token or not if you have a token will delete it and then will delete the student
        // if ($student->tokens()->exists()) {
        //     $student->tokens()->delete();
        // }

        // now need to use the forceDelete to delete this student from database permanently
        $student = $student->withTrashed()->find($student->id);
        return $student->forceDelete();
    }

    /**
     * @param string $searchTerm
     * @return AnonymousResourceCollection
     */
    public function searchStudents(string $searchTerm)
    {
        # now need using the scope created in Student Model
        $student = Student::searchStudents(searchTerm: $searchTerm)->with('courses')->latest()->paginate(5);
        return StudentResource::collection($student);
    }

    /**
     * @param Request $request
     * @return AnonymousResourceCollection
     */
    public function filterStudents(Request $request)
    {
        $students = Student::filterStudents($request->all())->with('courses')->latest()->paginate(10);
        return StudentResource::collection($students);
    }

    /**
     * @param Student $student
     * @param $image
     * @return string
     */
    public function uploadProfileImage(Student $student, $image): string
    {
        if ($student->profile_image) {
            Storage::disk('public')
                ->delete($student->profile_image);
        }

        $path = $this->fileUploadService->upload($image, 'students/profile-image');
        $student->update(['profile_image' => $path]);
        return asset('storage/' . $path);
    }

    /**
     * @param Student $student
     * @param array $files
     * @return array
     */
    public function uploadMultipleFiles(Student $student, array $files): array
    {
        $paths = $this->fileUploadService->uploadMany($files, 'students/files');
        $oldFiles = $student->files ?? [];
        $student->update(['files' => array_merge($oldFiles, $paths)]);
        return $paths;
    }

    public function getStudentPath()
    {
        $student = auth()->guard('student')->user();

        return $student->load([
            'courses.videos',
            'videoProgress'
        ]);
    }



}

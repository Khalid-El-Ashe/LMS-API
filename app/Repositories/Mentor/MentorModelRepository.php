<?php

namespace App\Repositories\Mentor;

use App\Http\Resources\Mentor\MentorResource;
use App\Models\Admin;
use App\Models\Mentor;
use App\Models\Student;
use App\Models\TaskSubmission;
use App\Notifications\NotifyNewStudent;
use App\Notifications\WelcomeMessage;
use App\Services\CountryService;
use App\Services\FileUploadService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use function request;

class MentorModelRepository implements MentorRepository
{
    use ApiResponseTrait;

    public function __construct(private FileUploadService $fileUploadService)
    {
    }


    public function registerMentor(array $data)
    {
        // generate the username
        $basename = strtolower(str_replace(' ', '', $data['name'])); // removing the spaces
        $uniqueNum = Str::random(5);
        $data['username'] = $basename . "$" . $uniqueNum;

        $country = CountryService::getCodeByIso($data['country_iso'] ?? null);
        $data['code_mobile'] = $country['countryCode'] ?? null;

        $mobile = ltrim($data['mobile_number'], '0');
        $data['mobile_number'] = $mobile;

        $state = CountryService::getStatesByCode($data['state_code'] ?? null);
        $data['state'] = $state['name'];
        unset($data['state_code']);

        $mentor = Mentor::query()->create($data);

        // now I need to check if the course_id is sent in the request or not if sent, I will attach the student to the course
        if (!empty($data['course_id']) && is_array($data['course_id'])) {
            $mentor->courses()->syncWithoutDetaching($data['course_id']);
        }

        // need get the email admins to send the emails
        $email_admins = Admin::query()->pluck('email');
        if (count($email_admins)) {
            Notification::route('mail', $email_admins)->notify(new NotifyNewStudent($data['name']));
        }

        $mentor->notify(new WelcomeMessage()); // send email to mentor
        $mentor->assignRole('mentor');

        return [
            'slug' => $mentor->slug,
            'name' => $mentor->name,
            'username' => $mentor->username,
            'id' => $mentor->id,
        ];
    }

    public function getAllMentors()
    {
        return Mentor::withoutTrashed()->with('courses')->latest()->paginate()->map(fn($student) => [
            'name' => $student->name,
            'slug' => $student->slug,
            'status' => $student->status,
            'email' => $student->email,
            'courses' => $student->courses->pluck('name'),
        ]);
    }

    public function loginMentor(array $data)
    {
        $mentor = Mentor::query()->where('username', '=', $data['username'])->first();

        // now this if the username is not found in database send this message
        if (!$mentor) {
            throw ValidationException::withMessages([
                'username' => ['Invalid credentials']
            ]);
        }

        // now I need to check if the account is Active or not
        if (!$mentor->is_active) {
            throw ValidationException::withMessages([
                'account' => ['Your account is not active, please contact the administrator']
            ]);
        }

        Auth::guard('mentor')->login($mentor);
        request()->session()->regenerate(); # this is to forbid the Session Fixation Attack
        session([
            'device_hash' => hash('sha256', request()->userAgent()),
            'ip' => request()->ip(),
            'last_activity' => now(),
        ]);
        $permissions = $mentor->getAllPermissions()->pluck('name');

        return [
            'user' => [
                'slug' => $mentor->slug,
                'name' => $mentor->name,
                'course_name' => $mentor->courses()->pluck('name'),
                'role' => 'mentor',
            ],
//            'permissions' => $permissions
        ];
    }


    public function updateInformation(Mentor $mentor, array $data)
    {
        $mentor->update($data);
        return $mentor->fresh();
    }

    public function mentorDashboard(Mentor $mentor)
    {

        $mentor->load(['courses:id,name,description']);

//        $studentCount = $mentor->courses()->count();
        $mentor->student_count = Student::query()
            ->whereHas('courses.mentors', function ($query) use ($mentor) {
                $query->where('mentors.id', $mentor->id);
            })
            ->count();

        $mentor->course_video_count = $mentor->courses()
            ->withCount('videos')
            ->get()
            ->sum('videos_count');

        $mentor->last_submissions = TaskSubmission::query()
            ->whereHas('task.course.mentors', function ($query) use ($mentor) {
                $query->where('mentors.id', $mentor->id);
            })
            ->with([
                'student:id,full_name',
                'task:id,title',
            ])
            ->latest()
            ->limit(3)
            ->get();

//        $lastSubmissions = TaskSubmission::query()
//            ->whereHas('task.course.mentors', function ($query) use ($mentor) {
//                $query->where('mentors.id', $mentor->id);
//            })
//            ->with([
//                'student:id,full_name',
//                'task:id,title'
//            ])
//            ->latest()
//            ->limit(3)
//            ->get()
//            ->map(function ($submission) {
//                return [
//                    'student_name' => $submission->student?->full_name,
//                    'task_title' => $submission->task?->title,
//                    'submitted_at' => $submission->created_at->format('Y-m-d H:i:s'),
//                ];
//            });

        return $mentor;
//        return [
//            'name' => $mentor->name,
//            'email' => $mentor->email,
//            'profile_image' => asset('storage/' . $mentor->profile_image) ?? null,
//            'course_name' => $courses->pluck('name')->values(), # $mentor->courses()->pluck('name'),
//            'student_count' => $studentCount ?? 0,
//            'course_video_count' => $videoCount ?? 0,
//            'last_submissions' => $lastSubmissions ?? null,
//            'last_task_submissions_count' => $lastSubmissions->count(),
//        ];
    }

    public function logoutMentor(): true|JsonResponse
    {
        $mentor = Auth::guard('mentor')->user();
        if (!$mentor) {
            return $this->error('User not authenticated', 401);
        }

//        Cache::forget('mentor_token_' . $user->id);
        Auth::guard('mentor')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return true;
    }

    /**
     * @param Mentor $mentor
     * @return bool
     */
    public function enableMentor(Mentor $mentor): bool
    {
        // now need to enable the student account by update the is_active column to true
        return $mentor->update(['is_active' => true]);
    }

    public function disableMentor(Mentor $mentor): bool
    {
        return $mentor->update(['is_active' => false]);
    }

    /**
     * @param Mentor $mentor
     * @param $image
     * @return string
     */
    public function uploadProfileImage(Mentor $mentor, $image): string
    {
        if ($mentor->profile_image
        ) {
            Storage::disk('public')
                ->delete($mentor->profile_image);
        }

        $path = $this->fileUploadService->upload($image, 'mentors/profile-image');
        $mentor->update(['profile_image' => $path]);
        return asset('storage/' . $path);
    }

    /**
     * @param Mentor $mentor
     * @param array $files
     * @return array
     */
    public function uploadMultipleFiles(Mentor $mentor, array $files)
    {
        $paths = $this->fileUploadService->uploadMany($files, 'mentors/files');
        $oldFiles = $mentor->files ?? [];
        $mentor->update(['files' => array_merge($oldFiles, $paths)]);
        return $paths;
    }

}

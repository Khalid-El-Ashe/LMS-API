<?php

namespace App\Repositories\Admin;

use App\Jobs\SendEmailToMentorJob;
use App\Jobs\SendEmailToStudentJob;
use App\Models\Admin;
use App\Models\Mentor;
use App\Models\Student;
use App\Traits\ApiResponseTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AdminModelRepository implements AdminRepository
{
    use ApiResponseTrait;

    public function createAdmin(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $admin = Admin::query()->create($data);
        $admin->assignRole('admin');
        return $admin;
    }

    public function login(array $data)
    {
        // now need to check the username and password is true in Database
        $admin = Admin::query()->where('username', '=', $data['username'])->first();
        if (!$admin) {
            throw ValidationException::withMessages([
                'username' => ['Invalid credentials']
            ]);
        }

        if (!Hash::check($data['password'], $admin->password)) {
            throw ValidationException::withMessages([
                'password' => ['Invalid credentials']
            ]);
        }

        # check the redis
//        $cachedData = Cache::get("admin_token_{$admin->id}");
//        if ($cachedData) {
//            return [
//                'token' => $cachedData['token'],
//                'permissions' => $cachedData['permissions']
//            ];
//        }

//        $admin->tokens()->delete();

        Auth::guard('admin')->login($admin);
        request()->session()->regenerate(); # this is to forbid the Session Fixation Attack
        session([
            'device_hash' => hash('sha256', request()->userAgent()),
            'ip' => request()->ip(),
            'last_activity' => now(),
        ]);

        // if all checks is success (true) make a token by sanctom
//        $token = $admin->createToken('admin_token', expiresAt: \Illuminate\Support\now()->addDays(7))->plainTextToken;
        $permissions = $admin->getAllPermissions()->pluck('name');

//        Cache::put("admin_token_{$admin->id}", ['token' => $token, 'permissions' => $permissions], now()->addDays(7));
        return [
//            'token' => $token,
            'admin' => [
                'name' => $admin->name,
                'email' => $admin->email
            ],
            'permissions' => $permissions
        ];
    }

    public function logout()
    {
        $user = auth('admin_token')->user();

        if (!$user) {
            return $this->error('User not authenticated', 401);
        }

        // Delete the current access token directly
        $user->currentAccessToken()?->delete();
        Cache::forget('admin_token_' . $user->id);
        return true;
    }


    public function delete(Admin $admin)
    {
        # delete the token
        if ($admin->tokens()->exists()) {
            $admin->tokens()->delete();
        }
        return $admin->delete();
    }

    public function getAdminByEmail($email)
    {
        return Admin::query()->where('email', $email)->first();
    }

    public function sendEmailToStudent(Student $student, string $subject, string $message)
    {
        SendEmailToStudentJob::dispatch($student, $subject, $message)->onQueue('emails');
    }

    public function sendEmailsToAllStudents(string $subject, string $message)
    {
        Student::query()->where('is_active', true)->chunk(100, function ($students) use ($subject, $message) {
            foreach ($students as $student) {
                SendEmailToStudentJob::dispatch($student, $subject, $message)->onQueue('emails');
            }
        });
    }

    public function sendEmailToTeacher(Mentor $mentor, string $subject, string $message)
    {
        SendEmailToMentorJob::dispatch($mentor, $subject, $message)->onQueue('emails');
    }

    public function sendEmailsToAllTeachers(string $subject, string $message)
    {
        Mentor::query()->where('is_active', true)->chunk(100, function ($mentors) use ($subject, $message) {
            foreach ($mentors as $mentor) {
                SendEmailToMentorJob::dispatch($mentor, $subject, $message)->onQueue('emails');
            }
        });
    }
}

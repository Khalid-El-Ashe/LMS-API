<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminLoginRequest;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Admin;
use App\Models\Mentor;
use App\Models\Student;
use App\Repositories\Admin\AdminRepository;
use App\Traits\ApiResponseTrait;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class AdminController extends Controller
{

    public function __construct(private readonly AdminRepository $adminRepo)
    {
    }

    public function createAdmin(AdminRequest $request)
    {
        try {
            $this->adminRepo->createAdmin($request->validated());
            return $this->success(null, "Admin registered successfully", 201);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function loginAdmin(AdminLoginRequest $request)
    {
        try {
            $data = $this->adminRepo->login($request->validated());
            return $this->success(data: $data, code: ResponseAlias::HTTP_OK);
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function logoutAdmin()
    {
        try {
            $this->adminRepo->logout();
            return $this->success(null, "Admin logged out successfully");
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function deleteAdmin(Admin $admin)
    {
        try {
            $this->adminRepo->delete($admin);
            return $this->success(null, "Admin deleted successfully");
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function sendEmailToStudent(Student $student, $subject, $message)
    {
        try {
            $this->adminRepo->sendEmailToStudent($student, $subject, $message);
            return $this->success(null, "Email sent to student successfully");
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function sendEmailsToAllStudents($subject, $message)
    {
        try {
            $this->adminRepo->sendEmailsToAllStudents($subject, $message);
            return $this->success(null, "Emails sent to all students successfully");
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function sendEmailToMentor(Mentor $mentor, $subject, $message)
    {
        try {
            $this->adminRepo->sendEmailToTeacher($mentor, $subject, $message);
            return $this->success(null, "Email sent to mentor successfully");
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }

    public function sendEmailsToAllMentors($subject, $message)
    {
        try {
            $this->adminRepo->sendEmailsToAllTeachers($subject, $message);
            return $this->success(null, "Emails sent to all mentors successfully");
        } catch (Exception $e) {
            return $this->error($e->getMessage());
        }
    }
}

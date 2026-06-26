<?php

namespace App\Repositories\Admin;

use App\Http\Requests\AdminRequest;
use App\Http\Requests\LoginRequest;
use App\Models\Admin;
use App\Models\Mentor;
use App\Models\Student;

interface AdminRepository
{
    public function createAdmin(array $data);

    public function login(array $data);

    public function logout();

    public function delete(Admin $admin);

    public function getAdminByEmail($email);

    public function sendEmailToStudent(Student $student, string $subject, string $message);
    public function sendEmailsToAllStudents(string $subject, string $message);

    public function sendEmailToTeacher(Mentor $mentor, string $subject, string $message);
    public function sendEmailsToAllTeachers(string $subject, string $message);

}

<?php

namespace App\Repositories\Student;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\StudentRequest;
use App\Models\Mentor;
use App\Models\Student;
use Illuminate\Http\Request;

interface StudentRepository
{
    public function getAllStudents();
    public function getAllStudentsForMentor(Mentor $mentor);

    public function getAllStudentsIsTrashed();

    public function register(array $data);

    public function login(array $data);

    public function logout();

    public function getStudentProfile(); # Student $student
//    public function resetPassword(Student $student);
//    public function getStudentProfileForAdmin(Student $student);

    public function updateStudentData(Student $student, array $data);

    public function deleteStudent(Student $student);

    public function restoreStudent(Student $student);

    public function forceDeleteStudent(Student $student);

    public function enableStudent(Student $student);

    public function disableStudent(Student $student);

    public function searchStudents(string $searchTerm);

    public function filterStudents(Request $request);
    // public function refreshAllStudents();
    // public function refreshAllStudentsIsCourse();
    public function uploadProfileImage(Student $student, string $image): string;

    public function uploadMultipleFiles(Student $student, array $files);

    public function getStudentPath();
}

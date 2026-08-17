<?php

namespace App\Repositories\Mentor;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\MentorRequest;
use App\Http\Requests\ProfileImageRequest;
use App\Models\Mentor;
use App\Models\Student;

interface MentorRepository
{
    public function registerMentor(array $data);
    public function getAllMentors();

    public function loginMentor(array $data);

    public function logoutMentor();
    public function updateInformation(Mentor $mentor, array $data);
    public function mentorDashboard(Mentor $mentor);
    public function enableMentor(Mentor $mentor): bool;

    public function disableMentor(Mentor $mentor): bool;

    public function uploadProfileImage(Mentor $mentor, $image): string;

    public function uploadMultipleFiles(Mentor $mentor, array $files);
//    public function sendEmailForAllStudents();
//    public function sendEmailForStudent();
}

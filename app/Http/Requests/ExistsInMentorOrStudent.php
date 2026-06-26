<?php

namespace App\Http\Requests;

use App\Models\Mentor;
use App\Models\Student;
use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class ExistsInMentorOrStudent implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $existsInMentor = Mentor::query()->where('username', $value)->exists();
        $existsInStudent = Student::query()->where('username', $value)->exists();

        if (!$existsInMentor && !$existsInStudent) {
            $fail('The username does not exist in mentor or student records.');
        }
    }
}

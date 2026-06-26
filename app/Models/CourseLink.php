<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseLink extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'url',
        'description',
        'type',
        'platform',
        'expires_at',
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

}

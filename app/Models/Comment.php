<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    protected $fillable = [
        'user_id',
        'user_type',
//        'commentable_id',
//        'commentable_type',
        'course_video_id',
        'body'
    ];


//    public function commentable()
//    {
//        return $this->morphTo();
//    }


    public function user()
    {
        return $this->morphTo();
    }

    public function video()
    {
        return $this->belongsTo(CourseVideo::class, 'course_video_id');
    }

    // i need to make accessor function to know who is make a Comment
    public function getUserRoleAttribute()
    {
        return match ($this->user_type) {
            Student::class => 'student',
            Mentor::class => 'mentor',
            default => 'unknown'
        };
    }

    // now i need make function to access course through video
    public function course()
    {
        return $this->video()->course;
    }

    public function scopeByCourse($query, $courseId)
    {
        return $query->whereHas('video', function ($q) use ($courseId) {
            $q->where('course_id', $courseId);
        })->with(['user', 'video']);
    }
}

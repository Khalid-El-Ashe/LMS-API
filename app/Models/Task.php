<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use SoftDeletes;

    /**
     * @var \Illuminate\Support\HigherOrderCollectionProxy|mixed
     */
    protected $fillable = [
        'course_id',
        'video_id',
        'title',
        'description',
        'dead_line',
        'files',
//        'is_required',
//        'type',
//        'submission_type',
        'passing_grade',
        'order'
    ];


    protected $casts = [
        'dead_line' => 'datetime',
        'is_required' => 'boolean',
        'passing_grade' => 'integer',
        'files' => 'array',
        'order' => 'integer',
    ];

    public function video()
    {
        return $this->belongsTo(CourseVideo::class, 'video_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
//        return $this->hasOneThrough(Course::class,
//            CourseVideo::class,
//            'id',
//            'id',
//            'video_id',
//            'course_id'
//        );
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class);
    }

    # submission for student in the task
    public function submissionFor($studentId)
    {
        return $this->submissions()
            ->where('student_id', $studentId)->latest()->first();
    }

    # are the student is complete the task
    public function isCompletedByStudent(int $studentId): bool
    {
        return $this->submissions()
            ->where('student_id', $studentId)
//            ->where('status', 'approved')
            ->exists();
    }



//    public function comments()
//    {
//        return $this->morphMany(Comment::class, 'commentable');
//    }
}

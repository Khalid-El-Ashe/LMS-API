<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentVideoProgress extends Model
{
    protected $table = 'student_video_progress';

    protected $fillable = [
        'student_id',
        'video_id',
        'is_completed',
        'completed_at',
        'last_position',
        'watched_seconds',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
        'last_position' => 'integer',
        'watched_seconds' => 'integer',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function video()
    {
        return $this->belongsTo(CourseVideo::class, 'video_id');
    }

    public function completed($query)
    {
        return $query->where('is_completed', true);
    }

    public function isCompleted()
    {
        return $this->is_completed;
    }
}

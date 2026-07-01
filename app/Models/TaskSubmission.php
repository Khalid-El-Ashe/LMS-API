<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class TaskSubmission extends Model
{

    protected $fillable = [
        'task_id',
        'student_id',
        'file',
//        'status',
        'grade',
        'answer',
        'reviewed_at',
        'review_notes',
        'reviewed_by',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'grade' => 'integer',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function reviewer()
    {
        return $this->belongsTo(Mentor::class, 'reviewed_by');
    }

    # need make a helper to tell is have a review or not
    public function isReviewed()
    {
        return !is_null($this->reviewed_by);
    }

//    public function isReviewed()
//    {
//        return $this->status === 'approved';
//    }

//    public function isRejected()
//    {
//        return $this->status === 'rejected';
//    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;

class TaskSubmission extends Model
{

    protected $fillable = [
        'task_id',
        'student_id',
        'file',
//        'status',
        'grade',
//        'answer',
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

    /**
     * @return Attribute
     * this method is run automatically
     */
    protected function file(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value
                ? asset('storage/' . $value)
                : null
        );
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

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseVideo extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'title',
        'youtube_id',
        'description',
        'duration',
        'order',
    ];

    protected $appends = ['video_url', 'embed_url'];

    public function getVideoUrlAttribute()
    {
        return "https://www.youtube.com/watch?v=" . $this->youtube_id;
    }

    public function getEmbedUrlAttribute()
    {
        return "https://www.youtube.com/embed/" . $this->youtube_id;
    }

    protected $attributes = [
        'order' => 0,
    ];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function tasks()
    {
        return $this->hasMany(Task::class, 'video_id');
    }

//    public function comments()
//    {
//        return $this->morphMany(Comment::class, 'commentable');
//    }
    public function comments()
    {
        return $this->hasMany(Comment::class, 'course_video_id');
    }

    public function nextVideo()
    {
        return self::query()
            ->where('course_id', $this->course_id)
            ->where('order', '>', $this->order)
            ->orderBy('order')
            ->first();
    }

    public function previousVideo()
    {
        return self::query()->where('course_id', $this->course_id)
            ->where('order', '<', $this->order)
            ->orderByDesc('order')->first();
    }

    public function progress()
    {
        return $this->hasMany(StudentVideoProgress::class, 'video_id');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Course extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'logo',
        'slug',
        'youtube_playlist_url',
        'price',
        'rating'
    ];

    protected $casts = [
        'price' => 'float',
        'rating' => 'float',
    ];

    //todo now i need to build the Relations
    public function videos()
    {
        return $this->hasMany(CourseVideo::class);
    }

    public function mentors()
    {
        return $this->belongsToMany(Mentor::class, 'course_mentors')->withTimestamps();
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'course_students')
            ->withTimestamps();
    }

    public function links()
    {
        return $this->hasMany(CourseLink::class);
    }

    public function tasks()
    {
        return $this->hasManyThrough(Task::class, CourseVideo::class, 'course_id', 'video_id');
    }

    public function comments()
    {
        return $this->hasManyThrough(
            Comment::class,
            CourseVideo::class,
            'course_id',
            'course_video_id'
        );
    }

    public function scopeHasPlaylist(Builder $query)
    {
        return $query->whereNotNull($query);
    }
}

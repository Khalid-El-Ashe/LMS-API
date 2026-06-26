<?php

namespace App\Repositories\Course\Link;

use App\Models\Course;
use App\Models\CourseLink;

class LinkModelRepository implements LinkRepository
{

    public function create(array $data)
    {
        return CourseLink::query()->create($data);
    }

    public function getByCourse(Course $course)
    {
        return CourseLink::query()->where('id', $course->id)->latest()->first();
    }

    public function getExpiredLiveLinks()
    {
        return CourseLink::query()->where('type', 'live')
            ->where('platform', '=', 'google-meet')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<', now())
            ->latest()
            ->get();
    }

    public function update(int $id, array $data)
    {
        return CourseLink::query()->where('id', $id)->update($data);
    }

    public function deleteMany(array $links)
    {
        foreach ($links as $link) {
            $link->delete();
        }
    }
    public function delete(int $id)
    {
        $course = CourseLink::query()->findOrFail($id);
        return $course->delete();
    }
}

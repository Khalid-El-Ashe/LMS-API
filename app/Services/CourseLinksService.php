<?php

namespace App\Services;

use App\Models\Course;
use App\Repositories\Course\Link\LinkRepository;
use Carbon\Carbon;
use InvalidArgumentException;

class CourseLinksService
{
    protected $linkRepo;

    public function __construct(LinkRepository $linkRepo)
    {
        $this->linkRepo = $linkRepo;
    }

    public function createLink(int $courseId, array $data)
    {
        $data['course_id'] = $courseId;

        if ($data['type'] === 'live') {
            if (empty($data['platform'])) {
                throw new InvalidArgumentException('Platform is required for live links');
            }

            if (empty($data['expires_at'])) {
                $data['expires_at'] = Carbon::now()->addHours(12); // is a default 12H
            }
        } else {
            $data['expires_at'] = null;
        }

        $course = Course::query()->findOrFail($courseId);
        return $course->links()->create($data);
    }
}

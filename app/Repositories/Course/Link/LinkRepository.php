<?php

namespace App\Repositories\Course\Link;

use App\Models\Course;

interface LinkRepository
{
    public function create(array $data);
    public function getByCourse(Course $course);
    public function getExpiredLiveLinks();
    public function update(int $id, array $data);
    public function deleteMany(array $links);
    public function delete(int $id);
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LandingPage extends Model
{
    protected $table = 'landing_page';

    protected $fillable = [
        'graduates_count',
        'courses_count',
        'professional_trainer_count',
        'success_stories_count',
        'practical_projects_count',
        'graduate_program_video_url',
    ];
}

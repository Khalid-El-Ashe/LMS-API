<?php

namespace App\Http\Controllers;

use App\Repositories\LandingPage\LandingPageModelRepository;
use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function __construct(private readonly LandingPageModelRepository $landingRepo)
    {
    }
}

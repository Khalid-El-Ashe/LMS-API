<?php

namespace App\Console\Commands;

use App\Repositories\Course\Link\LinkRepository;
use Illuminate\Console\Command;


/**
 * This is a Time-based automation command that deletes expired live Google Meet course links from the database. It retrieves all expired links using the LinkRepository and deletes them, ensuring that only active and valid links remain in the system.
 */
class DeleteExpiredCourseLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'links:cleanup';
    protected $description = 'Delete Expired live Google Meet Courses Links';
    protected $repository;

    public function __construct(LinkRepository $repository) {
        parent::__construct();
        $this->repository = $repository;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $expiredLinks = $this->repository->getExpiredLiveLinks();

        if ($expiredLinks->isEmpty()) {
            $this->info('No expired links found.');
            return;
        }

        $this->repository->deleteMany($expiredLinks);
        $this->info('Expired links deleted successfully.');
    }
}

<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class YoutubeService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('app.youtube_api_key');
    }

    /**
     * Get all videos in a playlist
     */
    public function getPlaylistVideos(string $playlistId)
    {
        $videos = collect(); # Create a collection from the given value.
        $pageToken = null;

        try {
            do {
                $response = Http::retry(3, 100)->get('https://www.googleapis.com/youtube/v3/playlistItems', [
                    'part' => 'snippet,contentDetails',
                    'maxResults' => 50,
                    'playlistId' => $playlistId,
                    'pageToken' => $pageToken,
                    'key' => $this->apiKey,
                ]);

                // now need to check if the response is successful, if not, I need to log the error and return an empty collection
                if (!$response->successful()) {
                    throw new \Exception('Youtube API failed: ' . $response->body());
                }

                $data = $response->json();
                $videos = $videos->merge(
                    collect($data['items'] ?? [])->filter(fn($item) => $item['snippet']['title'] !== 'Deleted video')
                        ->map(function ($item) {
                            $videoId = $item['contentDetails']['videoId'];
                            return [
                                'youtube_id' => $videoId,
                                'video_url' => "https://www.youtube.com/watch?v={$videoId}",
                                'embed_url' => "https://www.youtube.com/embed/{$videoId}",
                                'title' => $item['snippet']['title'],
                                'description' => $item['snippet']['description'],
                                'thumbnail' => $item['snippet']['thumbnails']['high']['url'] ?? null,
                                'order' => $item['snippet']['position'] ?? 0,
                            ];
                        })
                );


                $pageToken = $data['nextPageToken'] ?? null;
            } while ($pageToken);
        } catch (\Throwable $th) {
            Log::error('Failed to fetch playlist videos from YouTube API: ' . $th->getMessage());
            return collect(); // Return an empty collection on failure
        }
        return $videos;
    }

    /**
     * Get video duration and additional details (duration + stats)
     */
    public function getVideoDetails(array $videoIds)
    {
        $results = collect(); # Create a collection from the given value.

        try {
            // now need to cut the videoIds into chunks of 50 because the YouTube API has a limit of 50 ids per request
            $chunks = array_chunk($videoIds, 50);
            foreach ($chunks as $chunk) {
                $response = Http::retry(3, 100)->get('https://www.googleapis.com/youtube/v3/videos', [
                    'part' => 'contentDetails,statistics', // ,snippet,statistics
                    'id' => implode(',', $chunk),
                    'key' => $this->apiKey,
                ]);

                if (!$response->successful()) {
                    Log::error('Youtube Video Details Error: ' . $response->body());
                    continue; // Skip this chunk and continue with the next one
                }

                foreach ($response->json()['items'] ?? [] as $item) {
                    $results->push([
                        'youtube_id' => $item['id'],
                        'duration' => $this->convertDurationToSeconds($item['contentDetails']['duration'] ?? null),
                        'view_count' => $item['statistics']['viewCount'] ?? 0,
//                        'like_count' => $item['statistics']['likeCount'] ?? 0,
//                        'dislike_count' => $item['statistics']['dislikeCount'] ?? 0,
                    ]);
                }
            }
        } catch (\Throwable $th) {
            Log::error('YouTube Video Details Error: ' . $th->getMessage());
        }

        return $results;
    }

    // now I need to convert ISO duration -> seconds
    private function convertDurationToSeconds(string $duration): float|int|null
    {
        if (!$duration) return null;

        try {
            $interval = new \DateInterval($duration);
            return ($interval->h * 3600) + ($interval->i * 60) + $interval->s;
        } catch (\Throwable $th) {
            return null; // Return null if the duration format is invalid
        }
    }
}

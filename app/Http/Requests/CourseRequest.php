<?php

namespace App\Http\Requests;

use App\Http\Requests\based\BaseApiRequest;

class CourseRequest extends BaseApiRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     */
    public function rules(): array
    {
        return [

            // =========================
            // Course Main Data
            // =========================
            'name' => ['required', 'string', 'max:255', 'unique:courses,name', 'unique:courses,name'],
            'description' => ['required', 'string'],
            //$this->isMethod('post') ? 'required' : 'nullable'
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,svg', 'max:2048'], // Logo is required for creation, optional for update
            'price' => ['sometimes', 'nullable', 'numeric', 'min:0'],
            'rating' => ['sometimes', 'nullable', 'numeric', 'min:0', 'max:5'],
            'status' => ['nullable', 'in:draft,published,archived'],
            'youtube_playlist_url' => ['required', 'url'],

            // =========================
            // Videos (Core Feature)
            // =========================
//            'course_videos' => ['nullable', 'array', 'min:1'],
//            'course_videos.*.title' => ['nullable', 'string', 'max:255'],
//            'course_videos.*.youtube_id' => ['nullable', 'string', 'max:255'],
//            'course_videos.*.description' => ['nullable', 'string'],
//            'course_videos.*.duration' => ['nullable', 'integer', 'min:1'],
//            'course_videos.*.order' => ['nullable', 'integer', 'min:0'],

            // =========================
            // Tasks (Core Feature)
            // =========================
            // 'tasks' => ['nullable', 'array'],
            // 'tasks.*.title' => ['required_with:tasks', 'string', 'max:255'],
            // 'tasks.*.description' => ['nullable', 'string'],
            // 'tasks.*.is_required' => ['nullable', 'boolean'],
            // 'tasks.*.type' => ['required_with:tasks', 'in:video,reading,quiz'],

            // =========================
            // Task Submissions (Core Feature)
            // =========================
            // 'task_submissions' => ['nullable', 'array'],
            // 'task_submissions.*.task_id' => ['required_with:task_submissions', 'integer', 'exists:tasks,id'],
            // 'task_submissions.*.student_id' => ['required_with:task_submissions', 'integer', 'exists:users,id'],
            // 'task_submissions.*.status' => ['required_with:task_submissions', 'in:pending,approved,rejected'],
            // 'task_submissions.*.grade' => ['nullable', 'numeric', 'min:0', 'max:100'],
            // 'task_submissions.*.answer' => ['nullable', 'string'],
            // 'task_submissions.*.file' => ['nullable', 'file', 'max:2048'],

            // =========================
            // Comments (Core Feature)
            // =========================
            // 'comments' => ['nullable', 'array'],
            // 'comments.*.user_id' => ['required_with:comments', 'integer', 'exists:users,id'],
            // 'comments.*.course_id' => ['required_with:comments', 'integer', 'exists:courses,id'],
            // 'comments.*.content' => ['required_with:comments', 'string'],

            // =========================
            // Links (Core Feature)
            // =========================
            'course_links' => ['nullable', 'array'],
            'course_links.*.title' => ['required_with:course_links', 'string', 'max:255'],
            'course_links.*.url' => ['required_with:course_links', 'url'],
            'course_links.*.type' => ['required_with:course_links', 'in:zoom,github,meeting'],
            'course_links.*.expires_at' => ['nullable', 'date']

        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Course name is required.',
            'name.string' => 'Course name must be a string.',
            'name.max' => 'Course name cannot exceed 255 characters.',
            'name.unique' => 'Course name must be unique.',

            'description.required' => 'Course description is required.',
            'description.string' => 'Course description must be a string.',

            'logo.required' => 'Course logo is required.',
            'logo.image' => 'Course logo must be an image.',
            'logo.mimes' => 'Course logo must be a file of type: jpeg, png, jpg, svg.',
            'logo.max' => 'Course logo cannot exceed 2048 kilobytes.',

            'price.numeric' => 'Course price must be a number.',
            'price.min' => 'Course price must be at least 0.',

            'status.in' => 'Course status must be one of: draft, published, archived.',

            // Videos
            'course_videos.required' => 'At least one course video is required.',
            'course_videos.array' => 'Course videos must be an array.',
            'course_videos.min' => 'At least one course video is required.',

            'course_videos.*.title.required' => 'Video title is required.',
            'course_videos.*.title.string' => 'Video title must be a string.',
            'course_videos.*.title.max' => 'Video title cannot exceed 255 characters.',

            'course_videos.*.youtube_id.required' => 'Video YouTube ID is required.',
            'course_videos.*.youtube_id.string' => 'Video YouTube ID must be a string.',

            'course_videos.*.description.string' => 'Video description must be a string.',

            'course_videos.*.duration.integer' => 'Video duration must be an integer.',
            'course_videos.*.duration.min' => 'Video duration must be at least 1 second.',

            'course_videos.*.order.integer' => 'Video order must be an integer.',
            'course_videos.*.order.min' => 'Video order cannot be negative.',

            // Links
            'course_links.*.title.required' => 'Link title is required.',
            'course_links.*.title.string' => 'Link title must be a string.',
            'course_links.*.title.max' => 'Link title cannot exceed 255 characters.',
            'course_links.*.url.required' => 'Link URL is required.',
            'course_links.*.url.url' => 'Link URL must be a valid URL.',
            'course_links.*.type.required' => 'Link type is required.',
            'course_links.*.type.in' => 'Link type must be one of: zoom, github, meeting.',
            'course_links.*.expires_at.date' => 'Link expiration date must be a valid date.',
        ];
    }
}

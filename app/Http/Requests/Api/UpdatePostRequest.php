<?php

namespace App\Http\Requests\Api;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // For public API, set to true. Add authentication logic if needed.
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'content' => ['sometimes', 'string'],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:posts,slug,' . $this->route('post')],
            'excerpt' => ['nullable', 'string', 'max:500'],
            'image' => ['nullable', 'string', 'max:255'],
            'status' => ['sometimes', Rule::in([Post::STATUS_DRAFT, Post::STATUS_PUBLISHED])],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'slug.unique' => 'This slug is already taken.',
            'status.in' => 'The status must be either draft or published.',
            'category_ids.*.exists' => 'One or more selected categories do not exist.',
        ];
    }
}

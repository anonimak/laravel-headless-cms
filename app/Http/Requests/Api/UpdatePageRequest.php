<?php

namespace App\Http\Requests\Api;

use App\Models\Page;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePageRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'slug' => ['sometimes', 'string', 'max:255', 'unique:pages,slug,' . $this->route('page')],
            'body' => ['sometimes', 'string'],
            'status' => ['sometimes', Rule::in([Page::STATUS_DRAFT, Page::STATUS_PUBLISHED])],
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
        ];
    }
}

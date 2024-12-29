<?php

namespace App\Http\Requests\User;

use App\Enums\Language;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProfileRequest extends FormRequest
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
            'name' => [
                'required_without_all:language',
            ],
            'language' => [
                'required_without_all:name',
                Rule::enum(Language::class),
            ]
        ];
    }

    public function attributes()
    {
        return [
            'name' => 'ユーザー名前',
            'language' => '言語設定',
        ];
    }
}

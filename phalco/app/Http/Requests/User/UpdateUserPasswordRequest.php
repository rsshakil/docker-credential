<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class UpdateUserPasswordRequest extends FormRequest
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
            'nowPassword' => [
                'required',
                'string', 
            ],
            'newPassword' => [
                'required',
                'string', 
                'different:nowPassword',
                'min:8',
                'confirmed',
            ],
            'newPassword_confirmation' => [
                'required',
                'string', 
                'same:newPassword',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'nowPassword' => '現在のパスワード',
            'newPassword' => '新しいパスワード',
            'newPassword_confirmation' => '確認パスワード',
        ];
    }
}

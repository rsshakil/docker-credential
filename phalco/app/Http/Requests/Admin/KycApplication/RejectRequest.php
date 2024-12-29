<?php

namespace App\Http\Requests\Admin\KycApplication;

use Illuminate\Foundation\Http\FormRequest;

class RejectRequest extends FormRequest
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
            'rejected_reason' => [
                'required',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'rejected_reason' => '却下理由',
        ];
    }
}

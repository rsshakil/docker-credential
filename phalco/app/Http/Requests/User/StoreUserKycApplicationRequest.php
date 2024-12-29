<?php

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserKycApplicationRequest extends FormRequest
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
            'kycImage' => [
                'required',
                'image',
            ],
            'kycPhoto' => [
                'required',
                'image',
            ],
        ];
    }

    public function attributes()
    {
        return [
            'kycImage' => '本人確認書類',
            'kycPhoto' => '本人の顔写真',
        ];
    }
}

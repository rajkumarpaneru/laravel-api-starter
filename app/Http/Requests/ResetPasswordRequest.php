<?php

namespace App\Http\Requests;

use App\Rules\TokenIsValid;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'token' => ['required', new TokenIsValid($this->email)],
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ];
    }
}

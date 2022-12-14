<?php

namespace App\Http\Requests;

use App\Rules\TokenIsValid;
use Illuminate\Foundation\Http\FormRequest;

class ResetPasswordRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'token' => ['required', new TokenIsValid($this->email)],
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
        ];
    }
}

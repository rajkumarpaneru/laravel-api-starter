<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TokenIsValid implements Rule
{
    private $email;

    public function __construct($email)
    {
        $this->email = $email;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param string $attribute
     * @param mixed $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        // check if there is a db-entry
        $reset = DB::table('password_resets')
            ->where('email', $this->email)->first();

        if (isset($reset)) {
            if (Hash::check($value, $reset->token))
                return true;
        }
        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return 'The token is invalid.';
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Requests\UpdateUserProfileRequest;
use App\Mail\PasswordResetMail;
use App\Mail\VerifyEmailMail;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends BaseController
{
    public function register(RegisterRequest $request): JsonResponse
    {
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        Mail::to($request->email)->send(new VerifyEmailMail());

        $response = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];

        return $this->response('User registered successfully.', 200, $response);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return $this->response('Invalid credentials.', 402);
        }

        if (!Hash::check($request->password, $user->password)) {
            return $this->response('Invalid credentials.', 402);
        }

        $token = $user->createToken('api');

        $response = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'token' => $token->plainTextToken,
        ];
        return $this->response('User logged in successfully.', 200, $response);
    }

    public function showUserProfile(Request $request): JsonResponse
    {
        $user = $request->user();

        $response = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];

        return $this->response('Profile retrieved successfully.', 200, $response);
    }

    public function updateUserProfile(UpdateUserProfileRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->update([
            'name' => $request->name,
        ]);

        $response = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];

        return $this->response('Profile updated successfully.', 200, $response);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $user = $request->user();

        $user->password = Hash::make($request->password);
        $user->save();

        $response = [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
        ];

        return $this->response('Password changed successfully.', 200, $response);
    }

    public function sendPasswordResetEmail(Request $request): JsonResponse
    {
        $token = Str::random();
        DB::table('password_resets')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now(),
        ]);

        Mail::to($request->email)->send(new PasswordResetMail($token, $request->email));

        return $this->response('Password reset link sent successfully.');
    }

    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->firstOrFail();
        $user->password = Hash::make($request->password);
        $user->save();

        DB::table('password_resets')->where('email', $request->email)->delete();

        return $this->response('Password is successfully reset.');
    }
}

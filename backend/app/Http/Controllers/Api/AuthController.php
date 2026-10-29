<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\ResetPasswordRequest;
use App\Http\Resources\UserResource;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        if (! Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) return response()->json(['message' => 'The provided credentials are incorrect.'], 422);
        $request->session()->regenerate();
        if (! $request->user()->roles()->exists()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            abort(403, 'This account is not authorized for administrator access.');
        }
        return new UserResource($request->user()->load('roles'));
    }

    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return response()->json(['data' => null, 'message' => 'Signed out.']);
    }

    public function me(Request $request) { return new UserResource($request->user()->load('roles')); }

    public function forgotPassword(ForgotPasswordRequest $request)
    {
        $status = Password::sendResetLink($request->only('email'));
        return response()->json(['message' => $status === Password::RESET_LINK_SENT ? 'If the account exists, a reset link has been sent.' : 'Unable to send a reset link.'], $status === Password::RESET_LINK_SENT ? 200 : 422);
    }

    public function resetPassword(ResetPasswordRequest $request)
    {
        $status = Password::reset($request->validated(), function ($user, $password) {
            $user->forceFill(['password' => $password, 'remember_token' => Str::random(60)])->save();
            event(new PasswordReset($user));
        });
        return response()->json(['message' => $status === Password::PASSWORD_RESET ? 'Password reset.' : 'Unable to reset the password.'], $status === Password::PASSWORD_RESET ? 200 : 422);
    }
}

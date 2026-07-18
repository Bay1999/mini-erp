<?php

namespace App\Services;

use App\Repositories\Contracts\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class AuthService
{
    protected UserRepositoryInterface $userRepository;

    public function __construct(UserRepositoryInterface $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Authenticate a user and regenerate their session.
     *
     * @param array $data
     * @return bool
     * @throws ValidationException
     */
    public function login(array $data): bool
    {
        $validator = Validator::make($data, [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $authCredentials = [
            'email' => $data['email'],
            'password' => $data['password'],
        ];

        if (!Auth::attempt($authCredentials)) {
            throw ValidationException::withMessages([
                'email' => ['Email atau password salah.'],
            ]);
        }

        request()->session()->regenerate();

        return true;
    }

    /**
     * Log the user out of the application and invalidate the session.
     */
    public function logout(): void
    {
        Auth::logout();

        // Invalidate and regenerate the session and CSRF token (security best practice)
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AuthService;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Validation\ValidationException;
use Exception;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    public function login(Request $request)
    {
        try {
            $this->authService->login($request->only(['email', 'password']));

            return new SuccessResource([
                'redirect' => route('dashboard')
            ], 'Login berhasil.');
        } catch (ValidationException $e) {
            return new ErrorResource($e->errors(), $e->getMessage(), 422);
        } catch (Exception $e) {
            return new ErrorResource(null, 'Terjadi kesalahan pada server.', 500);
        }
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        try {
            $this->authService->logout();
            return redirect()->route('login');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Gagal logout.');
        }
    }
}

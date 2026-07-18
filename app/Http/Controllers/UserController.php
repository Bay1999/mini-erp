<?php

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\Request;
use App\Http\Resources\SuccessResource;
use App\Http\Resources\ErrorResource;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    protected UserService $userService;

    public function __construct(UserService $userService) {
        $this->userService = $userService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data = $this->userService->index();
        return view("pages.user.user-index", $data);
    }

    public function data()
    {
        return $this->userService->indexDataTable();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = $this->userService->create();
        return view("pages.user.user-form", $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:6|confirmed',
                'role' => 'required|string|exists:roles,name',
            ]);

            $user = $this->userService->store($validated);

            return new SuccessResource($user, 'User created successfully.');
        } catch (ValidationException $e) {
            return new ErrorResource($e->errors(), $e->getMessage(), 422);
        } catch (Exception $e) {
            return new ErrorResource(null, 'A server error occurred.', 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $data = $this->userService->show((int)$id);
        if (!$data) {
            return redirect()->route('master.user.index')->with('error', 'User not found.');
        }
        return view("pages.user.user-show", $data);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $data = $this->userService->edit((int)$id);
        if (!$data) {
            return redirect()->route('master.user.index')->with('error', 'User not found.');
        }
        return view("pages.user.user-form", $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        try {
            $rules = [
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users,email,' . $id,
                'role' => 'required|string|exists:roles,name',
            ];

            $newPassword = $request->input('password');
            $confirmPassword = $request->input('password_confirmation');
            $changePassword = false;

            if (!empty($newPassword) || !empty($confirmPassword)) {
                $changePassword = true;
                $rules['current_password'] = 'required|string';
                $rules['password'] = 'required|string|min:6|confirmed';
            }

            $validated = $request->validate($rules);

            if ($changePassword) {
                $user = $this->userService->getUser((int)$id);
                if (!$user || !Hash::check($request->input('current_password'), $user->password)) {
                    throw ValidationException::withMessages([
                        'current_password' => ['The current password is incorrect.'],
                    ]);
                }
            } else {
                unset($validated['password']);
            }

            $this->userService->update((int)$id, $validated);

            return new SuccessResource(null, 'User updated successfully.');
        } catch (ValidationException $e) {
            return new ErrorResource($e->errors(), $e->getMessage(), 422);
        } catch (Exception $e) {
            return new ErrorResource(null, 'A server error occurred.', 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->userService->delete((int)$id);
            return new SuccessResource(null, 'User deleted successfully.');
        } catch (Exception $e) {
            return new ErrorResource(null, 'An error occurred while deleting the user.', 500);
        }
    }
}

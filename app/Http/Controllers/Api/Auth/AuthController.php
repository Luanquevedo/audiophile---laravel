<?php
namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * AuthController
 *
 * Handles authentication for SPA clients using Laravel Sanctum
 * in cookie/session mode (no API tokens).
 *
 * This controller is intentionally thin:
 * - Input validation is delegated to Form Requests
 * - Output formatting is delegated to Resources
 * - Authentication state is managed via session cookies
 */
class AuthController extends Controller
{
    /**
     * Registers a new user and immediately authenticates them.
     *
     * Uses session-based authentication to avoid exposing tokens
     * to the browser (SPA-friendly and safer against XSS).
     *
     * @group Auth
     * @unauthenticated
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated();

        $user = User::create([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
            'phone'    => $data['phone'] ?? null,
        ]);

        // Automatically authenticate the user after registration
        Auth::login($user);

        // Prevent session fixation attacks
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return response()->json([
            'message' => 'Usuário criado com sucesso.',
            'user'    => new UserResource($user),
        ], 201);
    }

    /**
     * Authenticates an existing user using session-based login.
     *
     * On success, regenerates the session to mitigate fixation attacks.
     *
     * @group Auth
     * @unauthenticated
     */
    public function login(LoginRequest $request)
    {
        $credentials = $request->only('email', 'password');

        if (! Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Credenciais inválidas.'],
            ]);
        }
        
        // Regenerate session ID after successful authentication
        if ($request->hasSession()) {
            $request->session()->regenerate();
        }

        return response()->json([
            'message' => 'Login realizado com sucesso.',
            'user'    => new UserResource($request->user()),
        ]);
    }

    /**
     * Returns the currently authenticated user.
     *
     * This endpoint is protected by the `auth:sanctum` middleware
     * and relies on the session cookie for authentication.
     *
     * @group Auth
     * @authenticated
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => new UserResource($request->user()),
        ]);
    }

    /**
     * Logs out the current user and invalidates the session.
     *
     * Also regenerates the CSRF token to ensure a clean logout state.
     *
     * @group Auth
     * @authenticated
     */
    public function logout(Request $request)
    {
        Auth::logout();

        if ($request->hasSession()) {
            // Fully invalidate the session
            $request->session()->invalidate();

            // Regenerate CSRF token after logout
            $request->session()->regenerateToken();
        }

        return response()->json([
            'message' => 'Logout realizado com sucesso.',
        ]);
    }
}

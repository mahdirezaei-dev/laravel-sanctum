<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Register User
     *
     * @param  [string] name
     * @param  [string] email
     * @param  [string] password
     * @param  [string] password_confirmation
     * @return [string] message
     */
    public function register(RegisterRequest $request): JsonResponse
    {
        if ($user = User::create($request->validated())) {
            return $this->respondWithToken(
                $this->generateToken($user)
            );
        } else {
            return response()->json([
                'success' => false,
                'message' => __('auth.failed'),
                'errors' => []
            ]);
        }
    }

    /**
     * Login User
     *
     * @param  [string] email
     * @param  [string] password
     * @param  [boolean] remember_me
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = request(['email', 'password']);

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => __('auth.failed')
            ], 401);
        }
        return $this->respondWithToken(
            $this->generateToken($request->user())
        );
    }

    /**
     * Get the authenticated User
     *
     * @return [json] user object
     */
    public function user(Request $request): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => new UserResource($request->user())
        ]);
    }

    /**
     * Logout user (Revoke the token)
     *
     * @return [string] message
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => __('auth.logged-out')
        ]);
    }

    protected function generateToken(User $user): mixed
    {
        return $user->createToken('Personal Access Token');
	}

    protected function respondWithToken(object $token, string $message = null): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => [
                'token' => $token->plainTextToken,
                'type' => 'Bearer',
                'expires_at' => $token->accessToken->expires_at
            ]
        ]);
    }
}

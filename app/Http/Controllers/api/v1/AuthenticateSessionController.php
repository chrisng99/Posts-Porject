<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class AuthenticateSessionController extends Controller
{
    use HttpResponses;

    public function store(LoginRequest $request): JsonResponse
    {
        try {
            $request->authenticate();
        } catch (ValidationException $e) {
            return $this->error([], $e->getMessage(), 400);
        } catch (Exception) {
            return $this->error([], 'An error has occurred. Please try again later.', 500);
        }

        $user = User::firstWhere('email', $request->validated('email'));

        $token = $user->is_admin
            ? $user->createToken('Sanctum API token of ' . $user->name, ['post:store', 'post:update', 'post:destroy', 'category:manage'])->plainTextToken
            : $user->createToken('Sanctum API token of ' . $user->name, ['post:store', 'post:update', 'post:destroy'])->plainTextToken;

        return $this->success([
            'user' => new UserResource($user),
            'token' => $token,
        ], 'You have succesfully logged in.');
    }

    public function destroy(): JsonResponse
    {
        try {
            auth()->user()->tokens()->delete();
        } catch (QueryException) {
            return $this->error([], 'The specified user could not be logged out. Please try again.', 400);
        } catch (Exception) {
            return $this->error([], 'An error has occurred. Please try again later.', 500);
        }

        return $this->success([], 'You have successfully been logged out and your token has been deleted.');
    }
}

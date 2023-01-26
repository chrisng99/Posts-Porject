<?php

namespace App\Http\Controllers\api\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Traits\HttpResponses;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;

class RegisterUserController extends Controller
{
    use HttpResponses;

    public function __invoke(RegisterUserRequest $request): JsonResponse
    {
        $request->validated($request->all());

        try {
            $user = User::create([
                'name' => $request->validated('name'),
                'email' => $request->validated('email'),
                'password' => bcrypt($request->validated('password')),
            ]);
        } catch (QueryException) {
            return $this->error([], 'This account could not be created. Try again later or with different account details.', 400);
        } catch (Exception) {
            return $this->error([], 'An error has occurred. Please try again later.', 500);
        }

        return $this->success(['user' => new UserResource($user)], 'User has succesfully been registered.', 201);
    }
}

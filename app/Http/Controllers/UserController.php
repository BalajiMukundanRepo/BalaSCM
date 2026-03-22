<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $company = $user->getCompany();

        if (! $company) {
            return $this->errorResponse('No company found', 404);
        }

        $query = User::whereHas('company_users', function ($q) use ($company) {
            $q->where('company_id', $company->id);
        });

        return $this->listResponse($query, new UserTransformer(), $request);
    }

    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
        ]);

        $user = User::create([
            'first_name' => $request->input('first_name', ''),
            'last_name' => $request->input('last_name', ''),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'phone' => $request->input('phone'),
            'ip' => $request->ip(),
        ]);

        return $this->itemResponse($user, new UserTransformer());
    }

    public function show(Request $request, User $user): JsonResponse
    {
        return $this->itemResponse($user, new UserTransformer());
    }

    public function update(Request $request, User $user): JsonResponse
    {
        $request->validate([
            'email' => 'sometimes|email|unique:users,email,'.$user->id,
        ]);

        $user->update($request->only([
            'first_name', 'last_name', 'email', 'phone',
            'custom_value1', 'custom_value2', 'custom_value3', 'custom_value4',
        ]));

        if ($request->has('password') && ! empty($request->input('password'))) {
            $user->password = Hash::make($request->input('password'));
            $user->save();
        }

        return $this->itemResponse($user->fresh(), new UserTransformer());
    }

    public function destroy(Request $request, User $user): JsonResponse
    {
        $user->is_deleted = true;
        $user->save();
        $user->delete();

        return response()->json(['message' => 'User deleted']);
    }
}

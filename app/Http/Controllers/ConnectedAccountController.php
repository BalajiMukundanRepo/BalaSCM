<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ConnectedAccountController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'provider' => 'required|string|in:google,microsoft',
            'oauth_user_id' => 'required|string',
        ]);

        $user = $request->user();
        $user->oauth_provider_id = $request->input('provider');
        $user->oauth_user_id = $request->input('oauth_user_id');
        $user->save();

        return response()->json([
            'data' => [
                'message' => 'Account connected successfully',
                'provider' => $user->oauth_provider_id,
            ],
        ]);
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->oauth_provider_id = null;
        $user->oauth_user_id = null;
        $user->save();

        return response()->json([
            'data' => [
                'message' => 'Account disconnected successfully',
            ],
        ]);
    }
}

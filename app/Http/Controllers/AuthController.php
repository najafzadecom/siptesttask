<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterRequest;
use App\Http\Requests\UpdateRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth', ['except' => ['signIn', 'signUp', 'register']]);
    }

    public function signIn(Request $request): JsonResponse
    {
        $credentials = $request->only(['username', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json([
                'success'   => false,
                'message'   => 'Username or password is incorrect'
            ], 401);
        }

        return response()->json([
            'access_token'  => auth()->user()->createToken("API TOKEN")->plainTextToken,
            'id'            => auth()->id(),
            'email'         => auth()->user()->email,
            'token_type'    => 'bearer',
            'expires_in'    => 600000
        ]);
    }

    public function signUp(RegisterRequest $request): JsonResponse
    {
        $input = $request->validated();

        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);

        return response()->json([
            'success'   => true,
            'data'      => $user
        ]);
    }

    public function info(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => auth()->user()
        ]);
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        $input = $request->validated();
        $user = User::where('id', auth()->id())->first();
        $user->update($input);

        return response()->json([
            'success'   => true,
            'data'      => $user
        ]);
    }

    public function latency(Request $request): JsonResponse
    {
        $startTime = microtime(true);
        exec('ping -c 1 ' . $request->get('pingHost'), $output, $status);
        if ($status != 0) {
            return response()->json(['success' => false, 'message' => 'Failed to ping host'], 500);
        }
        $endTime = microtime(true);
        $latency = round(($endTime - $startTime) * 1000, 2); // in milliseconds
        return response()->json(['success' => true, 'latency' => $latency]);
    }

    public function delete(Request $request): JsonResponse
    {
        auth()->logout();
        return response()->json(['success' => true, 'message' => 'Successfully logged out']);
    }
}

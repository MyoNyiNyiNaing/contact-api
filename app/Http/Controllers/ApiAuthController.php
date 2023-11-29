<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Services\Point\PointCreateService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use \Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Jenssegers\Agent\Agent;

class ApiAuthController extends Controller
{
    public function register(Request $request, PointCreateService $pointCreate): JsonResponse
    {
        $request->validate([
            "name" => "required|min:2|max:50",
            "email" => "required|email|unique:users,email",
            "password" => "required|min:8|confirmed",
        ]);

        $user = User::create([
            "name" => $request->name,
            "email" => $request->email,
            "password" => Hash::make($request->password)
        ]);

        $point = $pointCreate->create($user->id);


        return response()->json([
            "success" => true,
            "message" => "$user->name is register successful.",
            "points" => $point
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            "email" => "required|email",
            "password" => "required",
            "device_name" => "nullable|string"
        ]);
        $agent = new Agent();

        $device = $agent->device();
        if (Auth::attempt($credentials)) {
            $token = Auth::user()->createToken($device);
            return response()->json([
                "success" => true,
                "message" => "Login successfully",
                "user" => new UserResource(Auth::user()),
                "token" => $token->plainTextToken
            ]);
        }

        return response()->json([
            "success" => false,
            "message" => "Login Fail"
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            "success" => true,
            "message" => "logout successfully"
        ]);
    }

    public function logoutAll(Request $request)
    {

        $tokenId = $request->user()->currentAccessToken()->id;
        $request->user()->tokens()->where("id", "!=", $tokenId)->delete();
        return response()->json(["message" => "logout all successfully.", "success" => true]);
    }

    public function profile(): JsonResponse
    {

        return response()->json([
            "success" => true,
            "message" => "user information",
            "user" => new UserResource(Auth::user()),
        ]);
    }

    public function devices(): JsonResponse
    {

        return response()->json([
            "success" => true,
            "message" => "your devices",
            "user" => Auth::user()->tokens
        ]);
    }

    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            "current_password" => "required|current_password",
            "password" => "required|min:8|confirmed",
        ]);
        Auth::user()->update([
            "password" => Hash::make($request->password)
        ]);
        Auth::user()->tokens()->delete();
        return response()->json([
            "success" => true,
            "message" => "password changed successfully. Login again"
        ]);
    }
}

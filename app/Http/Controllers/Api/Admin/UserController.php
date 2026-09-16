<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(User::class, 'user');
    }

    public function index(): JsonResponse
    {
        return response()->json(['data' => UserResource::collection(User::orderBy('name')->paginate(15))]);
    }

    public function show(User $user): JsonResponse
    {
        return response()->json(['data' => new UserResource($user)]);
    }

    public function store(StoreUserRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['password'] = Hash::make($data['password']);

        $user = User::create($data);

        return response()->json(['data' => new UserResource($user)], 201);
    }

    public function update(StoreUserRequest $request, User $user): JsonResponse
    {
        $data = $request->validated();

        if (! empty($data['password'])) {
            $data['password'] = Hash::make($data['password']);
        } else {
            unset($data['password']);
        }

        $user->update($data);

        return response()->json(['data' => new UserResource($user)]);
    }

    public function destroy(User $user): JsonResponse
    {
        $hasRelationships = $user->mentoredRelationships()->exists() || $user->discipleRelationships()->exists();

        if ($hasRelationships) {
            return response()->json(['message' => '此使用者仍是某段門訓關係的成員，請先結束或轉移該關係後再刪除。'], 422);
        }

        $user->delete();

        return response()->json(['message' => '已刪除使用者。']);
    }
}

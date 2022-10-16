<?php

namespace App\Http\Controllers;

use App\Interfaces\ConfigServiceInterface;
use App\Interfaces\KeyServiceInterface;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function get(int $id): JsonResponse
    {
        $model = Client::findOrFail($id);
        return response()->json($model);
    }

    public function post(Request $request, KeyServiceInterface $keyService, ConfigServiceInterface $configService): JsonResponse
    {
        $randomIp = $request->get('random_ip', false);
        $model = Client::where('is_granted', 0);

        if ($randomIp) {
            $model->inRandomOrder();
        }

        $model = $model->firstOrFail();
        $keyPrivate = $keyService->private();
        $keyPublic = $keyService->public($keyPrivate);

        $model->update([
            'is_granted' => 1,
            'key_private' => $keyPrivate,
            'key_public' => $keyPublic,
        ]);

        $configService->peerAppend($model->ip4, $keyPublic);

        return response()->json($model);
    }

    public function put(): JsonResponse
    {
        return response()->json([
            'hello' => $id ?? 'world',
        ]);
    }

    public function delete(): JsonResponse
    {
        return response()->json([
            'hello' => $id ?? 'world',
        ]);
    }
}

<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreFcmTokenRequest;
use App\Services\FcmTokenService;
use Illuminate\Http\Request;

class FcmTokenController extends BaseController
{
    public function __construct(protected FcmTokenService $fcmTokenService) {}

    /**
     * Store a newly created FCM token in storage.
     */
    public function store(StoreFcmTokenRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = auth()->id();

        $fcmToken = $this->fcmTokenService->updateOrCreate($data);

        return $this->apiResponse();
    }

    /**
     * Remove the specified FCM token from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $this->fcmTokenService->deleteByToken($request->token);

        return $this->apiResponse();
    }
}

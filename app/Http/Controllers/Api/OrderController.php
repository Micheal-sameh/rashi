<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\OrderCreateRequest;
use App\Http\Resources\OrderResource;
use App\Services\OrderService;

class OrderController extends BaseController
{
    public function __construct(protected OrderService $orderService) {}

    public function index()
    {
        $orders = $this->orderService->index();

        return $this->respondResource(OrderResource::collection($orders));
    }

    public function create(OrderCreateRequest $request)
    {
        try {
            $order = $this->orderService->store($request->reward_id, $request->quantity);

            return $this->apiResponse(new OrderResource($order));
        } catch (\Exception $e) {
            return $this->respondInternalError($e->getMessage());
        }
    }

    public function myOrders()
    {
        $data = $this->orderService->myOrders();

        return $this->respondResource(OrderResource::collection($data['orders']),
            additional_data: [
                'total_points' => $data['total_points'],
                'count' => $data['count'],
            ]);
    }
}

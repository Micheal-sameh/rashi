<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Services\OrderService;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index()
    {
        $orders = $this->orderService->index();

        return view('orders.index', compact('orders'));
    }

    public function received($id)
    {
        $order = $this->orderService->received($id);

        return response()->json([
            'success' => true,
            'status' => OrderStatus::getStringValue($order->status),
            'servant_name' => $order->servant->name ?? 'N/A',
        ]);
    }
}

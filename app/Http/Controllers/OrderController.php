<?php

namespace App\Http\Controllers;

use App\Enums\OrderStatus;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request)
    {
        // dd($request);
        $orders = $this->orderService->index($request->user_id, $request->status);

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

<?php

namespace App\Http\Controllers;

use App\Rules\CheckCanDeleteOrderRule;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    public function __construct(protected OrderService $orderService) {}

    public function index(Request $request)
    {
        $orders = $this->orderService->index($request->user_id, $request->status, $request->membership_code);

        return view('orders.index', compact('orders'));
    }

    public function received($id)
    {
        $order = $this->orderService->received($id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => \App\Enums\OrderStatus::getStringValue($order->status),
                'servant_name' => $order->servant?->name ?? '',
                'message' => 'Order received successfully',
            ]);
        }

        return redirect()->back()->with('success', 'received successfuly');
    }

    public function cancel($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => [new CheckCanDeleteOrderRule],
        ]);

        if ($validator->fails()) {
            if (request()->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => $validator->errors()->first(),
                ], 422);
            }

            return redirect()->back()->withErrors($validator)->withInput();
        }

        $order = $this->orderService->cancel($id);

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'status' => \App\Enums\OrderStatus::getStringValue($order->status),
                'message' => 'Order cancelled successfully',
            ]);
        }

        return redirect()->back()->with('success', 'deleted Successfuly');
    }
}

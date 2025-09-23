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
        $orders = $this->orderService->index($request->user_id, $request->status);

        return view('orders.index', compact('orders'));
    }

    public function received($id)
    {
        $order = $this->orderService->received($id);

        return redirect()->back()->with('success', 'received successfuly');
    }

    public function cancel($id)
    {
        $validator = Validator::make(['id' => $id], [
            'id' => [new CheckCanDeleteOrderRule],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $order = $this->orderService->cancel($id);

        return redirect()->back()->with('success', 'deleted Successfuly');
    }
}

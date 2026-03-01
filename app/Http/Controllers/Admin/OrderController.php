<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PayWayService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    protected $payWayService;

    public function __construct(PayWayService $payWayService){
        $this->payWayService = $payWayService;
    }

    public function index()
    {
        $orders = Order::with('user')->latest()->get();
        $orders = session()->get('orders', $orders);
        if(empty($orders)){

        }
        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load('items.product', 'user');
        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|string|in:Pending,Processing,Completed,Cancelled'
        ]);

        $order->update($validated);
        return redirect()->route('admin.orders.show', $order)->with('success', 'Order status updated successfully.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }
}

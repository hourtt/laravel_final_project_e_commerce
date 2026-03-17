<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\PayWayService;
use Illuminate\Http\Request;
use \Carbon\Carbon;
class OrderController extends Controller
{
    protected $payWayService;

    public function __construct(PayWayService $payWayService){
        $this->payWayService = $payWayService;
    }

    public function index(Request $request)
    {
        // 1. Initialize the query
        $query = Order::with('user')->latest();

        // 2. Apply Date Filter if provided
        if ($request->filled('date')) {
            $selectedDate = Carbon::parse($request->date)->startOfDay();
            $today = Carbon::today();

            if ($selectedDate->gt($today)) {
                $error = 'Invalid Date: You cannot filter for future dates as no order data exists yet.';
                
                if ($request->ajax()) {
                    return response()->json(['error' => $error], 422);
                }
                
                return redirect()->back()->with('error', $error);
            }

            $query->whereDate('created_at', $selectedDate);
        }

        // 3. Fetch paginated orders
        $orders = $query->paginate(20);

        // Keep existing parameters in pagination links
        $orders->appends($request->all());

        // 4. Group the items of the current page
        $groupedOrders = $orders->getCollection()->groupBy(function ($order) {
            if ($order->created_at->isToday()) {
                return 'Today';
            } elseif ($order->created_at->isYesterday()) {
                return 'Yesterday';
            }
            return $order->created_at->format('d M Y');
        });

        // 5. Re-assign the grouped collection back to the paginator
        $orders->setCollection($groupedOrders);

        // 6. Handle AJAX Request
        if ($request->ajax()) {
            return view('admin.orders.table', compact('orders'))->render();
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
            'status' => 'required|string|in:Pending,Paid,Processing,Completed,Cancelled'
        ]);

        if (strtoupper($order->status) === strtoupper($request->status)) {
            return redirect()->back()->with('error', "Can't update! Nothing is change");
        }

        $order->update($validated);

        // Auto-delete the order if it was marked as Cancelled
        if (strtoupper($order->status) === 'CANCELLED') {
            $order->delete();
            return redirect()->route('admin.orders.index')
                ->with('success', 'Order was marked as Cancelled and automatically deleted.');
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order status updated successfully.');
    }

    public function destroy(Order $order)
    {
        // Protect completed/successful orders from being deleted (case-insensitive)
        if (!in_array(strtoupper($order->status), ['PENDING', 'CANCELLED', 'FAILED'])) {
            return redirect()->route('admin.orders.index')
                ->with('error', 'You can only delete Pending, Cancelled, or Failed orders.');
        }

        $order->delete();
        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully.');
    }
}

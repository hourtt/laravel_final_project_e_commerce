<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Http\Request;

class VoucherController extends Controller
{
    /**
     * List all vouchers.
     */
    public function index()
    {
        // ── Auto-deactivate exhausted vouchers ────────────────────────────────
        // Safety net: deactivate any voucher whose used_count has reached the
        // usage_limit but whose status is still marked active (can happen if the
        // payment webhook ran before the browser-redirect session was available).
        Voucher::where('status', true)
            ->whereNotNull('usage_limit')
            ->whereColumn('used_count', '>=', 'usage_limit')
            ->update(['status' => false]);

        $vouchers = Voucher::with('product')->latest()->paginate(15);
        return view('admin.vouchers.index', compact('vouchers'));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();
        $randomCode = Voucher::generateCode();
        return view('admin.vouchers.create', compact('products', 'randomCode'));
    }

    /**
     * Store a new voucher.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code'           => 'required|string|max:50|unique:vouchers,code',
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0.01',
            'product_id'     => 'nullable|exists:products,id',
            'usage_limit'    => 'nullable|integer|min:1',
            'expires_at'     => 'nullable|date|after:now',
            'status'         => 'boolean',
        ]);

        // If discount_type is percentage, cap at 100
        if ($validated['discount_type'] === 'percentage' && $validated['discount_value'] > 100) {
            return back()->withErrors(['discount_value' => 'Percentage discount cannot exceed 100%.'])->withInput();
        }

        $validated['status'] = $request->boolean('status', true);

        Voucher::create($validated);

        return redirect()->route('admin.vouchers.index')
            ->with('success', "Voucher code \"{$validated['code']}\" created successfully!");
    }

    /**
     * Show edit form.
     */
    public function edit(Voucher $voucher)
    {
        $products = Product::orderBy('name')->get();
        return view('admin.vouchers.edit', compact('voucher', 'products'));
    }

    /**
     * Update the voucher.
     */
    public function update(Request $request, Voucher $voucher)
    {
        $validated = $request->validate([
            'code'           => "required|string|max:50|unique:vouchers,code,{$voucher->id}",
            'discount_type'  => 'required|in:percentage,fixed',
            'discount_value' => 'required|numeric|min:0.01',
            'product_id'     => 'nullable|exists:products,id',
            'usage_limit'    => 'nullable|integer|min:1',
            'expires_at'     => 'nullable|date',
            'status'         => 'boolean',
        ]);

        if ($validated['discount_type'] === 'percentage' && $validated['discount_value'] > 100) {
            return back()->withErrors(['discount_value' => 'Percentage discount cannot exceed 100%.'])->withInput();
        }

        $validated['status'] = $request->boolean('status', true);

        // Prevent re-activating a voucher that has already hit its usage limit.
        // If the admin tries to set it active but used_count >= usage_limit, keep it inactive.
        $newLimit = $validated['usage_limit'] ?? null;
        if ($validated['status'] === true && $newLimit !== null && $voucher->used_count >= $newLimit) {
            $validated['status'] = false;
            return redirect()->route('admin.vouchers.index')
                ->with('warning', "Voucher \"{$voucher->code}\" cannot be re-activated: usage limit ({$newLimit}) has already been reached ({$voucher->used_count} uses).");
        }

        $voucher->update($validated);

        return redirect()->route('admin.vouchers.index')
            ->with('success', "Voucher code \"{$voucher->code}\" updated successfully!");
    }

    /**
     * Delete a voucher.
     */
    public function destroy(Voucher $voucher)
    {
        $code = $voucher->code;
        $voucher->delete();

        return redirect()->route('admin.vouchers.index')
            ->with('success', "Voucher \"{$code}\" deleted.");
    }

    /**
     * AJAX: generate a fresh random code.
     */
    public function generateCode()
    {
        return response()->json(['code' => Voucher::generateCode()]);
    }
}

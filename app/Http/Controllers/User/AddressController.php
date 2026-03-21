<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AddressController extends Controller
{
    /**
     * Display a listing of the user's addresses.
     */
    public function index(): View
    {
        $addresses = Auth::user()
            ->addresses()
            ->orderByDesc('is_default')
            ->get();

        return view('profile.addresses', compact('addresses'));
    }

    /**
     * Store a newly created address in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'address_type' => 'required|in:Home,Office',
            'is_default' => 'nullable|boolean',
        ]);

        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            $this->clearDefaultAddress();
        }

        Auth::user()->addresses()->create([
            ...$validated,
            'is_default' => $isDefault,
        ]);

        return redirect()->back()->with('success', 'Address saved successfully!');
    }

    /**
     * Update the specified address in storage.
     */
    public function update(Request $request, Address $address): RedirectResponse
    {
        $this->authorizeAddress($address);

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'street_address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'postal_code' => 'required|string|max:20',
            'country' => 'required|string|max:100',
            'address_type' => 'required|in:Home,Office',
            'is_default' => 'nullable|boolean',
        ]);

        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            $this->clearDefaultAddress();
        }

        $address->update([
            ...$validated,
            'is_default' => $isDefault,
        ]);

        return redirect()->back()->with('success', 'Address updated successfully!');
    }

    /**
     * Remove the specified address from storage.
     */
    public function destroy(Address $address): RedirectResponse
    {
        $this->authorizeAddress($address);

        $address->delete();

        return redirect()->back()->with('success', 'Address deleted successfully!');
    }

    /**
     * Set the specified address as the user's default.
     */
    public function setAsDefault(Address $address): RedirectResponse
    {
        $this->authorizeAddress($address);

        $this->clearDefaultAddress();

        $address->update(['is_default' => true]);

        return redirect()->back()->with('success', 'Default address updated!');
    }

    /**
     * Abort if the authenticated user does not own the given address.
     */
    private function authorizeAddress(Address $address): void
    {
        abort_if($address->user_id !== Auth::id(), 403);
    }

    /**
     * Unset the default flag on all of the user's current default addresses.
     */
    private function clearDefaultAddress(): void
    {
        Auth::user()
            ->addresses()
            ->where('is_default', true)
            ->update(['is_default' => false]);
    }
}
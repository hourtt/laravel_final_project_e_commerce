<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // Display the user's profile form.

    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Fetch dashboard stats for the presentation
        $totalOrders = $user->orders()->count();
        $totalSpent  = $user->orders()->where('status', 'Completed')->sum('total_price');
        $totalSaved  = $user->orders()->sum('voucher_discount') ?? 0;
        $addresses   = $user->addresses()->orderBy('is_default', 'desc')->get();

        return view('profile.edit', compact('user', 'totalOrders', 'totalSpent', 'totalSaved', 'addresses'));
    }

    // Update the user's profile information.
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return redirect()->route('profile.edit')->with('status', 'profile-updated');
    }

    // Update the user's profile image.
    public function updateImage(Request $request): RedirectResponse
    {
        $image = ['required', 'image', 'mimes:jpg,jpeg,png', 'max:2048'];

        $request->validate([
            'profile_image' => $image,
        ]);

        $user = $request->user();

        // Delete old image if it exists
        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
        }

        // Store new image
        $path = $request->file('profile_image')->store('profile-images', 'public');
        $user->update(['profile_image' => $path]);

        return redirect()->route('profile.edit')->with('status', 'profile-image-updated');
    }

    // Remove the user's profile image.
    public function destroyImage(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_image) {
            Storage::disk('public')->delete($user->profile_image);
            $user->update(['profile_image' => null]);
        }

        return redirect()->route('profile.edit')->with('status', 'profile-image-removed');
    }

    // Delete the user's account.
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}

<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Show the profile edit form.
     */
    public function edit(): View
    {
        $user = Auth::user();
        $timezones = config('signup.timezones');

        // Get company team members for mentions
        $company = $user->companies->first();
        $teamMembers = $company
            ? $company->users()
                ->where('users.id', '!=', $user->id)
                ->get(['users.id', 'users.first_name', 'users.last_name', 'users.email'])
                ->map(function($member) {
                    return [
                        'id' => $member->id,
                        'value' => $member->first_name . ' ' . $member->last_name,
                        'email' => $member->email,
                    ];
                })
                ->values()
            : collect();

        return view('profile.edit', compact('user', 'timezones', 'teamMembers'));
    }

    /**
     * Update the user's profile.
     */
    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $validated = $request->validated();

        // Handle profile image upload
        if ($request->hasFile('profile_image')) {
            $file = $request->file('profile_image');

            // Validate file extension manually
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            $extension = strtolower($file->getClientOriginalExtension());

            if (!in_array($extension, $allowedExtensions)) {
                return back()->withErrors([
                    'profile_image' => 'The image must be a file of type: jpeg, jpg, png, gif.'
                ])->withInput();
            }

            // Delete old profile image if exists (without using Storage facade)
            if ($user->profile_image) {
                $oldImagePath = storage_path('app/public/' . $user->profile_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
            }

            // Generate unique filename and store without MIME detection
            $filename = uniqid('profile_') . '.' . $extension;
            $directory = storage_path('app/public/profile-images');

            // Create directory if it doesn't exist
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Move the uploaded file
            $file->move($directory, $filename);

            $user->profile_image = 'profile-images/' . $filename;
        }

        // Handle profile image removal
        if ($request->has('remove_profile_image') && $request->remove_profile_image) {
            if ($user->profile_image) {
                $oldImagePath = storage_path('app/public/' . $user->profile_image);
                if (file_exists($oldImagePath)) {
                    unlink($oldImagePath);
                }
                $user->profile_image = null;
            }
        }

        // Update user information
        $user->first_name = $validated['first_name'];
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->timezone = $validated['timezone'];
        $user->about_yourself = $validated['about_yourself'];

        // Update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('profile.edit')
            ->with('message', 'Your profile has been updated successfully.');
    }
}

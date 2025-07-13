<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AddressController extends Controller
{
    /**
     * Store a newly created address in storage for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Ensure user is authenticated
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }
        
        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|max:20',
            'address_line' => 'required|string|max:500', // For full address text
            'is_default' => 'nullable|boolean',
        ]);

        try {
            // --- NEW: Geocoding the address ---
            $latitude = null;
            $longitude = null;
            $accessToken = config('services.mapbox.access_token');

            if ($accessToken) {
                try {
                    $response = Http::get("https://api.mapbox.com/geocoding/v5/mapbox.places/" . urlencode($validated['address_line']) . ".json", [
                        'access_token' => $accessToken,
                        'country' => 'VN',
                        'limit' => 1,
                    ]);

                    if ($response->successful() && !empty($response->json()['features'])) {
                        $coordinates = $response->json()['features'][0]['center'];
                        $longitude = $coordinates[0];
                        $latitude = $coordinates[1];
                    } else {
                         Log::warning('Geocoding failed for address: ' . $validated['address_line']);
                    }
                } catch (\Exception $e) {
                    Log::error('Mapbox API request failed: ' . $e->getMessage());
                }
            } else {
                Log::warning('Mapbox Access Token is not configured.');
            }
            // --- END NEW ---

            DB::beginTransaction();

            // If the new address is set as default, unset the current default one
            if ($request->boolean('is_default')) {
                $user->addresses()->update(['is_default' => false]);
            }

            // Create and save the new address
            $address = new Address();
            $address->user_id = $user->id;
            $address->full_name = $validated['full_name'];
            $address->phone_number = $validated['phone_number'];
            $address->address_line = $validated['address_line'];
            
            // Add coordinates
            $address->latitude = $latitude;
            $address->longitude = $longitude;
            
            // The new inline form does not provide structured city/district/ward.
            // This is a trade-off for a simpler UI.
            // Parsing from the address_line can be added later if needed.
            $address->is_default = $request->boolean('is_default');

            // If this is the user's first address, make it default regardless of the checkbox
            if ($user->addresses()->count() === 0) {
                $address->is_default = true;
            }

            $address->save();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Địa chỉ đã được lưu thành công!',
                'address' => $address
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
} 
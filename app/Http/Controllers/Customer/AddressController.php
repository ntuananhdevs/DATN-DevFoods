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
        
        try {
            $validated = $request->validate([
                'recipient_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'address_line' => 'required|string|max:500',
                'city' => 'nullable|string|max:255',
                'district' => 'nullable|string|max:255', 
                'ward' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'is_default' => 'nullable|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        }

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
            $address->recipient_name = $validated['recipient_name'];
            $address->phone_number = $validated['phone_number'];
            $address->address_line = $validated['address_line'];
            
            // Add coordinates from form or geocoding
            $address->latitude = $validated['latitude'] ?? $latitude;
            $address->longitude = $validated['longitude'] ?? $longitude;
            
            // Add city/district/ward if provided
            $address->city = $validated['city'] ?? null;
            $address->district = $validated['district'] ?? null;
            $address->ward = $validated['ward'] ?? null;
            
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

            // Prepare response data with all necessary fields
            $responseData = [
                'id' => $address->id,
                'recipient_name' => $address->recipient_name,
                'phone_number' => $address->phone_number,
                'address_line' => $address->address_line,
                'city' => $address->city,
                'district' => $address->district,
                'ward' => $address->ward,
                'latitude' => $address->latitude,
                'longitude' => $address->longitude,
                'is_default' => $address->is_default,
                'full_address' => $address->address_line . ', ' . $address->ward . ', ' . $address->district . ', ' . $address->city
            ];

            return response()->json([
                'success' => true,
                'message' => 'Địa chỉ đã được lưu thành công!',
                'data' => $responseData
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update an existing address for the authenticated user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();

        // Ensure user is authenticated
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 401);
        }

        // Find the address and ensure it belongs to the user
        $address = $user->addresses()->find($id);
        if (!$address) {
            return response()->json([
                'success' => false,
                'message' => 'Địa chỉ không tồn tại hoặc không thuộc về bạn'
            ], 404);
        }
        
        try {
            $validated = $request->validate([
                'recipient_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'address_line' => 'required|string|max:500',
                'city' => 'nullable|string|max:255',
                'district' => 'nullable|string|max:255', 
                'ward' => 'nullable|string|max:255',
                'latitude' => 'nullable|numeric',
                'longitude' => 'nullable|numeric',
                'is_default' => 'nullable|boolean',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Dữ liệu không hợp lệ',
                'errors' => $e->errors()
            ], 422);
        }

        try {
            // --- NEW: Geocoding the address if coordinates not provided ---
            $latitude = $validated['latitude'] ?? null;
            $longitude = $validated['longitude'] ?? null;
            
            // Only geocode if coordinates are not provided
            if (!$latitude || !$longitude) {
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
            }
            // --- END NEW ---

            DB::beginTransaction();

            // If the updated address is set as default, unset the current default one
            if ($request->boolean('is_default')) {
                $user->addresses()->where('id', '!=', $id)->update(['is_default' => false]);
            }

            // Update the address
            $address->recipient_name = $validated['recipient_name'];
            $address->phone_number = $validated['phone_number'];
            $address->address_line = $validated['address_line'];
            
            // Update coordinates from form or geocoding
            $address->latitude = $latitude;
            $address->longitude = $longitude;
            
            // Update city/district/ward if provided
            $address->city = $validated['city'] ?? $address->city;
            $address->district = $validated['district'] ?? $address->district;
            $address->ward = $validated['ward'] ?? $address->ward;
            
            $address->is_default = $request->boolean('is_default');

            $address->save();

            DB::commit();

            // Prepare response data with all necessary fields
            $responseData = [
                'id' => $address->id,
                'recipient_name' => $address->recipient_name,
                'phone_number' => $address->phone_number,
                'address_line' => $address->address_line,
                'city' => $address->city,
                'district' => $address->district,
                'ward' => $address->ward,
                'latitude' => $address->latitude,
                'longitude' => $address->longitude,
                'is_default' => $address->is_default,
                'full_address' => $address->address_line . ', ' . $address->ward . ', ' . $address->district . ', ' . $address->city
            ];

            return response()->json([
                'success' => true,
                'message' => 'Địa chỉ đã được cập nhật thành công!',
                'data' => $responseData
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Update address error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Đã có lỗi xảy ra: ' . $e->getMessage()
            ], 500);
        }
    }
}
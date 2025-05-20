<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverApplication;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class HiringController extends Controller
{
    /**
     * Display the driver hiring landing page
     */
    public function landing()
    {
        return view('customer.hiring.landing');
    }

    /**
     * Display the driver application form
     */
    public function applicationForm()
    {
        return view('customer.hiring.application');
    }

    /**
     * Process the driver application submission
     */
    public function submitApplication(Request $request)
    {
        // Validate form data
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:100',
            'email' => 'required|email|max:100|unique:driver_applications',
            'phone_number' => 'required|string|max:15|unique:driver_applications',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'id_card_number' => 'required|string|max:20|unique:driver_applications',
            'id_card_issue_date' => 'required|date',
            'id_card_issue_place' => 'required|string|max:100',
            'address' => 'required|string',
            'city' => 'required|string|max:50',
            'district' => 'required|string|max:50',
            'vehicle_type' => 'required|in:motorcycle,car,bicycle',
            'vehicle_model' => 'required|string|max:50',
            'vehicle_color' => 'required|string|max:50',
            'license_plate' => 'required|string|max:20|unique:driver_applications',
            'driver_license_number' => 'required|string|max:20|unique:driver_applications',
            'id_card_front_image' => 'required|image|max:2048',
            'id_card_back_image' => 'required|image|max:2048',
            'driver_license_image' => 'required|image|max:2048',
            'profile_image' => 'required|image|max:2048',
            'vehicle_registration_image' => 'required|image|max:2048',
            'bank_name' => 'required|string|max:100',
            'bank_account_number' => 'required|string|max:50',
            'bank_account_name' => 'required|string|max:100',
            'emergency_contact_name' => 'required|string|max:100',
            'emergency_contact_phone' => 'required|string|max:20',
            'emergency_contact_relationship' => 'required|string|max:50',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle file uploads
        $idCardFrontPath = $request->file('id_card_front_image')->store('driver-applications/id-cards', 'public');
        $idCardBackPath = $request->file('id_card_back_image')->store('driver-applications/id-cards', 'public');
        $driverLicensePath = $request->file('driver_license_image')->store('driver-applications/licenses', 'public');
        $profileImagePath = $request->file('profile_image')->store('driver-applications/profile', 'public');
        $vehicleRegistrationPath = $request->file('vehicle_registration_image')->store('driver-applications/vehicles', 'public');

        // Create driver application record
        $application = DriverApplication::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'date_of_birth' => $request->date_of_birth,
            'gender' => $request->gender,
            'id_card_number' => $request->id_card_number,
            'id_card_issue_date' => $request->id_card_issue_date,
            'id_card_issue_place' => $request->id_card_issue_place,
            'address' => $request->address,
            'city' => $request->city,
            'district' => $request->district,
            'vehicle_type' => $request->vehicle_type,
            'vehicle_model' => $request->vehicle_model,
            'vehicle_color' => $request->vehicle_color,
            'license_plate' => $request->license_plate,
            'driver_license_number' => $request->driver_license_number,
            'id_card_front_image' => $idCardFrontPath,
            'id_card_back_image' => $idCardBackPath,
            'driver_license_image' => $driverLicensePath,
            'profile_image' => $profileImagePath,
            'vehicle_registration_image' => $vehicleRegistrationPath,
            'bank_name' => $request->bank_name,
            'bank_account_number' => $request->bank_account_number,
            'bank_account_name' => $request->bank_account_name,
            'emergency_contact_name' => $request->emergency_contact_name,
            'emergency_contact_phone' => $request->emergency_contact_phone,
            'emergency_contact_relationship' => $request->emergency_contact_relationship,
            'status' => 'pending',
        ]);

        return redirect()->route('driver.application.success');
    }

    /**
     * Display success page after application submission
     */
    public function applicationSuccess()
    {
        return view('customer.hiring.success');
    }
}

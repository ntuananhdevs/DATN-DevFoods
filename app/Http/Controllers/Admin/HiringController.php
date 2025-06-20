<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DriverApplication;
use App\Models\DriverApplicationNotifiable;
use App\Notifications\DriverApplicationConfirmation;
use App\Rules\TurnstileRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

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
        // Log the request for debugging
        Log::info('Driver application submission started', [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'has_files' => $request->hasFile(['profile_image', 'id_card_front_image']),
            'turnstile_token' => $request->has('cf-turnstile-response') ? 'present' : 'missing'
        ]);

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
            'terms_accepted' => 'required|accepted',
            'cf-turnstile-response' => ['required', new TurnstileRule()],
        ], [
            'cf-turnstile-response.required' => 'Vui lòng hoàn thành xác minh bảo mật.',
            'terms_accepted.required' => 'Bạn phải đồng ý với điều khoản và điều kiện.',
            'terms_accepted.accepted' => 'Bạn phải đồng ý với điều khoản và điều kiện.',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Handle file uploads to S3
        try {
            $folderPath = $this->generateFolderName($request->full_name);
            
            $idCardFrontPath = $request->file('id_card_front_image')->store("{$folderPath}/identification", 'driver_documents');
            $idCardBackPath = $request->file('id_card_back_image')->store("{$folderPath}/identification", 'driver_documents');
            $driverLicensePath = $request->file('driver_license_image')->store("{$folderPath}/license", 'driver_documents');
            $profileImagePath = $request->file('profile_image')->store("{$folderPath}/profile", 'driver_documents');
            $vehicleRegistrationPath = $request->file('vehicle_registration_image')->store("{$folderPath}/vehicle", 'driver_documents');
        } catch (\Exception $e) {
            Log::error('File upload failed: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Có lỗi xảy ra khi tải lên tài liệu. Vui lòng thử lại.')
                ->withInput();
        }

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
            'address' => $request->address . ', ' . $request->district . ', ' . $request->city,
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

        // Send confirmation email to the applicant
        try {
            $applicant = new DriverApplicationNotifiable($application->email, $application->full_name);
            $applicant->notify(new DriverApplicationConfirmation($application));
            
            Log::info("Application confirmation email sent to applicant", [
                'application_id' => $application->id,
                'applicant_email' => $application->email,
                'applicant_name' => $application->full_name
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send application confirmation email: ' . $e->getMessage(), [
                'application_id' => $application->id,
                'applicant_email' => $application->email,
                'error' => $e->getMessage()
            ]);
            // Don't fail the application submission if confirmation email fails
        }

        // Set session flag for successful application submission
        session()->flash('application_submitted', true);
        session()->flash('application_data', [
            'id' => $application->id,
            'name' => $application->full_name,
            'email' => $application->email,
            'submitted_at' => $application->created_at->format('d/m/Y H:i')
        ]);

        return redirect()->route('driver.application.success');
    }

    /**
     * Display success page after application submission
     */
    public function applicationSuccess()
    {
        // Check if user came from successful application submission
        if (!session()->has('application_submitted')) {
            return redirect()->route('driver.application.form')
                ->with('error', 'Vui lòng điền và gửi đơn đăng ký trước khi truy cập trang này.');
        }

        // Get application data from session (optional, for display purposes)
        $applicationData = session('application_data');

        return view('customer.hiring.success', compact('applicationData'));
    }

    /**
     * Generate secure URL for S3 file
     */
    public function getDocumentUrl($path, $expiration = 60)
    {
        try {
            return Storage::disk('driver_documents')->temporaryUrl($path, now()->addMinutes($expiration));
        } catch (\Exception $e) {
            Log::error('Failed to generate document URL: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Generate sanitized folder name from driver name
     */
    private function generateFolderName($driverName)
    {
        // Remove special characters and convert to lowercase
        $sanitized = preg_replace('/[^a-zA-Z0-9\s]/', '', $driverName);
        $sanitized = str_replace(' ', '_', strtolower(trim($sanitized)));
        $timestamp = now()->format('Y-m-d_H-i-s');
        
        return "driver-info/application/{$sanitized}_{$timestamp}";
    }
}

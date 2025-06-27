<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\GeneralSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class GeneralSettingController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $settings = GeneralSetting::all();
        return view('admin.general_settings.index', compact('settings'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'key' => 'required|string|max:255|unique:general_setting,key',
            'value' => 'required|string|max:1000',
            'description' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $setting = GeneralSetting::create([
            'key' => $request->key,
            'value' => $request->value,
            'description' => $request->description
        ]);

        session()->flash('toast', [
            'type' => 'success',
            'title' => 'Thành công!',
            'message' => 'Đã thêm cài đặt mới thành công'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cài đặt đã được thêm thành công!',
            'setting' => $setting
        ]);
    }

    /**p
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $setting = GeneralSetting::findOrFail($id);
        
        // Prevent updating key for fixed settings (tax and free_shipping_threshold)
        $fixedKeys = ['tax_rate', 'free_shipping_threshold'];
        
        $rules = [
            'value' => 'required|string|max:1000',
            'description' => 'nullable|string|max:500'
        ];
        
        if (!in_array($setting->key, $fixedKeys)) {
            $rules['key'] = 'required|string|max:255|unique:general_setting,key,' . $id;
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = [
            'value' => $request->value,
            'description' => $request->description
        ];
        
        if (!in_array($setting->key, $fixedKeys)) {
            $updateData['key'] = $request->key;
        }

        $setting->update($updateData);

        session()->flash('toast', [
            'type' => 'success',
            'title' => 'Thành công!',
            'message' => 'Đã cập nhật cài đặt thành công'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cài đặt đã được cập nhật thành công!',
            'setting' => $setting
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $setting = GeneralSetting::findOrFail($id);
        
        // Prevent deleting fixed settings
        $fixedKeys = ['tax_rate', 'free_shipping_threshold'];
        
        if (in_array($setting->key, $fixedKeys)) {
            return response()->json([
                'success' => false,
                'message' => 'Không thể xóa cài đặt cố định này!'
            ], 403);
        }

        $setting->delete();

        session()->flash('toast', [
            'type' => 'success',
            'title' => 'Thành công!',
            'message' => 'Đã xóa cài đặt thành công'
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Cài đặt đã được xóa thành công!'
        ]);
    }
}
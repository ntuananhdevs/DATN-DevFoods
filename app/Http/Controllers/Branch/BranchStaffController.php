<?php

namespace App\Http\Controllers\Branch;

use App\Http\Controllers\Controller;

class BranchStaffController extends Controller
{
    public function index()
    {
        // Lấy danh sách nhân viên của chi nhánh, ví dụ:

        $staff = [];

        return view('branch.staff', compact('staff'));
    }
}

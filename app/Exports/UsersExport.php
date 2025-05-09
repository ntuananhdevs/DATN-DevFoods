<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class UsersExport implements FromCollection, WithHeadings, WithMapping
{
    protected $users;
    
    public function __construct($users = null)
    {
        $this->users = $users;
    }
    
    public function collection()
    {
        return $this->users ?: User::with('role')
            ->whereHas('role', function($query) {
                $query->where('name', 'customer');
            })->get();
    }
    
    public function headings(): array
    {
        return [
            'ID',
            'Tên đăng nhập',
            'Họ và tên',
            'Email',
            'Số điện thoại',
            'Vai trò',
            'Trạng thái',
            'Số dư',
            'Ngày tạo',
            'Ngày cập nhật'
        ];
    }
    
    public function map($user): array
    {
        return [
            $user->id,
            $user->user_name,
            $user->full_name,
            $user->email,
            $user->phone,
            $user->role->name,
            $user->active ? 'Kích hoạt' : 'Vô hiệu hóa',
            $user->balance,
            $user->created_at->format('d/m/Y H:i:s'),
            $user->updated_at->format('d/m/Y H:i:s'),
        ];
    }
}
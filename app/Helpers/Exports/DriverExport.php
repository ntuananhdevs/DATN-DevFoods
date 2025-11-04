<?php

namespace App\Exports;

use App\Models\Driver;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DriverExport implements FromCollection, WithHeadings, WithMapping
{
    protected $drivers;
    
    public function __construct($drivers = null)
    {
        $this->drivers = $drivers;
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->drivers ?: Driver::all();
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Họ và tên',
            'Email',
            'Số điện thoại',
            'Loại xe',
            'Màu xe',
            'Biển số xe',
            'Đánh giá',
            'Trạng thái',
            'Số dư',
            'Ngày tạo',
            'Ngày cập nhật'
        ];
    }
    
    /**
     * @param mixed $driver
     * @return array
     */
    public function map($driver): array
    {
        $status = '';
        switch($driver->status) {
            case 'active':
                $status = 'Đang hoạt động';
                break;
            case 'inactive':
                $status = 'Không hoạt động';
                break;
            case 'suspended':
                $status = 'Tạm khóa';
                break;
            default:
                $status = $driver->status;
        }
        
        return [
            $driver->id,
            $driver->full_name,
            $driver->email,
            $driver->phone_number,
            $driver->vehicle_type,
            $driver->vehicle_color,
            $driver->license_number,
            number_format($driver->rating, 1),
            $status,
            number_format($driver->balance, 0, ',', '.') . ' đ',
            $driver->created_at->format('d/m/Y H:i:s'),
            $driver->updated_at->format('d/m/Y H:i:s'),
        ];
    }
} 
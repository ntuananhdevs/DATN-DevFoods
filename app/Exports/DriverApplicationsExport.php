<?php

namespace App\Exports;

use App\Models\DriverApplication;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class DriverApplicationsExport implements FromCollection, WithHeadings, WithMapping
{
    protected $applications;
    
    public function __construct($applications = null)
    {
        $this->applications = $applications;
    }
    
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->applications ?: DriverApplication::all();
    }
    
    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Họ và tên',
            'Số điện thoại',
            'Email',
            'Biển số xe',
            'Loại phương tiện',
            'Trạng thái',
            'Ghi chú',
            'Ngày nộp đơn',
            'Ngày cập nhật'
        ];
    }
    
    /**
     * @param mixed $application
     * @return array
     */
    public function map($application): array
    {
        $status = '';
        switch($application->status) {
            case 'pending':
                $status = 'Đang chờ';
                break;
            case 'approved':
                $status = 'Đã duyệt';
                break;
            case 'rejected':
                $status = 'Đã từ chối';
                break;
            default:
                $status = $application->status;
        }
        
        return [
            $application->id,
            $application->full_name,
            $application->phone_number,
            $application->email,
            $application->license_plate,
            $application->vehicle_type,
            $status,
            $application->admin_notes,
            $application->created_at->format('d/m/Y H:i:s'),
            $application->updated_at->format('d/m/Y H:i:s'),
        ];
    }
} 
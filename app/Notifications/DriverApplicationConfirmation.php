<?php

namespace App\Notifications;

use App\Models\DriverApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DriverApplicationConfirmation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $application;

    public function __construct(DriverApplication $application)
    {
        $this->application = $application;
    }

    public function via($notifiable): array
    {
        return ['mail'];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Xác nhận đã nhận đơn ứng tuyển tài xế - DevFoods')
            ->greeting('Xin chào ' . $this->application->full_name . '!')
            ->line('Cảm ơn bạn đã gửi đơn ứng tuyển trở thành tài xế của DevFoods.')
            ->line('Chúng tôi đã nhận được đơn ứng tuyển của bạn và hiện đang trong quá trình xem xét.')
            ->line('')
            ->line('**Thông tin đơn ứng tuyển:**')
            ->line('- Mã đơn: #' . str_pad($this->application->id, 6, '0', STR_PAD_LEFT))
            ->line('- Ngày nộp: ' . $this->application->created_at->format('d/m/Y H:i'))
            ->line('- Loại phương tiện: ' . $this->getVehicleTypeText($this->application->vehicle_type))
            ->line('- Biển số xe: ' . $this->application->license_plate)
            ->line('')
            ->line('**Quy trình tiếp theo:**')
            ->line('1. Đội ngũ HR sẽ xem xét hồ sơ của bạn trong vòng 1-3 ngày làm việc')
            ->line('2. Nếu hồ sơ đạt yêu cầu, chúng tôi sẽ liên hệ với bạn để hướng dẫn các bước tiếp theo')
            ->line('3. Bạn sẽ nhận được email thông báo kết quả xét duyệt')
            ->line('')
            ->line('**Lưu ý quan trọng:**')
            ->line('- Vui lòng đảm bảo điện thoại luôn liên lạc được')
            ->line('- Kiểm tra email thường xuyên để không bỏ lỡ thông báo')
            ->line('- Nếu có thắc mắc, vui lòng liên hệ hotline: 1900-xxxx')
            ->line('')
            ->line('Một lần nữa, cảm ơn bạn đã quan tâm đến DevFoods. Chúng tôi sẽ liên hệ với bạn sớm nhất có thể!')
            ->salutation('Trân trọng,')
            ->salutation('Đội ngũ Tuyển dụng DevFoods');
    }

    /**
     * Get vehicle type in Vietnamese
     */
    private function getVehicleTypeText($type)
    {
        $types = [
            'motorcycle' => 'Xe máy',
            'car' => 'Ô tô',
            'bicycle' => 'Xe đạp'
        ];

        return $types[$type] ?? ucfirst($type);
    }

    public function toArray($notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'message' => 'Đơn ứng tuyển tài xế đã được gửi thành công'
        ];
    }
} 
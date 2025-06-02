@include('emails.components.header')

<div style="padding: 20px;">
    <h3>Thông báo kết quả ứng tuyển tài xế</h3>
    
    <p>Kính gửi Ứng viên {{ $data['driver']['full_name'] ?? $data['application']->full_name ?? 'Tài xế' }},</p>
    
    <p>Cảm ơn bạn đã quan tâm đến vị trí tài xế tại {{ config('app.name') }}.</p>
    
    <p>Sau khi xem xét hồ sơ của bạn, chúng tôi rất tiếc phải thông báo rằng hồ sơ của bạn chưa đáp ứng được yêu cầu của chúng tôi tại thời điểm này. Cụ thể:</p>
    
    <p style="margin: 15px 0;">
        @if(!empty($data['reason']))
            {{ $data['reason'] }}
        @else
            Hồ sơ của bạn chưa phù hợp với tiêu chí tuyển dụng hiện tại của chúng tôi.
        @endif
    </p>
    <p>Đây không phải là đánh giá về khả năng hay tiềm năng của bạn. Chúng tôi khuyến khích bạn cải thiện các yếu tố sau:</p>
    
    <p style="background-color: #f5f5f5; padding: 10px; margin: 15px 0; border-left: 4px solid #007bff;">
        Kinh nghiệm lái xe và giao hàng, hiểu biết về các khu vực giao hàng, kỹ năng giao tiếp với khách hàng.
    </p>
    
    <p>Chúng tôi hoan nghênh bạn ứng tuyển lại sau 3 tháng nếu bạn cảm thấy hồ sơ của mình đã được cải thiện.</p>
    
    <p>Chúc bạn may mắn trong quá trình tìm kiếm công việc!</p>
    
    <p>Trân trọng,<br>Đội ngũ tuyển dụng {{ config('app.name') }}</p>
</div>

@include('emails.components.footer')

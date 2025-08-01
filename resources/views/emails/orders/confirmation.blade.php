@extends('emails.layouts.app')

@section('content')
    <div style="padding:20px;max-width:600px;margin:0 auto;background:#ffffff;border-radius:8px;">
        <h2 style="color:#FF6B35;margin-top:0;">Thông báo đơn hàng mới</h2>
        <p>Xin chào {{ $data['order']->customer->name ?? $data['order']->guest_name }},</p>
        <p>Cảm ơn bạn đã đặt hàng tại <strong>{{ config('app.name') }}</strong>! Dưới đây là thông tin đơn hàng của bạn:</p>

        <table width="100%" cellpadding="6" cellspacing="0" style="border-collapse:collapse;border:1px solid #eee;font-size:14px;">
            <tr style="background:#f6f6f6;">
                <th align="left">Mã đơn</th>
                <td colspan="3">#{{ $data['order']->order_code }}</td>
            </tr>
            <tr style="background:#fafafa;">
                <th align="left">Phương thức thanh toán</th>
                <td colspan="3">
                    @php
                        $map = ['cod'=>'Thanh toán khi nhận hàng (COD)','vnpay'=>'VNPAY','balance'=>'Số dư tài khoản'];
                    @endphp
                    {{ $map[$data['order']->payment->payment_method] ?? $data['order']->payment->payment_method }}
                </td>
            </tr>
            <tr style="background:#f6f6f6;">
                <th align="left">Tổng thanh toán</th>
                <td colspan="3"><strong>{{ number_format($data['order']->total_amount,0,',','.') }}đ</strong></td>
            </tr>
            <tr>
                <th colspan="3" style="padding-top:10px;" align="left">Chi tiết món đã đặt</th>
            </tr>
            <tr style="background:#FF6B35;color:#fff;">
                <th align="left" style="padding:8px;">Món</th>
                <th align="center" style="padding:8px;">SL</th>
                <th align="right" style="padding:8px;">Thành tiền</th>
            </tr>
            @foreach($data['order']->orderItems as $item)
            @php
                // Xác định tên sản phẩm
                $itemName = 'Sản phẩm';
                $imageUrl = null;
                
                if($item->productVariant && $item->productVariant->product) {
                    $itemName = $item->productVariant->product->name;
                    if($item->productVariant->product->images->count()) {
                        $img = $item->productVariant->product->images->where('is_primary',true)->first() ?? $item->productVariant->product->images->first();
                        $imageUrl = $img->img ?? $img->url ?? null;
                    }
                } elseif($item->combo) {
                    $itemName = $item->combo->name;
                    $imageUrl = $item->combo->image ?? null;
                } elseif($item->product) {
                    $itemName = $item->product->name;
                    if($item->product->images->count()) {
                        $img = $item->product->images->where('is_primary',true)->first() ?? $item->product->images->first();
                        $imageUrl = $img->img ?? $img->url ?? null;
                    }
                }
                
                // Xử lý URL ảnh từ S3
                if($imageUrl && !str_starts_with($imageUrl, 'http')) {
                    $imageUrl = config('filesystems.disks.s3.url') . '/' . ltrim($imageUrl, '/');
                }
            @endphp
            <tr style="background:#ffffff;border-bottom:1px solid #eee;">
                <td style="padding:8px;vertical-align:middle;">
                    <div style="display:flex;align-items:center;gap:10px;">
                        @if($imageUrl)
                            <img src="{{ $imageUrl }}" alt="{{ $itemName }}" width="50" height="50" style="border-radius:6px;object-fit:cover;border:1px solid #ddd;">
                        @else
                            <div style="width:50px;height:50px;background:#f5f5f5;border-radius:6px;display:flex;align-items:center;justify-content:center;border:1px solid #ddd;">
                                <span style="color:#999;font-size:12px;">No img</span>
                            </div>
                        @endif
                        <span style="font-weight:500;color:#333;">{{ $itemName }}</span>
                    </div>
                </td>
                <td align="center" style="padding:8px;vertical-align:middle;">{{ $item->quantity }}</td>
                <td align="right" style="padding:8px;vertical-align:middle;font-weight:500;">{{ number_format($item->total_price,0,',','.') }}đ</td>
            </tr>
            @endforeach
        </table>

        <p style="margin-top:20px;">Bạn có thể theo dõi trạng thái đơn hàng của mình bất cứ lúc nào tại đường dẫn dưới đây:</p>
        <p><a href="{{ url('/track/' . $data['order']->order_code) }}" style="background:#FF6B35;color:#fff;padding:10px 20px;border-radius:6px;text-decoration:none;display:inline-block;">Theo dõi đơn hàng</a></p>
        <p>Nếu bạn có bất kỳ thắc mắc nào, xin vui lòng phản hồi email này hoặc liên hệ với bộ phận hỗ trợ khách hàng.</p>
        <p style="margin-bottom:0;">Trân trọng,<br>{{ config('app.name') }}</p>
    </div>
@endsection
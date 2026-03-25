<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Cập nhật trạng thái đơn hàng - {{ config('app.name') }}</title>
    <style>
        body {
            font-family: 'Georgia', serif;
            background-color: #f6f4f0;
            color: #333333;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            border: 1px solid #e0d8c8;
            border-top: 5px solid #b89768;
            padding: 40px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .header {
            text-align: center;
            border-bottom: 1px dashed #d1c7b1;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #2c2c2c;
            text-transform: uppercase;
            letter-spacing: 2px;
        }
        .title {
            font-size: 24px;
            color: #b89768;
            margin-top: 20px;
            font-style: italic;
        }
        .content {
            font-size: 16px;
            line-height: 1.6;
        }
        .details-box {
            background-color: #fbfaf8;
            border: 1px solid #e8e3d5;
            padding: 20px;
            margin: 25px 0;
            border-radius: 4px;
        }
        .details-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .details-list li {
            margin-bottom: 10px;
            border-bottom: 1px solid #f0ece0;
            padding-bottom: 10px;
        }
        .details-list li:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        .details-list strong {
            display: inline-block;
            width: 140px;
            color: #6d5b43;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 4px;
            font-weight: bold;
            color: #fff;
            background-color: #b89768; 
            font-size: 14px;
        }
        .button-wrapper {
            text-align: center;
            margin: 35px 0;
        }
        .button {
            background-color: #2c2c2c;
            color: #ffffff;
            text-decoration: none;
            padding: 12px 30px;
            font-family: 'Arial', sans-serif;
            font-size: 14px;
            font-weight: bold;
            letter-spacing: 1px;
            text-transform: uppercase;
            border-radius: 2px;
            display: inline-block;
            border: 1px solid #2c2c2c;
        }
        .footer {
            margin-top: 40px;
            text-align: center;
            font-size: 13px;
            color: #888888;
            border-top: 1px dashed #d1c7b1;
            padding-top: 20px;
            font-family: 'Arial', sans-serif;
        }
        .note {
            font-style: italic;
            color: #6d5b43;
            text-align: center;
            margin-top: 30px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">🛒 {{ config('app.name', 'Classic Cut') }}</div>
            <div class="title">Cập Nhật Đơn Hàng</div>
        </div>
        
        <div class="content">
            <p>Kính chào <strong>{{ $order->customer->name }}</strong>,</p>
            
            <p>Đơn hàng <strong>#{{ $order->order_code }}</strong> của bạn trên hệ thống <strong>{{ config('app.name', 'Classic Cut') }}</strong> vừa được cập nhật trạng thái mới.</p>
            
            <div style="text-align: center; margin: 20px 0;">
                <span style="font-size: 18px;">Trạng thái hiện tại:</span><br><br>
                <div class="status-badge">
                    {{ $order->status->label() }}
                </div>
            </div>
            
            <div class="details-box">
                <ul class="details-list">
                    <li><strong>Mã đơn hàng:</strong> {{ $order->order_code }}</li>
                    <li><strong>Ngày đặt:</strong> {{ $order->created_at->format('d/m/Y H:i') }}</li>
                    <li><strong>Tổng thanh toán:</strong> {{ number_format($order->total_amount, 0, ',', '.') }} VNĐ</li>
                    
                    @if($order->status === \App\Enums\OrderStatus::Cancelled && $order->cancel_reason)
                        <li><strong>Lý do hủy:</strong> <span style="color: #e3342f;">{{ $order->cancel_reason }}</span></li>
                    @endif
                </ul>
            </div>

            @if($order->status === \App\Enums\OrderStatus::Delivered)
                <p class="note">Cảm ơn bạn đã mua sắm tại cửa hàng của chúng tôi. Chúc bạn một ngày tốt lành!</p>
            @else
                <p class="note">Nếu bạn có bất kỳ thắc mắc nào, xin vui lòng liên hệ với chúng tôi qua số hotline hoặc email hỗ trợ.</p>
            @endif
            
            <div class="button-wrapper">
                <a href="{{ config('app.url') }}/client/profile?tab=orders" class="button">Xem Chi Tiết Đơn Hàng</a>
            </div>
            
            <p>Trân trọng,<br>Đội ngũ {{ config('app.name', 'Classic Cut') }}</p>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Classic Cut') }}. All rights reserved.
        </div>
    </div>
</body>
</html>

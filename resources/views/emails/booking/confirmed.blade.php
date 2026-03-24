<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Xác nhận lịch hẹn - {{ config('app.name') }}</title>
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
            width: 120px;
            color: #6d5b43;
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
            <div class="logo">✂️ {{ config('app.name', 'Classic Cut') }}</div>
            <div class="title">Lịch Hẹn Đã Được Xác Nhận</div>
        </div>
        
        <div class="content">
            <p>Kính chào <strong>{{ $booking->customer->name }}</strong>,</p>
            
            <p>Lịch hẹn cắt tóc của bạn tại <strong>{{ config('app.name', 'Classic Cut') }}</strong> đã được thợ cắt <strong>{{ $booking->barber->user->name }}</strong> xác nhận thành công!</p>
            
            <div class="details-box">
                <ul class="details-list">
                    <li><strong>Mã đặt lịch:</strong> {{ $booking->booking_code }}</li>
                    <li><strong>Ngày hẹn:</strong> {{ \Carbon\Carbon::parse($booking->booking_date)->format('d/m/Y') }}</li>
                    <li><strong>Giờ hẹn:</strong> {{ \Carbon\Carbon::parse($booking->start_time)->format('H:i') }}</li>
                    <li><strong>Dịch vụ:</strong> 
                        @php
                            $serviceNames = $booking->services->pluck('name')->implode(', ');
                        @endphp
                        {{ $serviceNames }}
                    </li>
                </ul>
            </div>

            <p class="note">Vui lòng đến đúng giờ để được phục vụ tốt nhất. Nếu bạn không thể đến, xin vui lòng huỷ lịch sớm nhất có thể trên hệ thống.</p>
            
            <div class="button-wrapper">
                <a href="{{ config('app.url') }}/client/profile" class="button">Xem Lịch Sử Đặt Lịch</a>
            </div>
            
            <p>Trân trọng,<br>Đội ngũ {{ config('app.name', 'Classic Cut') }}</p>
        </div>
        
        <div class="footer">
            &copy; {{ date('Y') }} {{ config('app.name', 'Classic Cut') }}. All rights reserved.
        </div>
    </div>
</body>
</html>

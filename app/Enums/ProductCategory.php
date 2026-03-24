<?php

namespace App\Enums;

enum ProductCategory: string
{
    case HairCare = 'hair_care';
    case Styling = 'styling';
    case Tools = 'tools';
    case Accessories = 'accessories';
    case Other = 'other';

    /**
     * Tên hiển thị tiếng Việt.
     */
    public function label(): string
    {
        return match ($this) {
            self::HairCare => 'Chăm sóc tóc',
            self::Styling => 'Tạo kiểu',
            self::Tools => 'Dụng cụ',
            self::Accessories => 'Phụ kiện',
            self::Other => 'Khác',
        };
    }
}

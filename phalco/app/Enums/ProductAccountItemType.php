<?php

namespace App\Enums;

enum ProductAccountItemType: int
{
    case TEXT = 1;
    case SELECT = 2;
    case CHECKBOX = 3;
    case RADIO = 4;
    case IMAGE = 5;

    public function text()
    {
        return match ($this) {
            self::TEXT => 'テキスト',
            self::SELECT => 'セレクトボックス',
            self::CHECKBOX => 'チェックボックス',
            self::RADIO => 'ラジオボタン',
            self::IMAGE => '画像',
        };
    }

    // オプション参照型
    public static function referenceType()
    {
        return [
            self::SELECT,
            self::CHECKBOX,
            self::RADIO,
        ];
    }
}

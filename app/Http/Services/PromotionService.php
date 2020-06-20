<?php

namespace App\Http\Services;

use App\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PromotionService
{
    const PROMOTION_TYPE_PRODUCT_COMBO = 1;
    const PROMOTION_TYPE_PRODUCT_COMBO_TEXT = 'Set Produk';

    const PROMOTION_TYPE_FREE_GIFT = 2;
    const PROMOTION_TYPE_FREE_GIFT_TEXT = 'Free Gift';

    const PROMOTION_TYPE_FREE_POSTAGE = 3;
    const PROMOTION_TYPE_FREE_POSTAGE_TEXT = 'Free Postage';

    const PROMOTION_TYPE_HAPPY_HOUR = 4;
    const PROMOTION_TYPE_HAPPY_HOUR_TEXT = 'Happy Hour';

    const PROMOTION_TYPE_VOUCHER = 5;
    const PROMOTION_TYPE_VOUCHER_TEXT = 'Voucher / Kod Kupon';

    public static function get_types()
    {
        return [
            self::PROMOTION_TYPE_PRODUCT_COMBO => self::PROMOTION_TYPE_PRODUCT_COMBO_TEXT,
            self::PROMOTION_TYPE_FREE_GIFT => self::PROMOTION_TYPE_FREE_GIFT_TEXT,
            self::PROMOTION_TYPE_FREE_POSTAGE => self::PROMOTION_TYPE_FREE_POSTAGE_TEXT,
            self::PROMOTION_TYPE_HAPPY_HOUR => self::PROMOTION_TYPE_HAPPY_HOUR_TEXT,
            self::PROMOTION_TYPE_VOUCHER => self::PROMOTION_TYPE_VOUCHER_TEXT
        ];
    }
}

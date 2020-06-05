<?php

namespace App\Http\Services;

class AddressService
{
    const STATE_JOHOR = 1;
    const STATE_JOHOR_TEXT = 'Johor';

    const STATE_KEDAH = 2;
    const STATE_KEDAH_TEXT = 'Kedah';

    const STATE_KELANTAN = 3;
    const STATE_KELANTAN_TEXT = 'Kelantan';

    const STATE_KUALA_LUMPUR = 4;
    const STATE_KUALA_LUMPUR_TEXT = 'Kuala Lumpur';

    const STATE_LABUAN = 5;
    const STATE_LABUAN_TEXT = 'Labuan';

    const STATE_MELAKA = 6;
    const STATE_MELAKA_TEXT = 'Melaka';

    const STATE_NEGERI_SEMBILAN = 7;
    const STATE_NEGERI_SEMBILAN_TEXT = 'Negeri Sembilan';

    const STATE_PAHANG = 8;
    const STATE_PAHANG_TEXT = 'Pahang';

    const STATE_PULAU_PINANG = 9;
    const STATE_PULAU_PINANG_TEXT = 'Pulau Pinang';

    const STATE_PERAK = 10;
    const STATE_PERAK_TEXT = 'Perak';

    const STATE_PERLIS = 11;
    const STATE_PERLIS_TEXT = 'Perlis';

    const STATE_PUTRAJAYA = 12;
    const STATE_PUTRAJAYA_TEXT = 'Putrajaya';

    const STATE_SELANGOR = 13;
    const STATE_SELANGOR_TEXT = 'Selangor';

    const STATE_SABAH = 14;
    const STATE_SABAH_TEXT = 'Sabah';

    const STATE_SARAWAK = 15;
    const STATE_SARAWAK_TEXT = 'Sarawak';

    const STATE_TERENGGANU = 16;
    const STATE_TERENGGANU_TEXT = 'Terengganu';

    static function states()
    {
        return [
            self::STATE_JOHOR => self::STATE_JOHOR_TEXT,
            self::STATE_KEDAH => self::STATE_KEDAH_TEXT,
            self::STATE_KELANTAN => self::STATE_KELANTAN_TEXT,
            self::STATE_KUALA_LUMPUR => self::STATE_KUALA_LUMPUR_TEXT,
            self::STATE_LABUAN => self::STATE_LABUAN_TEXT,
            self::STATE_MELAKA => self::STATE_MELAKA_TEXT,
            self::STATE_NEGERI_SEMBILAN => self::STATE_NEGERI_SEMBILAN_TEXT,
            self::STATE_PAHANG => self::STATE_PAHANG_TEXT,
            self::STATE_PULAU_PINANG => self::STATE_PULAU_PINANG_TEXT,
            self::STATE_PERAK => self::STATE_PERAK_TEXT,
            self::STATE_PERLIS => self::STATE_PERLIS_TEXT,
            self::STATE_PUTRAJAYA => self::STATE_PUTRAJAYA_TEXT,
            self::STATE_SELANGOR => self::STATE_SELANGOR_TEXT,
            self::STATE_SABAH => self::STATE_SABAH_TEXT,
            self::STATE_SARAWAK => self::STATE_SARAWAK_TEXT,
            self::STATE_TERENGGANU => self::STATE_TERENGGANU_TEXT
        ];
    }

    static function cities($state)
    {
        switch ($state) {
            case 1:
                return [
                    1 => 'Ayer Baloi',
                    2 => 'Ayer Hitam',
                    3 => ' Bakri',
                    4 => 'Batu Anam',
                    5 => 'Batu Pahat',
                    6 => 'Bekok',
                    7 => 'Benut',
                    8 => 'Bukit Gambir',
                    9 => ' Bukit Pasir',
                    10 => 'Chaah',
                    11 => 'Endau',
                    12 => 'Gelang Patah',
                    13 => 'Gerisek',
                    14 => 'Gugusan Taib Andak',
                    15 => 'Iskandar Puteri',
                    16 => 'Jementah',
                    17 => 'Johor Bahru',
                    18 => 'Kahang',
                    19 => 'Kampung Kenangan Tun Dr Ismail',
                    20 => 'Kluang',
                    21 => 'Kota Tinggi',
                    22 => 'Kukup',
                    23 => 'Kulai',
                    24 => 'Labis',
                    25 => 'Layang Layang',
                    26 => 'Masai',
                    27 => 'Mersing',
                    28 => 'Muar',
                    29 => 'Pagoh',
                    30 => 'Paloh',
                    31 => 'Panchor',
                    32 => 'Parit Jawa',
                    33 => 'Parit Raja',
                    34 => 'Parit Sulong',
                    35 => 'Pasir Gudang',
                    36 => 'Pekan Nanas',
                    37 => 'Pengerang',
                    38 => 'Permas Jaya',
                    39 => 'Plentong',
                    40 => 'Pontian',
                    41 => 'Rengam',
                    42 => 'Rengit',
                    43 => 'Segamat',
                    44 => 'Semerah',
                    45 => 'Senai',
                    46 => 'Senggarang',
                    47 => 'Senibong',
                    48 => 'Seri Gading',
                    49 => 'Setia Indah',
                    50 => 'Setia Tropika',
                    51 => 'Simpang Rengam',
                    52 => 'Skudai',
                    53 => 'Sungai Mati',
                    54 => 'Tampoi',
                    55 => 'Tangkak',
                    56 => 'Ulu Tiram',
                    57 => 'Yong Peng'
                ];
        }
    }
}

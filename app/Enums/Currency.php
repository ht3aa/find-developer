<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum Currency: string implements HasLabel
{
    case USD = 'USD';
    case IQD = 'IQD';
    case SAR = 'SAR';
    case AED = 'AED';
    case KWD = 'KWD';
    case BHD = 'BHD';
    case OMR = 'OMR';
    case QAR = 'QAR';
    case JOD = 'JOD';
    case EGP = 'EGP';
    case LBP = 'LBP';
    case IRR = 'IRR';
    case YER = 'YER';

    public function getLabel(): string
    {
        return match ($this) {
            self::USD => 'US Dollar (USD)',
            self::IQD => 'Iraqi Dinar (IQD)',
            self::SAR => 'Saudi Riyal (SAR)',
            self::AED => 'UAE Dirham (AED)',
            self::KWD => 'Kuwaiti Dinar (KWD)',
            self::BHD => 'Bahraini Dinar (BHD)',
            self::OMR => 'Omani Rial (OMR)',
            self::QAR => 'Qatari Riyal (QAR)',
            self::JOD => 'Jordanian Dinar (JOD)',
            self::EGP => 'Egyptian Pound (EGP)',
            self::LBP => 'Lebanese Pound (LBP)',
            self::IRR => 'Iranian Rial (IRR)',
            self::YER => 'Yemeni Rial (YER)',
        };
    }
}

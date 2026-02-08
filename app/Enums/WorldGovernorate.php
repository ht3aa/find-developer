<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum WorldGovernorate: string implements HasLabel
{
    // Iraqi Governorates
    case BAGHDAD = 'baghdad';
    case BASRA = 'basra';
    case NINEVEH = 'nineveh';
    case ERBIL = 'erbil';
    case NAJAF = 'najaf';
    case KARBALA = 'karbala';
    case BABYLON = 'babylon';
    case WASIT = 'wasit';
    case QADISIYYA = 'qadisiyya';
    case MUTHANNA = 'muthanna';
    case MESAN = 'mesan';
    case DHI_QAR = 'dhi_qar';
    case SALAH_AL_DIN = 'salah_al_din';
    case KIRKUK = 'kirkuk';
    case DIYALA = 'diyala';
    case ANBAR = 'anbar';
    case SULAYMANIYAH = 'sulaymaniyah';
    case DUHOK = 'duhok';

    // Yemen Governorates
    case YEMEN_ABYAN = 'yemen-abyan';
    case YEMEN_AD_DALI = 'yemen-ad_dali';
    case YEMEN_ADEN = 'yemen-aden';
    case YEMEN_AL_BAYDA = 'yemen-al_bayda';
    case YEMEN_AL_HUDAYDAH = 'yemen-al_hudaydah';
    case YEMEN_AL_JAWF = 'yemen-al_jawf';
    case YEMEN_AL_MAHRAH = 'yemen-al_mahrah';
    case YEMEN_AL_MAHWIT = 'yemen-al_mahwit';
    case YEMEN_AMANAT_AL_ASIMAH = 'yemen-amanat_al_asimah';
    case YEMEN_AMRAN = 'yemen-amran';
    case YEMEN_DHALE = 'yemen-dhale';
    case YEMEN_DHAMAR = 'yemen-dhamar';
    case YEMEN_HADRAMAUT = 'yemen-hadramaut';
    case YEMEN_HAJJAH = 'yemen-hajjah';
    case YEMEN_IBB = 'yemen-ibb';
    case YEMEN_LAHIJ = 'yemen-lahij';
    case YEMEN_MARIB = 'yemen-marib';
    case YEMEN_RAYMAH = 'yemen-raymah';
    case YEMEN_SAADA = 'yemen-saada';
    case YEMEN_SANAA = 'yemen-sanaa';
    case YEMEN_SOCOTRA = 'yemen-socotra';
    case YEMEN_TAIZ = 'yemen-taiz';

    public function getLabel(): string
    {
        return match ($this) {
            // Iraqi Governorates
            self::BAGHDAD => 'Baghdad (بغداد)',
            self::BASRA => 'Basra (البصرة)',
            self::NINEVEH => 'Nineveh (نينوى)',
            self::ERBIL => 'Erbil (إربيل)',
            self::NAJAF => 'Najaf (نجف)',
            self::KARBALA => 'Karbala (كربلاء)',
            self::BABYLON => 'Babylon (بابل)',
            self::WASIT => 'Wasit (واسط)',
            self::QADISIYYA => 'Al-Qadisiyyah (القادسية)',
            self::MUTHANNA => 'Al-Muthanna (المثنى)',
            self::MESAN => 'Maysan (ميسان)',
            self::DHI_QAR => 'Dhi Qar (ذي قار)',
            self::SALAH_AL_DIN => 'Salah al-Din (صلاح الدين)',
            self::KIRKUK => 'Kirkuk (كركوك)',
            self::DIYALA => 'Diyala (ديالى)',
            self::ANBAR => 'Anbar (الأنبار)',
            self::SULAYMANIYAH => 'Sulaymaniyah (السليمانية)',
            self::DUHOK => 'Duhok (دهوك)',

            // Yemen Governorates
            self::YEMEN_ABYAN => 'Abyan (أبين)',
            self::YEMEN_AD_DALI => 'Ad Dali\' (الضالع)',
            self::YEMEN_ADEN => 'Aden (عدن)',
            self::YEMEN_AL_BAYDA => 'Al Bayda\' (البيضاء)',
            self::YEMEN_AL_HUDAYDAH => 'Al Hudaydah (الحديدة)',
            self::YEMEN_AL_JAWF => 'Al Jawf (الجوف)',
            self::YEMEN_AL_MAHRAH => 'Al Mahrah (المهرة)',
            self::YEMEN_AL_MAHWIT => 'Al Mahwit (المحويت)',
            self::YEMEN_AMANAT_AL_ASIMAH => 'Amanat Al Asimah (أمانة العاصمة)',
            self::YEMEN_AMRAN => 'Amran (عمران)',
            self::YEMEN_DHALE => 'Dhale (الضالع)',
            self::YEMEN_DHAMAR => 'Dhamar (ذمار)',
            self::YEMEN_HADRAMAUT => 'Hadramaut (حضرموت)',
            self::YEMEN_HAJJAH => 'Hajjah (حجة)',
            self::YEMEN_IBB => 'Ibb (إب)',
            self::YEMEN_LAHIJ => 'Lahij (لحج)',
            self::YEMEN_MARIB => 'Marib (مأرب)',
            self::YEMEN_RAYMAH => 'Raymah (الريمة)',
            self::YEMEN_SAADA => 'Sa\'dah (صعدة)',
            self::YEMEN_SANAA => 'Sana\'a (صنعاء)',
            self::YEMEN_SOCOTRA => 'Socotra (سقطرى)',
            self::YEMEN_TAIZ => 'Taiz (تعز)',
        };
    }

    /**
     * Get all Iraqi governorates
     *
     * @return array<WorldGovernorate>
     */
    public static function getIraqLocations(): array
    {
        return [
            self::BAGHDAD,
            self::BASRA,
            self::NINEVEH,
            self::ERBIL,
            self::NAJAF,
            self::KARBALA,
            self::BABYLON,
            self::WASIT,
            self::QADISIYYA,
            self::MUTHANNA,
            self::MESAN,
            self::DHI_QAR,
            self::SALAH_AL_DIN,
            self::KIRKUK,
            self::DIYALA,
            self::ANBAR,
            self::SULAYMANIYAH,
            self::DUHOK,
        ];
    }
}

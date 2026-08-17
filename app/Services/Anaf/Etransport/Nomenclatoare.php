<?php

namespace App\Services\Anaf\Etransport;

/**
 * Nomenclatoarele fixe ale declarației e-Transport, luate din schema XSD v2
 * publicată de ANAF (resources/anaf/eTransport_v2.xsd).
 *
 * Sunt scrise aici, nu citite din XSD la fiecare cerere: schema le ține doar în
 * comentarii de documentație, iar interfața are nevoie de perechi cod-denumire.
 * La o schemă nouă se actualizează și listele acestea.
 */
class Nomenclatoare
{
    /** Tipurile de operațiune (codTipOperatiune). */
    public const TIPURI_OPERATIUNE = [
        10 => 'AIC — Achiziție intracomunitară',
        12 => 'LHI — Operațiuni în sistem lohn (UE) — intrare',
        14 => 'SCI — Stocuri la dispoziția clientului — intrare',
        20 => 'LIC — Livrare intracomunitară',
        22 => 'LHE — Operațiuni în sistem lohn (UE) — ieșire',
        24 => 'SCE — Stocuri la dispoziția clientului — ieșire',
        30 => 'TTN — Transport pe teritoriul național',
        40 => 'IMP — Import',
        50 => 'EXP — Export',
        60 => 'DIN — Tranzacție intracomunitară — intrare pentru depozitare',
        70 => 'DIE — Tranzacție intracomunitară — ieșire după depozitare',
    ];

    /** Scopurile posibile (codScopOperatiune), pe tip de operațiune. */
    public const SCOPURI = [
        101 => 'Comercializare',
        201 => 'Producție',
        301 => 'Gratuități',
        401 => 'Echipament comercial',
        501 => 'Mijloace fixe',
        601 => 'Consum propriu',
        703 => 'Operațiuni de livrare cu instalare',
        704 => 'Transfer între gestiuni',
        705 => 'Bunuri puse la dispoziția clientului',
        801 => 'Leasing financiar/operațional',
        802 => 'Bunuri în garanție',
        901 => 'Operațiuni scutite',
        1001 => 'Investiție în curs',
        1101 => 'Donații, ajutoare',
        9901 => 'Altele',
        9999 => 'Același cu operațiunea',
    ];

    /** Ce scopuri se pot alege la fiecare tip de operațiune. */
    public const SCOPURI_PE_OPERATIUNE = [
        10 => [101, 201, 301, 401, 501, 601, 703, 801, 802, 901, 1001, 1101, 9901],
        12 => [9999],
        14 => [9999],
        20 => [101, 301, 703, 801, 802, 9901],
        22 => [9999],
        24 => [9999],
        30 => [101, 704, 705, 9901],
        40 => [9999],
        50 => [9999],
        60 => [9999],
        70 => [9999],
    ];

    /**
     * De unde pleacă și unde ajunge traseul rutier, după tipul operațiunii:
     *   ptf         — punct de trecere a frontierei
     *   birou_vamal — birou vamal de interior/frontieră
     *   adresa      — județ + localitate + stradă
     */
    public const TRASEU_PE_OPERATIUNE = [
        10 => ['start' => 'ptf', 'final' => 'adresa'],
        12 => ['start' => 'ptf', 'final' => 'adresa'],
        14 => ['start' => 'ptf', 'final' => 'adresa'],
        20 => ['start' => 'adresa', 'final' => 'ptf'],
        22 => ['start' => 'adresa', 'final' => 'ptf'],
        24 => ['start' => 'adresa', 'final' => 'ptf'],
        30 => ['start' => 'adresa', 'final' => 'adresa'],
        40 => ['start' => 'birou_vamal', 'final' => 'adresa'],
        50 => ['start' => 'adresa', 'final' => 'birou_vamal'],
        60 => ['start' => 'ptf', 'final' => 'adresa'],
        70 => ['start' => 'adresa', 'final' => 'ptf'],
    ];

    /** Județele, cu codurile ANAF (codJudet). */
    public const JUDETE = [
        1 => 'Alba', 2 => 'Arad', 3 => 'Argeș', 4 => 'Bacău', 5 => 'Bihor',
        6 => 'Bistrița-Năsăud', 7 => 'Botoșani', 8 => 'Brașov', 9 => 'Brăila',
        10 => 'Buzău', 11 => 'Caraș-Severin', 12 => 'Cluj', 13 => 'Constanța',
        14 => 'Covasna', 15 => 'Dâmbovița', 16 => 'Dolj', 17 => 'Galați',
        18 => 'Gorj', 19 => 'Harghita', 20 => 'Hunedoara', 21 => 'Ialomița',
        22 => 'Iași', 23 => 'Ilfov', 24 => 'Maramureș', 25 => 'Mehedinți',
        26 => 'Mureș', 27 => 'Neamț', 28 => 'Olt', 29 => 'Prahova',
        30 => 'Satu Mare', 31 => 'Sălaj', 32 => 'Sibiu', 33 => 'Suceava',
        34 => 'Teleorman', 35 => 'Timiș', 36 => 'Tulcea', 37 => 'Vaslui',
        38 => 'Vâlcea', 39 => 'Vrancea', 40 => 'Municipiul București',
        51 => 'Călărași', 52 => 'Giurgiu',
    ];

    /** Punctele de trecere a frontierei (codPtf). */
    public const PTF = [
        1 => 'Petea (HU)', 2 => 'Borș (HU)', 3 => 'Vărșand (HU)', 4 => 'Nădlac (HU)',
        5 => 'Calafat (BG)', 6 => 'Bechet (BG)', 7 => 'Turnu Măgurele (BG)',
        8 => 'Zimnicea (BG)', 9 => 'Giurgiu (BG)', 10 => 'Ostrov (BG)',
        11 => 'Negru Vodă (BG)', 12 => 'Vama Veche (BG)', 13 => 'Călărași (BG)',
        14 => 'Corabia (BG)', 15 => 'Oltenița (BG)', 16 => 'Carei (HU)',
        17 => 'Cenad (HU)', 18 => 'Episcopia Bihor (HU)', 19 => 'Salonta (HU)',
        20 => 'Săcuieni (HU)', 21 => 'Turnu (HU)', 22 => 'Urziceni (HU)',
        23 => 'Valea lui Mihai (HU)', 24 => 'Vladimirescu (HU)',
        25 => 'Porțile de Fier 1 (RS)', 26 => 'Naidăș (RS)',
        27 => 'Stamora Moravița (RS)', 28 => 'Jimbolia (RS)', 29 => 'Halmeu (UA)',
        30 => 'Stânca Costești (MD)', 31 => 'Sculeni (MD)', 32 => 'Albița (MD)',
        33 => 'Oancea (MD)', 34 => 'Galați Giurgiulești (MD)',
        35 => 'Constanța Sud Agigea', 36 => 'Siret (UA)',
        37 => 'Nădlac 2 - A1 (HU)', 38 => 'Borș 2 - A3 (HU)',
    ];

    /** Birourile vamale de interior/frontieră (codBirouVamal). */
    public const BIROURI_VAMALE = [
        12801 => 'BVI Alba Iulia (ROBV0300)', 22801 => 'BVI Arad (ROTM0200)',
        22901 => 'BVF Arad Aeroport (ROTM0230)', 22902 => 'BVF Zona Liberă Curtici (ROTM2300)',
        32801 => 'BVI Pitești (ROCR7000)', 42801 => 'BVI Bacău (ROIS0600)',
        42901 => 'BVF Bacău Aeroport (ROIS0620)', 52801 => 'BVI Oradea (ROCJ6570)',
        52901 => 'BVF Oradea Aeroport (ROCJ6580)', 62801 => 'BVI Bistrița-Năsăud (ROCJ0400)',
        72801 => 'BVI Botoșani (ROIS1600)', 72901 => 'BVF Stânca Costești (ROIS1610)',
        72902 => 'BVF Rădăuți Prut (ROIS1620)', 82801 => 'BVI Brașov (ROBV0900)',
        92901 => 'BVF Zona Liberă Brăila (ROGL0710)', 92902 => 'BVF Brăila (ROGL0700)',
        102801 => 'BVI Buzău (ROGL1500)', 112801 => 'BVI Reșița (ROTM7600)',
        112901 => 'BVF Naidăș (ROTM6100)', 122801 => 'BVI Cluj Napoca (ROCJ1800)',
        122901 => 'BVF Cluj Napoca Aero (ROCJ1810)', 132901 => 'BVF Constanța Sud Agigea (ROCT1900)',
        132902 => 'BVF Mihail Kogălniceanu (ROCT5100)', 132903 => 'BVF Mangalia (ROCT5400)',
        132904 => 'BVF Constanța Port (ROCT1970)', 142801 => 'BVI Sfântu Gheorghe (ROBV7820)',
        152801 => 'BVI Târgoviște (ROBU8600)', 162801 => 'BVI Craiova (ROCR2100)',
        162901 => 'BVF Craiova Aeroport (ROCR2110)', 162902 => 'BVF Bechet (ROCR1720)',
        162903 => 'BVF Calafat (ROCR1700)', 172901 => 'BVF Zona Liberă Galați (ROGL3810)',
        172902 => 'BVF Giurgiulești (ROGL3850)', 172903 => 'BVF Oancea (ROGL3610)',
        172904 => 'BVF Galați (ROGL3800)', 182801 => 'BVI Târgu Jiu (ROCR8810)',
        192801 => 'BVI Miercurea Ciuc (ROBV5600)', 202801 => 'BVI Deva (ROTM8100)',
        212801 => 'BVI Slobozia (ROCT8220)', 222901 => 'BVF Iași Aero (ROIS4660)',
        222902 => 'BVF Sculeni (ROIS4990)', 222903 => 'BVF Iași (ROIS4650)',
        232801 => 'BVI Antrepozite/Ilfov (ROBU1200)', 232901 => 'BVF Otopeni Călători (ROBU1030)',
        242801 => 'BVI Baia Mare (ROCJ0500)', 242901 => 'BVF Aero Baia Mare (ROCJ0510)',
        242902 => 'BVF Sighet (ROCJ8000)', 252901 => 'BVF Orșova (ROCR7280)',
        252902 => 'BVF Porțile De Fier I (ROCR7270)', 252903 => 'BVF Porțile De Fier II (ROCR7200)',
        252904 => 'BVF Drobeta Turnu Severin (ROCR9000)', 262801 => 'BVI Târgu Mureș (ROBV8800)',
        262901 => 'BVF Târgu Mureș Aeroport (ROBV8820)', 272801 => 'BVI Piatra Neamț (ROIS7400)',
        282801 => 'BVI Corabia (ROCR2000)', 282802 => 'BVI Olt (ROCR8210)',
        292801 => 'BVI Ploiești (ROBU7100)', 302801 => 'BVI Satu-Mare (ROCJ7810)',
        302901 => 'BVF Halmeu (ROCJ4310)', 302902 => 'BVF Aeroport Satu Mare (ROCJ7830)',
        312801 => 'BVI Zalău (ROCJ9700)', 322801 => 'BVI Sibiu (ROBV7900)',
        322901 => 'BVF Sibiu Aeroport (ROBV7910)', 332801 => 'BVI Suceava (ROIS8230)',
        332901 => 'BVF Dornești (ROIS2700)', 332902 => 'BVF Siret (ROIS8200)',
        332903 => 'BVF Suceava Aero (ROIS8250)', 332904 => 'BVF Vicovu De Sus (ROIS9620)',
        342801 => 'BVI Alexandria (ROCR0310)', 342901 => 'BVF Turnu Măgurele (ROCR9100)',
        342902 => 'BVF Zimnicea (ROCR5800)', 352802 => 'BVI Timișoara Bază (ROTM8720)',
        352901 => 'BVF Jimbolia (ROTM5010)', 352902 => 'BVF Moravița (ROTM5510)',
        352903 => 'BVF Timișoara Aeroport (ROTM8730)', 362901 => 'BVF Sulina (ROCT8300)',
        362902 => 'BVF Aeroport Delta Dunării Tulcea (ROGL8910)', 362903 => 'BVF Tulcea (ROGL8900)',
        362904 => 'BVF Isaccea (ROGL8920)', 372801 => 'BVI Vaslui (ROIS9610)',
        372901 => 'BVF Fălciu', 372902 => 'BVF Albița (ROIS0100)',
        382801 => 'BVI Râmnicu Vâlcea (ROCR7700)', 392801 => 'BVI Focșani (ROGL3600)',
        402801 => 'BVI București Poștă (ROBU1380)', 402802 => 'BVI Târguri și Expoziții (ROBU1400)',
        402901 => 'BVF Băneasa (ROBU1040)', 512801 => 'BVI Călărași (ROCT1710)',
        522801 => 'BVI Giurgiu (ROBU3910)', 522901 => 'BVF Zona Liberă Giurgiu (ROBU3980)',
    ];

    /** Tipurile documentelor de transport (tipDocument). */
    public const TIPURI_DOCUMENT = [
        10 => 'CMR',
        20 => 'Factură',
        30 => 'Aviz de însoțire a mărfii',
        9999 => 'Altele',
    ];

    /** Unitățile de măsură uzuale (UN/ECE Rec. 20); H87 e cea din aplicația ANAF. */
    public const UNITATI_MASURA = [
        'H87' => 'Bucată',
        'KGM' => 'Kilogram',
        'GRM' => 'Gram',
        'TNE' => 'Tonă',
        'LTR' => 'Litru',
        'MTR' => 'Metru',
        'MTK' => 'Metru pătrat',
        'MTQ' => 'Metru cub',
        'NPR' => 'Pereche',
        'SET' => 'Set',
        'XPK' => 'Pachet',
        'XBX' => 'Cutie',
        'XPX' => 'Palet',
    ];

    /** Țările UE din care se poate alege partenerul la operațiuni intracomunitare. */
    public const TARI_UE = [
        'AT' => 'Austria', 'BE' => 'Belgia', 'BG' => 'Bulgaria', 'CY' => 'Cipru',
        'CZ' => 'Cehia', 'DE' => 'Germania', 'DK' => 'Danemarca', 'EE' => 'Estonia',
        'EL' => 'Grecia', 'ES' => 'Spania', 'FI' => 'Finlanda', 'FR' => 'Franța',
        'HR' => 'Croația', 'HU' => 'Ungaria', 'IE' => 'Irlanda', 'IT' => 'Italia',
        'LT' => 'Lituania', 'LU' => 'Luxemburg', 'LV' => 'Letonia', 'MT' => 'Malta',
        'NL' => 'Țările de Jos', 'PL' => 'Polonia', 'PT' => 'Portugalia',
        'RO' => 'România', 'SE' => 'Suedia', 'SI' => 'Slovenia', 'SK' => 'Slovacia',
        'XI' => 'Irlanda de Nord',
    ];

    /** Câteva țări din afara UE întâlnite des la import/export și transport. */
    public const TARI_NON_UE = [
        'AL' => 'Albania', 'BA' => 'Bosnia și Herțegovina', 'BY' => 'Belarus',
        'CH' => 'Elveția', 'CN' => 'China', 'GB' => 'Regatul Unit', 'IL' => 'Israel',
        'IN' => 'India', 'MD' => 'Moldova', 'ME' => 'Muntenegru',
        'MK' => 'Macedonia de Nord', 'NO' => 'Norvegia', 'RS' => 'Serbia',
        'RU' => 'Rusia', 'TR' => 'Turcia', 'UA' => 'Ucraina', 'US' => 'S.U.A.',
    ];

    /** Toate țările de ales în interfață, cele UE întâi. */
    public static function tari(): array
    {
        return self::TARI_UE + self::TARI_NON_UE;
    }
}

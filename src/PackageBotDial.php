<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot;


class PackageBotDial
{
    /** @var array */
    public static $supportedCountryCodes = [
        self::COUNTRY_CODE_CZ,
        self::COUNTRY_CODE_DE,
        self::COUNTRY_CODE_GB,
        self::COUNTRY_CODE_SK,
        self::COUNTRY_CODE_AT,
        self::COUNTRY_CODE_PL,
        self::COUNTRY_CODE_CH,
        self::COUNTRY_CODE_FI,
        self::COUNTRY_CODE_HU,
        self::COUNTRY_CODE_SI,
        self::COUNTRY_CODE_LV,
        self::COUNTRY_CODE_EE,
        self::COUNTRY_CODE_LT,
        self::COUNTRY_CODE_BE,
        self::COUNTRY_CODE_DK,
        self::COUNTRY_CODE_ES,
        self::COUNTRY_CODE_FR,
        self::COUNTRY_CODE_IE,
        self::COUNTRY_CODE_IT,
        self::COUNTRY_CODE_NL,
        self::COUNTRY_CODE_NO,
        self::COUNTRY_CODE_PT,
        self::COUNTRY_CODE_SE,
        self::COUNTRY_CODE_RO,
        self::COUNTRY_CODE_BG,
        self::COUNTRY_CODE_GR,
        self::COUNTRY_CODE_HR,
        self::COUNTRY_CODE_TR,
        self::COUNTRY_CODE_LU
    ];

    /** @var array  */
    public static $supportedCurrencyCodes = [
        self::CURRENCY_CODE_CZK,
        self::CURRENCY_CODE_EUR,
        self::CURRENCY_CODE_PLN
    ];

    /** @var array  */
    public static $countryCodeHaveCurrencyCode = [
        self::COUNTRY_CODE_CZ => self::CURRENCY_CODE_CZK,
        self::COUNTRY_CODE_SK => self::CURRENCY_CODE_EUR,
        self::COUNTRY_CODE_PL => self::CURRENCY_CODE_PLN,
    ];

    // Country codes
    const COUNTRY_CODE_CZ = 'cz';
    const COUNTRY_CODE_DE = 'de';
    const COUNTRY_CODE_GB = 'gb';
    const COUNTRY_CODE_SK = 'sk';
    const COUNTRY_CODE_AT = 'at';
    const COUNTRY_CODE_PL = 'pl';
    const COUNTRY_CODE_CH = 'ch';
    const COUNTRY_CODE_FI = 'fi';
    const COUNTRY_CODE_HU = 'hu';
    const COUNTRY_CODE_SI = 'si';
    const COUNTRY_CODE_LV = 'lv';
    const COUNTRY_CODE_EE = 'ee';
    const COUNTRY_CODE_LT = 'lt';
    const COUNTRY_CODE_BE = 'be';
    const COUNTRY_CODE_DK = 'dk';
    const COUNTRY_CODE_ES = 'es';
    const COUNTRY_CODE_FR = 'fr';
    const COUNTRY_CODE_IE = 'ie';
    const COUNTRY_CODE_IT = 'it';
    const COUNTRY_CODE_NL = 'nl';
    const COUNTRY_CODE_NO = 'no';
    const COUNTRY_CODE_PT = 'pt';
    const COUNTRY_CODE_SE = 'se';
    const COUNTRY_CODE_RO = 'ro';
    const COUNTRY_CODE_BG = 'bg';
    const COUNTRY_CODE_GR = 'gr';
    const COUNTRY_CODE_HR = 'hr';
    const COUNTRY_CODE_TR = 'tr';
    const COUNTRY_CODE_LU = 'lu';

    // Currency codes
    const CURRENCY_CODE_CZK = 'CZK';
    const CURRENCY_CODE_EUR = 'EUR';
    const CURRENCY_CODE_PLN = 'PLN';
}
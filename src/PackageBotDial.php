<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot;


use Salamek\PackageBot\Enum\Country;
use Salamek\PackageBot\Enum\Currency;

class PackageBotDial
{
    /** @var array */
    public static $supportedCountryCodes = [
        Country::CZ,
        Country::DE,
        Country::GB,
        Country::SK,
        Country::AT,
        Country::PL,
        Country::CH,
        Country::FI,
        Country::HU,
        Country::SI,
        Country::LV,
        Country::EE,
        Country::LT,
        Country::BE,
        Country::DK,
        Country::ES,
        Country::FR,
        Country::IE,
        Country::IT,
        Country::NL,
        Country::NO,
        Country::PT,
        Country::SE,
        Country::RO,
        Country::BG,
        Country::GR,
        Country::HR,
        Country::TR,
        Country::LU
    ];

    /** @var array  */
    public static $supportedCurrencyCodes = [
        Currency::CZK,
        Currency::EUR,
        Currency::PLN
    ];

    /** @var array  */
    public static $countryCodeHaveCurrencyCode = [
        Country::CZ => Currency::CZK,
        Country::SK => Currency::EUR,
        Country::PL => Currency::PLN,
    ];
}
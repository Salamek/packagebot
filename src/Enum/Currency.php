<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot\Enum;


class Currency
{
    const CZK = 'CZK';
    const EUR = 'EUR';
    const PLN = 'PLN';

    /** @var array */
    public static $list = [
        self::CZK,
        self::EUR,
        self::PLN
    ];
}
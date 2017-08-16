<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot\Enum;


class TransportService
{
    const CZECH_POST_PACKAGE_TO_HAND = 1;
    const CZECH_POST_PACKAGE_TO_THE_POST_OFFICE = 2;
    const PPL_PARCEL_CZ_PRIVATE = 3;
    const PPL_PARCEL_CZ_PRIVATE_COD = 4;
    const ZASILKOVNA = 5;

    /** @var array */
    public static $list = [
        self::CZECH_POST_PACKAGE_TO_HAND,
        self::CZECH_POST_PACKAGE_TO_THE_POST_OFFICE,
        self::PPL_PARCEL_CZ_PRIVATE,
        self::PPL_PARCEL_CZ_PRIVATE_COD,
        self::ZASILKOVNA
    ];
}
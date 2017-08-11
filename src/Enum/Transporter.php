<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot\Enum;


class Transporter
{
    const CZECH_POST = 'czechPost';
    const PPL = 'professionalParcelLogistic';
    const ULOZENKA = 'ulozenka';
    const ZASILKOVNA = 'zasilkovna';

    /** @var array */
    public static $list = [
        self::CZECH_POST,
        self::PPL,
        self::ULOZENKA,
        self::ZASILKOVNA
    ];
}
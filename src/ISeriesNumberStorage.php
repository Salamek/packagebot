<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot;

use Salamek\PackageBot\Exception\NumberSeriesWastedException;
use Salamek\PackageBot\Model\SeriesNumberInfo;

/**
 * Interface IPackageBotSeriesNumberStorage
 * @package Salamek\PackageBot
 */
interface ISeriesNumberStorage
{
    /**
     * @param string $transporter
     * @param null|integer $transportService
     * @param null|string $sender
     * @throws NumberSeriesWastedException
     * @return SeriesNumberInfo
     */
    public function getNextSeriesNumberId($transporter, $transportService = null, $sender = null);
}
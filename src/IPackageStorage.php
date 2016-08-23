<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot;

use Salamek\PackageBot\Model\Package;

/**
 * Interface IPackageBotStorage
 * @package Salamek\PackageBot
 */
interface IPackageStorage
{
    /**
     * @param $transporter
     * @param integer $orderId
     * @param integer $seriesNumberId
     * @param string $packageNumber
     * @param Package $packageData
     * @param \DateTimeInterface|null $send
     * @return mixed
     */
    public function savePackage($transporter, $orderId, $seriesNumberId, $packageNumber, Package $packageData, \DateTimeInterface $send = null);

    /**
     * @param string $transporter
     * @return mixed
     */
    public function getUnSentPackages($transporter);

    /**
     * @param string $transporter
     * @param integer $orderId
     * @return mixed
     */
    public function getPackageByOrderId($transporter, $orderId);

    /**
     * @param string $transporter
     * @param integer $seriesNumberId
     * @return mixed
     */
    public function getPackageBySeriesNumberId($transporter, $seriesNumberId);

    /**
     * @param string $transporter
     * @param string $packageNumber
     * @return mixed
     */
    public function getPackageByPackageNumber($transporter, $packageNumber);

    /**
     * @param $transporter
     * @param Package[] $packages
     * @param \DateTimeInterface $date
     * @return mixed
     */
    public function setSendPackages($transporter, array $packages, \DateTimeInterface $date);
}
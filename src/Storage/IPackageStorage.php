<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot\Storage;

use Salamek\PackageBot\Model\Package;
use Salamek\PackageBot\Model\SendPackageResult;
use Salamek\PackageBot\Model\SeriesNumberInfo;

/**
 * Interface IPackageBotStorage
 * @package Salamek\PackageBot
 */
interface IPackageStorage
{
    /**
     * @param $transporter
     * @param string $packageNumber
     * @param Package $packageData
     * @param \DateTimeInterface|null $send
     * @return mixed
     */
    public function savePackage($transporter, $packageNumber, Package $packageData, \DateTimeInterface $send = null);

    /**
     * @param string $transporter
     * @return mixed
     */
    public function getUnSentPackages($transporter);

    /**
     * @param string $transporter
     * @param integer $orderId
     * @return array
     */
    public function getPackagesByOrderId($transporter, $orderId);

    /**
     * @param string $transporter
     * @param SeriesNumberInfo $seriesNumberInfo
     * @return mixed
     */
    public function getPackageBySeriesNumberInfo($transporter, SeriesNumberInfo $seriesNumberInfo);

    /**
     * @param string $transporter
     * @param string $packageNumber
     * @return mixed
     */
    public function getPackageByPackageNumber($transporter, $packageNumber);

    /**
     * @param $transporter
     * @param SendPackageResult[] $sendPackagesResults
     * @param \DateTimeInterface $date
     * @return mixed
     */
    public function setSendPackages($transporter, array $sendPackagesResults, \DateTimeInterface $date);
}
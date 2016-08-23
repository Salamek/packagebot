<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot\Storage;


use Salamek\PackageBot\IPackageStorage;
use Salamek\PackageBot\Model\Package;

class PackageBotPackageStorage extends FileStorage implements IPackageStorage
{
    /**
     * @param $transporter
     * @param $orderId
     * @param $seriesId
     * @param $packageNumber
     * @param Package $packageData
     * @param \DateTimeInterface|null $send
     * @return void
     */
    public function savePackage($transporter, $orderId, $seriesId, $packageNumber, Package $packageData, \DateTimeInterface $send = null)
    {
        $this->set(self::STORAGE_TABLE_PACKAGE.$transporter, $seriesId, $packageData);
    }

    /**
     * @param $transporter
     * @return array
     */
    public function getUnSentPackages($transporter)
    {
        return $this->all(self::STORAGE_TABLE_PACKAGE.$transporter);
    }

    /**
     * @param $transporter
     * @param Package[] $packages
     * @param \DateTimeInterface $date
     * @return void
     */
    public function setSendPackages($transporter, array $packages, \DateTimeInterface $date)
    {
        foreach($packages AS $package)
        {
            $this->set(self::STORAGE_TABLE_PACKAGE.$transporter, $package->get, null);
        }
    }

    /**
     * @param $transporter
     * @param $orderId
     * @throws \Exception
     * @return void
     */
    public function getPackageByOrderId($transporter, $orderId)
    {
        // TODO: Implement getPackageByOrderId() method.
        throw new \Exception(__CLASS__.' is unable to do that');
    }

    /**
     * @param $transporter
     * @param $packageNumber
     * @throws \Exception
     * @return null
     */
    public function getPackageByPackageNumber($transporter, $packageNumber)
    {
        // TODO: Implement getPackageByPackageNumber() method.
        throw new \Exception(__CLASS__.' is unable to do that');
    }

    /**
     * @param $transporter
     * @param $seriesNumberId
     * @return null
     */
    public function getPackageBySeriesNumberId($transporter, $seriesNumberId)
    {
        return $this->get(self::STORAGE_TABLE_PACKAGE.$transporter, $seriesNumberId);
    }
}
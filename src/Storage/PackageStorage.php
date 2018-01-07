<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot\Storage;


use Nette\SmartObject;
use Salamek\PackageBot\IPackageStorage;
use Salamek\PackageBot\Model\Package;
use Salamek\PackageBot\Model\SeriesNumberInfo;

class PackageBotPackageStorage extends FileStorage implements IPackageStorage
{
    use SmartObject;
    
    /**
     * @param $transporter
     * @param $packageNumber
     * @param Package $packageData
     * @param \DateTimeInterface|null $send
     * @return void
     */
    public function savePackage($transporter, $packageNumber, Package $packageData, \DateTimeInterface $send = null)
    {
        $this->set(self::STORAGE_TABLE_PACKAGE.$transporter, $packageData->getSeriesNumberInfo()->getSeriesId().$packageData->getSeriesNumberInfo()->getSeriesNumber(), $packageData);
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
    public function getPackagesByOrderId($transporter, $orderId)
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
     * @param SeriesNumberInfo $seriesNumberInfo
     * @return null
     */
    public function getPackageBySeriesNumberInfo($transporter, SeriesNumberInfo $seriesNumberInfo)
    {
        return $this->get(self::STORAGE_TABLE_PACKAGE.$transporter, $seriesNumberInfo->getSeriesId().$seriesNumberInfo->getSeriesNumber());
    }
}
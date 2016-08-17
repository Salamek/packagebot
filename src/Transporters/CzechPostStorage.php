<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot\Transporters;


use Salamek\PackageBot\Enum\Transporter;
use Salamek\PackageBot\IPackageBotStorage;
use Salamek\ICzechPostStorage;
use Salamek\CzechPostPackage;
use Salamek\CzechPostSender;

class CzechPostStorage implements ICzechPostStorage
{
    private $botStorage;

    /**
     * CzechPostStorage constructor.
     * @param IPackageBotStorage $botStorage
     */
    public function __construct(IPackageBotStorage $botStorage)
    {
        $this->botStorage = $botStorage;
    }

    /**
     * @param $id
     * @param CzechPostPackage $package
     * @return void
     */
    public function savePackageToSend($id, CzechPostPackage $package)
    {
        $this->botStorage->savePackage(Transporter::CZECH_POST, $package->getOrderId(), $id, $package, null);
    }

    /**
     * @return mixed
     */
    public function getPackagesToSend()
    {
        return $this->botStorage->getUnSendPackages(Transporter::CZECH_POST);
    }

    /**
     * @param CzechPostPackage $package
     * @param CzechPostSender $sender
     * @param $year
     * @return mixed
     */
    public function getNextPackageId(CzechPostPackage $package, CzechPostSender $sender, $year)
    {
        return $this->botStorage->getNextPackageId(Transporter::CZECH_POST, $package->getType(), $sender->getType().$sender->getId(), $year);
    }

    /**
     * @param array $ids
     * @return void
     */
    public function setPackagesSend(array $ids)
    {
        foreach ($ids AS $id)
        {
            $this->botStorage->setSend(Transporter::CZECH_POST, $id, new \DateTime());
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getPackageByPackageId($id)
    {
        return $this->botStorage->getPackageByPackageId(Transporter::CZECH_POST, $id);
    }
}
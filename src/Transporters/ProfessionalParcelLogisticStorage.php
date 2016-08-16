<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot\Transporters;


use Salamek\IProfessionalParcelLogisticStorage;
use Salamek\PackageBot\IPackageBotStorage;
use Salamek\PackageBot\PackageBot;
use Salamek\ProfessionalParcelLogisticPackage;

class ProfessionalParcelLogisticStorage implements IProfessionalParcelLogisticStorage
{
    private $botStorage;

    /**
     * ProfessionalParcelLogisticStorage constructor.
     * @param IPackageBotStorage $botStorage
     */
    public function __construct(IPackageBotStorage $botStorage)
    {
        $this->botStorage = $botStorage;
    }

    /**
     * @param $id
     * @param ProfessionalParcelLogisticPackage $package
     * @return void
     */
    public function savePackageToSend($id, ProfessionalParcelLogisticPackage $package)
    {
        $this->botStorage->savePackage(PackageBot::TRANSPORTER_PPL, $package->getOrderId(), $id, $package, null);
    }

    /**
     * @return mixed
     */
    public function getPackagesToSend()
    {
        return $this->botStorage->getUnSendPackages(PackageBot::TRANSPORTER_PPL);
    }

    /**
     * @param ProfessionalParcelLogisticPackage $package
     * @param $customerId
     * @param $year
     * @return mixed
     */
    public function getNextPackageId(ProfessionalParcelLogisticPackage $package, $customerId, $year)
    {
        return $this->botStorage->getNextPackageId(PackageBot::TRANSPORTER_PPL, $package->getPackageProductType(), $customerId, $year);
    }

    /**
     * @param array $ids
     * @return void
     */
    public function setPackagesSend(array $ids)
    {
        foreach ($ids AS $id)
        {
            $this->botStorage->setSend(PackageBot::TRANSPORTER_PPL, $id, new \DateTime());
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function getPackageByPackageId($id)
    {
        return $this->botStorage->getPackageByPackageId(PackageBot::TRANSPORTER_PPL, $id);
    }
}
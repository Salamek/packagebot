<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot;

/**
 * Interface IPackageBotStorage
 * @package Salamek\PackageBot
 */
interface IPackageBotStorage
{
    /**
     * @param $transporter
     * @param $orderId
     * @param $packageId
     * @param $packageData
     * @param \DateTime|null $send
     * @return mixed
     */
    public function savePackage($transporter, $orderId, $packageId, $packageData, \DateTime $send = null);

    /**
     * @param $transporter
     * @return mixed
     */
    public function getUnSendPackages($transporter);

    /**
     * @param $transporter
     * @param $orderId
     * @return mixed
     */
    public function getPackageByOrderId($transporter, $orderId);

    /**
     * @param $transporter
     * @param $packageId
     * @return mixed
     */
    public function getPackageByPackageId($transporter, $packageId);

    /**
     * @param $transporter
     * @param null $packageType
     * @param null $sender
     * @param null $year
     * @return mixed
     */
    public function getNextPackageId($transporter, $packageType = null, $sender = null, $year = null);

    /**
     * @param $transporter
     * @param $packageId
     * @param \DateTime $date
     * @return mixed
     */
    public function setSend($transporter, $packageId, \DateTime $date);

    /**
     * @param $transporter
     * @param $packageId
     * @param $label
     * @return string
     */
    public function savePackageLabel($transporter, $packageId, $label);

    /**
     * @param $transporter
     * @param $packageId
     * @return mixed
     */
    public function getPackageLabel($transporter, $packageId);
}
<?php
namespace Salamek\PackageBot\Transporters;

use Salamek\PackageBot\IPackageBotStorage;
use Salamek\PackageBot\PackageBotPackage;
use Salamek\PackageBot\PackageBotParcelInfo;
use Salamek\PackageBot\PackageBotReceiver;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
interface ITransporter
{
    /**
     * ITransporter constructor.
     * @param array $configuration
     * @param array $sender
     * @param IPackageBotStorage $botStorage
     * @param $cookieJar
     */
    public function __construct(array $configuration, array $sender, IPackageBotStorage $botStorage, $cookieJar);

    /**
     * @param PackageBotPackage $package
     * @param PackageBotReceiver $receiver
     * @return int
     */
    public function doParcel(PackageBotPackage $package, PackageBotReceiver $receiver);

    /**
     * @return mixed
     */
    public function doFlush();

    /**
     * @param $id
     * @param $decomposition
     * @return mixed
     */
    public function doGenerateLabel($id, $decomposition);
}
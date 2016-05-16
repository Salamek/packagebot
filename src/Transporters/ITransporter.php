<?php
namespace Extensions\PackageBot\Transporters;

use Extensions\PackageBot\IPackageBotStorage;
use Extensions\PackageBot\PackageBotPackage;
use Extensions\PackageBot\PackageBotParcelInfo;
use Extensions\PackageBot\PackageBotReceiver;

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
     * @return PackageBotParcelInfo
     */
    public function doParcel(PackageBotPackage $package, PackageBotReceiver $receiver);

    public function doFlush();
}
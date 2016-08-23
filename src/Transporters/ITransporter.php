<?php
namespace Salamek\PackageBot\Transporters;

use Salamek\PackageBot\Enum\LabelPosition;
use Salamek\PackageBot\IPackageBotStorage;
use Salamek\PackageBot\Model\Package;

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
     * @param Package $package
     * @return mixed
     */
    public function packageBotPackageToTransporterPackage(Package $package);

    /**
     * @param Package[] $packages
     * @return mixed
     */
    public function doSendPackages(array $packages);

    /**
     * @param \TCPDF $pdf
     * @param Package $package
     * @return /TCPDF
     */
    public function doGenerateLabelFull(\TCPDF $pdf, Package $package);

    /**
     * @param \TCPDF $pdf
     * @param Package $package
     * @param int $position
     * @return /TCPDF
     */
    public function doGenerateLabelQuarter(\TCPDF $pdf, Package $package, $position = LabelPosition::TOP_LEFT);
}
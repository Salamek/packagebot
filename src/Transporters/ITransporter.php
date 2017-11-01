<?php
namespace Salamek\PackageBot\Transporters;

use Salamek\PackageBot\Enum\LabelPosition;
use Salamek\PackageBot\Model\Package;
use Salamek\PackageBot\Model\SendPackageResult;
use Salamek\PackageBot\Storage\ITransporterDataGroupStorage;
use Salamek\PackageBot\Storage\ITransporterDataItemStorage;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
interface ITransporter
{
    /**
     * ITransporter constructor.
     * @param array $configuration
     * @param array $sender
     * @param $cookieJar
     * @param ITransporterDataGroupStorage $transporterDataGroupStorage
     * @param ITransporterDataItemStorage $transporterDataItemStorage
     */
    public function __construct(array $configuration, array $sender, $cookieJar, ITransporterDataGroupStorage $transporterDataGroupStorage, ITransporterDataItemStorage $transporterDataItemStorage);

    /**
     * @param Package $package
     * @return mixed
     */
    public function packageBotPackageToTransporterPackage(Package $package);

    /**
     * @param Package[] $packages
     * @return SendPackageResult[]
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

    /**
     * @param Package $package
     * @return string
     */
    public function doGenerateTrackingUrl(Package $package);

    /**
     * @param Package $package
     * @return string
     */
    public function getPackageNumber(Package $package);

    /**
     * @return bool
     */
    public function hasLocalSeriesNumber();

    /**
     * @return void
     */
    public function doDataUpdate();
}
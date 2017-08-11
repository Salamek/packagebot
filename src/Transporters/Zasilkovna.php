<?php
/**
 * Created by PhpStorm.
 * User: sadam
 * Date: 11.8.17
 * Time: 2:37
 */

namespace Salamek\PackageBot\Transporters;

use Salamek\PackageBot\Enum\LabelPosition;
use Salamek\PackageBot\Model\Package;

class Zasilkovna implements ITransporter
{

    public function __construct(array $configuration, array $sender, $cookieJar)
    {

    }

    public function packageBotPackageToTransporterPackage(Package $package)
    {
        // TODO: Implement packageBotPackageToTransporterPackage() method.
    }

    public function doSendPackages(array $packages)
    {
        // TODO: Implement doSendPackages() method.
    }

    public function doGenerateLabelFull(\TCPDF $pdf, Package $package)
    {
        // TODO: Implement doGenerateLabelFull() method.
    }

    public function doGenerateLabelQuarter(\TCPDF $pdf, Package $package, $position = LabelPosition::TOP_LEFT)
    {
        // TODO: Implement doGenerateLabelQuarter() method.
    }

    public function doGenerateTrackingUrl($packageNumber)
    {
        // TODO: Implement doGenerateTrackingUrl() method.
    }

}
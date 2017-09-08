<?php
/**
 * Created by PhpStorm.
 * User: sadam
 * Date: 11.8.17
 * Time: 2:37
 */

namespace Salamek\PackageBot\Transporters;

use Salamek\PackageBot\Enum\LabelPosition;
use Salamek\PackageBot\Enum\Transporter;
use Salamek\PackageBot\Exception\WrongDeliveryDataException;
use Salamek\PackageBot\Model\Package;
use Salamek\PackageBot\Model\SeriesNumberInfo;
use Salamek\Zasilkovna\ApiRest;
use Salamek\Zasilkovna\Branch;
use Salamek\Zasilkovna\Exception\WrongDataException;
use Salamek\Zasilkovna\Label;
use Salamek\Zasilkovna\Model\BranchStorageSqLite;
use Salamek\Zasilkovna\Model\PacketAttributes;
use Salamek\PackageBot\Model\SendPackageResult;

class Zasilkovna implements ITransporter
{
    private $sender;

    private $api;

    private $branch;

    private $label;

    public function __construct(array $configuration, array $sender, $cookieJar)
    {
        $this->sender = $sender;

        /*try
        {*/
            $this->api = new ApiRest($configuration['apiPassword'], $configuration['apiKey']);
            $this->branch = new Branch($configuration['apiKey'], new BranchStorageSqLite()); //@TODO Implement custom storage using nette cache or Packagebot generic storage ?
            $this->label = new Label($this->api, $this->branch);
            //$this->api = new ApiSoap($configuration['apiPassword'], $configuration['apiKey']);
        /*}
        catch (OfflineException $e)
        {
            throw new \Salamek\PackageBot\Exception\OfflineException($e->getMessage(), $e->getCode(), $e);
        }*/
    }

    public function packageBotPackageToTransporterPackage(Package $package)
    {
        //Get address ID
        $addressId = $package->getSpecialData(Transporter::ZASILKOVNA);

        return new PacketAttributes(
            $package->getOrderId(),
            $package->getRecipient()->getFirstName(),
            $package->getRecipient()->getLastName(),
            ($package->getGoodsPrice() ? $package->getGoodsPrice() : null),
            $addressId,
            ($package->getSeriesNumberInfo() ? $package->getSeriesNumberInfo()->getSeriesNumber() : null),
            $package->getRecipient()->getCompany(),
            $package->getRecipient()->getEmail(),
            $package->getRecipient()->getPhone(),
            ($package->getPaymentInfo() ? $package->getPaymentInfo()->getCashOnDeliveryCurrency() : null),
            ($package->getPaymentInfo() ? $package->getPaymentInfo()->getCashOnDeliveryPrice() : null),
            ($package->getWeightedPackageInfo() ? $package->getWeightedPackageInfo()->getWeight() : null),
            $this->sender['www'],
            false,
            $package->getRecipient()->getStreet(),
            $package->getRecipient()->getStreetNumber(),
            $package->getRecipient()->getCity(),
            $package->getRecipient()->getZipCode()
        );
    }

    public function doSendPackages(array $packages)
    {
        $return = [];

        /** @var Package $package */
        foreach ($packages AS $package)
        {
            $transporterPackage = $this->packageBotPackageToTransporterPackage($package);
            try
            {
                $this->api->packetAttributesValid($transporterPackage);
            }
            catch (WrongDataException $e)
            {
                throw new WrongDeliveryDataException($e->getMessage(), $e->getCode(), $e->getPrevious());
            }
            
            $result = $this->api->createPacket($transporterPackage);

            $seriesNumberInfo = new SeriesNumberInfo($result->id, null, $result->barcode);
            $package->setSeriesNumberInfo($seriesNumberInfo);

            $return[] = new SendPackageResult(true, 'OK', 'OK', $package->getSeriesNumberInfo());
        }

        return $return;
    }

    /**
     * @param \TCPDF $pdf
     * @param Package $package
     * @return \TCPDF
     */
    public function doGenerateLabelFull(\TCPDF $pdf, Package $package)
    {
        $transporterPackage = $this->packageBotPackageToTransporterPackage($package);
        return $this->label->generateLabelFull($pdf, $transporterPackage);
    }

    /**
     * @param \TCPDF $pdf
     * @param Package $package
     * @param int $position
     * @return \TCPDF
     * @throws \Exception
     */
    public function doGenerateLabelQuarter(\TCPDF $pdf, Package $package, $position = LabelPosition::TOP_LEFT)
    {
        $transporterPackage = $this->packageBotPackageToTransporterPackage($package);
        return $this->label->generateLabelQuarter($pdf, $transporterPackage, $position);
    }

    /**
     * @param Package $package
     * @throws \Exception
     */
    public function getPackageNumber(Package $package)
    {
        throw new \Exception('This transporter service has no localy generated series numbers!');
    }

    /**
     * @param Package $package
     * @return string
     */
    public function doGenerateTrackingUrl(Package $package)
    {
        $transporterPackage = $this->packageBotPackageToTransporterPackage($package);
        return 'https://www.zasilkovna.cz/vyhledavani?number='.$transporterPackage->getId();
    }

    public function hasLocalSeriesNumber()
    {
        return false;
    }
}
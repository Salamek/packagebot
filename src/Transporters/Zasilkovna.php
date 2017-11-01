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
use Salamek\PackageBot\Model\TransporterDataItem;
use Salamek\PackageBot\Storage\ITransporterDataGroupStorage;
use Salamek\PackageBot\Storage\ITransporterDataItemStorage;
use Salamek\Zasilkovna\ApiRest;
use Salamek\Zasilkovna\Branch;
use Salamek\Zasilkovna\Exception\WrongDataException;
use Salamek\Zasilkovna\Label;
use Salamek\Zasilkovna\Model\IBranchStorage;
use Salamek\Zasilkovna\Model\PacketAttributes;
use Salamek\PackageBot\Model\SendPackageResult;

/**
 * Class BranchStoragePackageBot
 * @package Salamek\PackageBot\Transporters
 */
class BranchStoragePackageBot implements IBranchStorage
{
    /** @var string */
    private $expiry;

    /** @var ITransporterDataGroupStorage */
    private $transporterDataGroupStorage;

    /** @var ITransporterDataItemStorage */
    private $transporterDataItemStorage;

    /**
     * BranchStoragePackageBot constructor.
     * @param ITransporterDataGroupStorage $transporterDataGroupStorage
     * @param ITransporterDataItemStorage $transporterDataItemStorage
     * @param string $expiry
     */
    public function __construct(ITransporterDataGroupStorage $transporterDataGroupStorage, ITransporterDataItemStorage $transporterDataItemStorage, $expiry = '-7 days')
    {
        $this->transporterDataGroupStorage = $transporterDataGroupStorage;
        $this->transporterDataItemStorage = $transporterDataItemStorage;
        $this->expiry = $expiry;
    }

    /**
     * @return \Generator
     */
    public function getBranchList()
    {
        foreach($this->transporterDataItemStorage->findBy(['transporter' => Transporter::ZASILKOVNA]) AS $item)
        {
            yield $item->getData();
        }
    }

    /**
     * @param $id
     * @return mixed|null
     */
    public function find($id)
    {
        $found = $this->transporterDataItemStorage->findOneBy(['identifier' => $id]);
        if ($found)
        {
            return $found->getData();
        }

        return null;
    }

    /**
     * @param $branchList
     */
    public function setBranchList($branchList)
    {
        //Delete all stuff

        $this->transporterDataItemStorage->deleteBy(['transporter' => Transporter::ZASILKOVNA]);

        $this->transporterDataGroupStorage->deleteBy(['transporter' => Transporter::ZASILKOVNA]);

        foreach($branchList AS $item)
        {
            $group = null;
            $dataItem = new TransporterDataItem(Transporter::ZASILKOVNA, $item['id'], strtoupper($item['country']).', '.$item['name'], $item, new \DateTime(), $group);
            $this->transporterDataItemStorage->create($dataItem);
        }
    }

    /**
     * @return bool
     */
    public function isStorageValid()
    {
        $found = $this->transporterDataItemStorage->findOneBy([], ['date' => 'DESC']);

        $limit = new \DateTime();
        $limit->modify($this->expiry);

        if ($found && $found->getDate() > $limit)
        {
            return true;
        }

        return false;
    }
}

/**
 * Class Zasilkovna
 * @package Salamek\PackageBot\Transporters
 */
class Zasilkovna implements ITransporter
{
    /** @var array */
    private $sender;

    /** @var ApiRest */
    private $api;

    /** @var Branch */
    private $branch;

    /** @var Label */
    private $label;

    /** @var array */
    private $configuration;

    /**
     * Zasilkovna constructor.
     * @param array $configuration
     * @param array $sender
     * @param $tempDir
     * @param ITransporterDataGroupStorage $transporterDataGroupStorage
     * @param ITransporterDataItemStorage $transporterDataItemStorage
     */
    public function __construct(
        array $configuration,
        array $sender,
        $tempDir,
        ITransporterDataGroupStorage $transporterDataGroupStorage,
        ITransporterDataItemStorage $transporterDataItemStorage
    )
    {
        $this->configuration = $configuration;
        $this->sender = $sender;

        $this->api = new ApiRest($configuration['apiPassword'], $configuration['apiKey']);
        $this->branch = new Branch($configuration['apiKey'], new BranchStoragePackageBot($transporterDataGroupStorage, $transporterDataItemStorage));
        $this->label = new Label($this->api, $this->branch);
    }

    /**
     * @param Package $package
     * @return PacketAttributes
     */
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
            $this->configuration['eshop'],
            false,
            $package->getRecipient()->getStreet(),
            $package->getRecipient()->getStreetNumber(),
            $package->getRecipient()->getCity(),
            $package->getRecipient()->getZipCode()
        );
    }

    /**
     * @param array $packages
     * @return array
     * @throws WrongDeliveryDataException
     */
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
     * @return void
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

    /**
     * @return bool
     */
    public function hasLocalSeriesNumber()
    {
        return false;
    }

    /**
     * @throws \Exception
     */
    public function doDataUpdate()
    {
        $this->branch->initializeStorage(true);
    }
}
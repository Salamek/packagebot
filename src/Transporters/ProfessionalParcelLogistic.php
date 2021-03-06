<?php
namespace Salamek\PackageBot\Transporters;

use Nette\SmartObject;
use Salamek\PackageBot\Enum\LabelPosition;
use Salamek\PackageBot\Enum\TransportService;
use Salamek\PackageBot\Exception\WrongDeliveryDataException;
use Salamek\PackageBot\ITransporterDataStorage;
use Salamek\PackageBot\Model\SendPackageResult;
use Salamek\PackageBot\Storage\ITransporterDataGroupStorage;
use Salamek\PackageBot\Storage\ITransporterDataItemStorage;
use Salamek\PplMyApi\Api;
use Salamek\PplMyApi\Enum\Product;
use Salamek\PplMyApi\Exception\OfflineException;
use Salamek\PplMyApi\Exception\WrongDataException;
use Salamek\PplMyApi\Label;
use Salamek\PplMyApi\Model\Package as TransporterPackage;
use Salamek\PplMyApi\Model\PackageNumberInfo;
use Salamek\PplMyApi\Model\PaymentInfo;
use Salamek\PplMyApi\Model\Recipient;
use Salamek\PplMyApi\Model\Sender;

use Salamek\PackageBot\Model\Package;
use Salamek\PplMyApi\Tools;


/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class ProfessionalParcelLogistic implements ITransporter
{
    use SmartObject;

    /** @var mixed */
    private $customerId;

    /** @var mixed */
    private $username;

    /** @var mixed */
    private $password;

    /** @var mixed */
    private $depoCode;
    
    /** @var Api */
    private $api;

    /** @var Sender */
    private $professionalParcelLogisticSender;

    /**
     * ProfessionalParcelLogistic constructor.
     * @param array $configuration
     * @param array $sender
     * @param $tempDir
     * @param ITransporterDataGroupStorage $transporterDataGroupStorage
     * @param ITransporterDataItemStorage $transporterDataItemStorage
     * @throws \Salamek\PackageBot\Exception\OfflineException
     */
    public function __construct(
        array $configuration,
        array $sender,
        $tempDir,
        ITransporterDataGroupStorage $transporterDataGroupStorage,
        ITransporterDataItemStorage $transporterDataItemStorage
    )
    {
        $this->customerId = $configuration['senderId'];
        $this->username = $configuration['username'];
        $this->password = $configuration['password'];
        $this->depoCode = $configuration['depoCode'];

        $this->professionalParcelLogisticSender = new Sender(
            $sender['city'],
            $sender['name'],
            $sender['street'].' '.$sender['streetNumber'],
            $sender['zipCode'],
            $sender['email'],
            $sender['phone'],
            null,
            $sender['country'],
            $sender['www']
        );

        try
        {
            $this->api = new Api($this->username, $this->password, $this->customerId);
        }
        catch (OfflineException $e)
        {
            throw new \Salamek\PackageBot\Exception\OfflineException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param Package $package
     * @return TransporterPackage
     * @throws WrongDeliveryDataException
     */
    public function packageBotPackageToTransporterPackage(Package $package)
    {
        try
        {
            switch ($package->getTransportService())
            {
                default:
                case TransportService::PPL_PARCEL_CZ_PRIVATE:
                    $packageProductType = Product::PPL_PARCEL_CZ_PRIVATE;
                    break;
                
                case TransportService::PPL_PARCEL_CZ_PRIVATE_COD:
                    $packageProductType = Product::PPL_PARCEL_CZ_PRIVATE_COD;
                    break;
            }

            if (!is_null($package->getPaymentInfo()))
            {
                $professionalParcelLogisticPaymentInfo = new PaymentInfo(
                    $package->getPaymentInfo()->getCashOnDeliveryPrice(),
                    $package->getPaymentInfo()->getCashOnDeliveryCurrency(),
                    $package->getPaymentInfo()->getBankIdentifier()
                );
            }
            else
            {
                $professionalParcelLogisticPaymentInfo = null;
            }
            $professionalParcelLogisticRecipient = new Recipient(
                $package->getRecipient()->getCity(),
                ($package->getRecipient()->getCompany() ? $package->getRecipient()->getCompany() : $package->getRecipient()->getFirstName().' '.$package->getRecipient()->getLastName()),
                $package->getRecipient()->getStreet().' '.$package->getRecipient()->getStreetNumber(),
                $package->getRecipient()->getZipCode(),
                $package->getRecipient()->getEmail(),
                $package->getRecipient()->getPhone(),
                $package->getRecipient()->getFirstName().' '.$package->getRecipient()->getLastName(),
                $package->getRecipient()->getCountry(),
                $package->getRecipient()->getWww()
            );

            if (!is_null($package->getWeightedPackageInfo()))
            {
                $weight = $package->getWeightedPackageInfo()->getWeight();
            }
            else
            {
                $weight = null;
            }

            if (strlen($package->getDescription()) > 300)
            {
                $description = mb_substr($package->getDescription(), 0, 300);
            }
            else
            {
                $description = $package->getDescription();
            }

            $packageNumberInfo = new PackageNumberInfo(
                $package->getSeriesNumberInfo()->getSeriesNumber(),
                $packageProductType,
                $this->depoCode
            );

            $packageNumber = Tools::generatePackageNumber($packageNumberInfo);

            return new TransporterPackage(
                $packageNumber,
                $packageProductType,
                $weight,
                $description,
                $this->depoCode,
                $professionalParcelLogisticRecipient,
                $this->professionalParcelLogisticSender,
                null,
                $professionalParcelLogisticPaymentInfo,
                [],
                [],
                [],
                null,
                null,
                $package->getPackageCount(),
                $package->getPackagePosition()
            );
        }
        catch (WrongDataException $e)
        {
            throw new WrongDeliveryDataException($e->getMessage(), $e->getCode(), $e->getPrevious());
        }
    }

    /**
     * @param array $packages
     * @throws WrongDataException
     * @throws WrongDeliveryDataException
     * @throws \Exception
     * @return SendPackageResult[]
     */
    public function doSendPackages(array $packages)
    {
        $transporterPackages = [];
        $packagesByPackageNumber = [];
        /** @var Package $package */
        foreach ($packages AS $package)
        {
            $transporterPackage = $this->packageBotPackageToTransporterPackage($package);
            $transporterPackages[] = $transporterPackage;
            $packagesByPackageNumber[$transporterPackage->getPackageNumber()] = $package;
        }

        $return = [];
        if (!empty($transporterPackages))
        {
            $results = $this->api->createPackages($transporterPackages);

            //This may return simple result for single package or array for multiple packages
            /*
             * stdClass Object
                (
                    [Code] => 0
                    [ItemKey] => 40990940972
                    [Message] =>
                )
                */

            $proccessResult = function($result) use($packagesByPackageNumber)
            {
                if (!array_key_exists($result->ItemKey, $packagesByPackageNumber))
                {
                    throw new \Exception('Returned PackageNumber is not in send PackageNumbers');
                }

                /** @var Package $foundSendPackage */
                $foundSendPackage = $packagesByPackageNumber[$result->ItemKey];
                return new SendPackageResult(
                    ($result->Code == 0 ? true : false),
                    $result->Code,
                    (!$result->Message ? ($result->Code == 0 ? 'OK' : 'ERR') : $result->Message),
                    $foundSendPackage->getSeriesNumberInfo()
                );
            };

            if (is_object($results) && property_exists($results, 'ItemKey'))
            {
                $return[] = $proccessResult($results);
            }
            else
            {
                foreach($results AS $result)
                {
                    $return[] = $proccessResult($result);
                }
            }
        }
        
        return $return;
    }

    /**
     * @param \TCPDF $pdf
     * @param Package $package
     * @return \TCPDF
     * @throws WrongDeliveryDataException
     */
    public function doGenerateLabelFull(\TCPDF $pdf, Package $package)
    {
        $transporterPackage = $this->packageBotPackageToTransporterPackage($package);
        return Label::generateLabelFull($pdf, $transporterPackage);
    }

    /**
     * @param \TCPDF $pdf
     * @param Package $package
     * @param int $position
     * @return \TCPDF
     * @throws WrongDeliveryDataException
     * @throws \Exception
     */
    public function doGenerateLabelQuarter(\TCPDF $pdf, Package $package, $position = LabelPosition::TOP_LEFT)
    {
        $transporterPackage = $this->packageBotPackageToTransporterPackage($package);
        return Label::generateLabelQuarter($pdf, $transporterPackage, $position);
    }

    /**
     * @param Package $package
     * @return string
     * @throws WrongDeliveryDataException
     */
    public function doGenerateTrackingUrl(Package $package)
    {
        $transporterPackage = $this->packageBotPackageToTransporterPackage($package);
        return 'https://www.ppl.cz/main2.aspx?cls=Package&idSearch='.$transporterPackage->getPackageNumber();
    }

    /**
     * @param Package $package
     * @return string
     * @throws WrongDeliveryDataException
     */
    public function getPackageNumber(Package $package)
    {
        $transporterPackage = $this->packageBotPackageToTransporterPackage($package);
        return $transporterPackage->getPackageNumber();
    }

    /**
     * @return bool
     */
    public function hasLocalSeriesNumber()
    {
        return true;
    }

    public function doDataUpdate()
    {
        // TODO: Implement doDataUpdate() method.
    }
}

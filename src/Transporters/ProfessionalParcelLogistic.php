<?php
namespace Salamek\PackageBot\Transporters;

use Salamek\MyApi\Dial;
use Salamek\PackageBot\IPackageBotStorage;
use Salamek\PackageBot\PackageBot;
use Salamek\PackageBot\PackageBotPackage;
use Salamek\PackageBot\PackageBotParcelInfo;
use Salamek\PackageBot\PackageBotPaymentInfo;
use Salamek\PackageBot\PackageBotReceiver;
use Salamek\ProfessionalParcelLogisticApi;
use Salamek\ProfessionalParcelLogisticPackage;
use Salamek\ProfessionalParcelLogisticPaymentInfo;
use Salamek\ProfessionalParcelLogisticRecipient;
use Salamek\ProfessionalParcelLogisticSender;
use Salamek\PackageBot\WrongDeliveryDataException;
use Salamek\ProfessionalParcelLogisticWrongDataException;

/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */
class ProfessionalParcelLogistic implements ITransporter
{
    /** @var mixed */
    private $customerId;

    /** @var mixed */
    private $username;

    /** @var mixed */
    private $password;

    /** @var mixed */
    private $depoCode;

    /** @var IPackageBotStorage  */
    private $botStorage;

    /** @var ProfessionalParcelLogisticApi */
    private $api;
    
    private $professionalParcelLogisticSender;

    /**
     * Ppl constructor.
     * @param array $configuration
     * @param array $sender
     * @param IPackageBotStorage $botStorage
     * @param $cookieJar
     */
    public function __construct(array $configuration, array $sender, IPackageBotStorage $botStorage, $cookieJar)
    {
        $this->customerId = $configuration['customerId'];
        $this->username = $configuration['username'];
        $this->password = $configuration['password'];
        $this->depoCode = $configuration['depoCode'];
        $this->botStorage = $botStorage;
        
        $this->professionalParcelLogisticSender = new ProfessionalParcelLogisticSender($sender['city'], $sender['name'], $sender['street'].' '.$sender['streetNumber'], $sender['zipCode'], $sender['email'], $sender['phone'], $sender['www'], $sender['countryCode']);

        $this->api = new ProfessionalParcelLogisticApi($this->username, $this->password, $this->customerId, new ProfessionalParcelLogisticStorage($this->botStorage));
    }

    /**
     * @param PackageBotPackage $package
     * @param PackageBotReceiver $receiver
     * @param PackageBotPaymentInfo $paymentInfo
     * @return mixed
     * @throws WrongDeliveryDataException
     * @throws \Exception
     */
    public function doParcel(PackageBotPackage $package, PackageBotReceiver $receiver, PackageBotPaymentInfo $paymentInfo = null)
    {
        try {
            if (!is_null($paymentInfo))
            {
                $packageProductType = Dial::PRODUCT_TYPE_PPL_PARCEL_CZ_PRIVATE_COD;
                $professionalParcelLogisticPaymentInfo = new ProfessionalParcelLogisticPaymentInfo($paymentInfo->getCashOnDeliveryPrice(), $paymentInfo->getCashOnDeliveryCurrency(), $paymentInfo->getBankIdentifier());
            }
            else
            {
                $packageProductType = Dial::PRODUCT_TYPE_PPL_PARCEL_CZ_PRIVATE;
                $professionalParcelLogisticPaymentInfo = null;
            }

            $professionalParcelLogisticRecipient = new ProfessionalParcelLogisticRecipient($receiver->getCity(), $receiver->getFirstName().' '.$receiver->getLastName(), $receiver->getStreet().' '.$receiver->getStreetNumber(), $receiver->getZipCode(), $receiver->getEmail(), $receiver->getPhone(), $receiver->getWww(), $receiver->getStateCode(), $receiver->getCompany());

            $professionalParcelLogisticPackage = new ProfessionalParcelLogisticPackage($package->getOrderId(), null, $packageProductType, $package->getWeight(), $package->getDescription(), $this->depoCode, $this->professionalParcelLogisticSender, $professionalParcelLogisticRecipient, null, $professionalParcelLogisticPaymentInfo);

        } catch (ProfessionalParcelLogisticWrongDataException $e) {
            throw new WrongDeliveryDataException($e->getMessage());
        }

        $this->api->persistPackage($professionalParcelLogisticPackage);

        return $this->api->generatePackageIdentifier($professionalParcelLogisticPackage);
    }

    /**
     * @return void
     */
    public function doFlush()
    {
        $this->api->flushPackages();
    }

    /**
     * @param $id
     * @param $decomposition
     * @return string
     * @throws \Exception
     */
    public function doGenerateLabel($id, $decomposition)
    {
        switch ($decomposition)
        {
            case PackageBot::PACKAGE_LABEL_QUARTER:
                $decompositionProfessionalParcelLogistic = ProfessionalParcelLogisticApi::LABEL_DECOMPOSITION_QUARTER;
                break;

            default:
            case PackageBot::PACKAGE_LABEL_FULL:
                $decompositionProfessionalParcelLogistic = ProfessionalParcelLogisticApi::LABEL_DECOMPOSITION_FULL;
                break;
        }

        return $this->api->genetatePackageLabelByPackageId($id, null, ProfessionalParcelLogisticApi::LABEL_FORMAT_RAW, $decompositionProfessionalParcelLogistic);
    }
}
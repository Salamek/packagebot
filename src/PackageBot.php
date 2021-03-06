<?php
namespace Salamek\PackageBot;
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

use Salamek\PackageBot\Enum\LabelDecomposition;
use Salamek\PackageBot\Enum\LabelPosition;
use Salamek\PackageBot\Enum\Transporter;
use Salamek\PackageBot\Enum\TransportService;
use Salamek\PackageBot\Exception\WrongDeliveryDataException;
use Salamek\PackageBot\Model\Package;
use Salamek\PackageBot\Model\SendPackageResult;
use Salamek\PackageBot\Storage\IPackageStorage;
use Salamek\PackageBot\Storage\ISeriesNumberStorage;
use Salamek\PackageBot\Storage\ITransporterDataGroupStorage;
use Salamek\PackageBot\Storage\ITransporterDataItemStorage;
use Salamek\PackageBot\Transporters\ITransporter;
use Nette;

class PackageBot
{
    use Nette\SmartObject;
    
    public static $namespace = 'Salamek\PackageBot';

    /** @var array */
    private $transporters;

    /** @var array */
    private $sender;

    /** @var IPackageStorage */
    private $packageStorage;

    /** @var ISeriesNumberStorage */
    private $seriesNumberStorage;

    /** @var ITransporterDataGroupStorage */
    private $transporterDataGroupStorage;

    /** @var ITransporterDataItemStorage */
    private $transporterDataItemStorage;

    /** @var Nette\Caching\Cache */
    private $cache;

    /** @var null|string */
    private $tempDir;

    /**
     * PackageBot constructor.
     * @param Nette\Caching\IStorage $cacheStorage
     * @param array $transporters
     * @param array $sender
     * @param IPackageStorage $packageStorage
     * @param ISeriesNumberStorage $seriesNumberStorage
     * @param ITransporterDataGroupStorage $transporterDataGroupStorage
     * @param ITransporterDataItemStorage $transporterDataItemStorage
     * @param null $tempDir
     */
    public function __construct(
        Nette\Caching\IStorage $cacheStorage,
        array $transporters,
        array $sender,
        IPackageStorage $packageStorage,
        ISeriesNumberStorage $seriesNumberStorage,
        ITransporterDataGroupStorage $transporterDataGroupStorage,
        ITransporterDataItemStorage $transporterDataItemStorage,
        $tempDir = null
    )
    {
        $this->cache = new Nette\Caching\Cache($cacheStorage, self::$namespace);
        $this->transporters = $transporters;
        $this->sender = $sender;
        $this->tempDir = $tempDir;
        $this->packageStorage = $packageStorage;
        $this->seriesNumberStorage = $seriesNumberStorage;
        $this->transporterDataGroupStorage = $transporterDataGroupStorage;
        $this->transporterDataItemStorage = $transporterDataItemStorage;
    }

    /**
     * @param $transporter
     * @return ITransporter
     * @throws \Exception
     */
    private function getTransporter($transporter)
    {
        if (!array_key_exists($transporter, $this->transporters))
        {
            throw new \Exception(sprintf('Transporter %s is not configured', $transporter));
        }

        if (!$this->transporters[$transporter]['enabled'])
        {
            throw new \Exception(sprintf('Transporter %s is not enabled', $transporter));
        }

        switch($transporter)
        {
            case Transporter::CZECH_POST:
            case Transporter::PPL:
            case Transporter::ULOZENKA:
            case Transporter::ZASILKOVNA:
                $className = 'Salamek\\PackageBot\\Transporters\\'.ucfirst($transporter);
                /** @var ITransporter $iTransporter */
                $iTransporter = new $className(
                    $this->transporters[$transporter],
                    $this->sender,
                    $this->tempDir,
                    $this->transporterDataGroupStorage,
                    $this->transporterDataItemStorage
                );
                break;

            default:
                //@TODO Allow custom transporters here ?
                throw new \Exception('Unknow transporter');
                break;
        }

        return $iTransporter;
    }

    public function dataUpdate(array $transporterNames = [])
    {
        if (empty($transporterNames))
        {
            $transportersData = $this->transporters;
        }
        else
        {
            $transportersData = [];

            foreach($transporterNames AS $transporterName)
            {
                if (!array_key_exists($transporterName, $this->transporters))
                {
                    throw new \Exception(sprintf('Transporter %s is not configured', $transporterName));
                }

                if (!$this->transporters[$transporterName]['enabled'])
                {
                    throw new \Exception(sprintf('Transporter %s is not enabled', $transporterName));
                }

                $transportersData[$transporterName] = $this->transporters[$transporterName];
            }
        }

        foreach($transportersData AS $transporter => $config)
        {
            if ($config['enabled'])
            {
                $iTransporter = $this->getTransporter($transporter);
                /** @var SendPackageResult[] $sendPackagesResults */
                $iTransporter->doDataUpdate();
            }
        }
    }

    /**
     * @param array $transporterNames
     * @throws \Exception
     */
    public function flush(array $transporterNames = [])
    {
        if (empty($transporterNames))
        {
            $transportersData = $this->transporters;
        }
        else
        {
            $transportersData = [];

            foreach($transporterNames AS $transporterName)
            {
                if (!array_key_exists($transporterName, $this->transporters))
                {
                    throw new \Exception(sprintf('Transporter %s is not configured', $transporterName));
                }

                if (!$this->transporters[$transporterName]['enabled'])
                {
                    throw new \Exception(sprintf('Transporter %s is not enabled', $transporterName));
                }

                $transportersData[$transporterName] = $this->transporters[$transporterName];
            }
        }

        foreach($transportersData AS $transporter => $config)
        {
            if ($config['enabled'])
            {
                $iTransporter = $this->getTransporter($transporter);
                $unsentPackages = $this->packageStorage->getUnSentPackages($transporter);
                /** @var SendPackageResult[] $sendPackagesResults */
                $sendPackagesResults = $iTransporter->doSendPackages($unsentPackages);
                $this->packageStorage->setSendPackages($transporter, $sendPackagesResults, new \DateTime());
            }
        }
    }

    /**
     * @param $transportService
     * @return mixed
     * @throws \Exception
     */
    public function transportServiceToTransporter($transportService)
    {
        $map = [
            TransportService::CZECH_POST_PACKAGE_TO_HAND => Transporter::CZECH_POST,
            TransportService::CZECH_POST_PACKAGE_TO_THE_POST_OFFICE => Transporter::CZECH_POST,
            TransportService::PPL_PARCEL_CZ_PRIVATE => Transporter::PPL,
            TransportService::PPL_PARCEL_CZ_PRIVATE_COD => Transporter::PPL,
            TransportService::ZASILKOVNA => Transporter::ZASILKOVNA,
        ];

        if (!array_key_exists($transportService, $map))
        {
            throw new \Exception(sprintf('Transport service %s is not mapped to any transporter!', $transportService));
        }

        return $map[$transportService];
    }

    /**
     * @param Package[] $packages
     * @return string
     * @throws \Exception
     */
    public function parcel(array $packages)
    {
        foreach($packages AS $package)
        {
            //Get transporter from package
            $transporter = $this->transportServiceToTransporter($package->getTransportService());

            $transporterConfig = $this->transporters[$transporter];

            //Get transporter class
            $iTransporter = $this->getTransporter($transporter);

            if ($iTransporter->hasLocalSeriesNumber())
            {
                //Get next unique ID for package from series, this action generates PackageNumber too
                // We need set SeriesNumberInfo before we attempt to call SeriesNumberInfo
                $seriesNumberInfo = $this->seriesNumberStorage->getNextSeriesNumberId($transporter, $package->getTransportService(), $transporterConfig['senderId']);
                $package->setSeriesNumberInfo($seriesNumberInfo);

                $seriesNumberInfo->setPackageNumber($iTransporter->getPackageNumber($package));
                $package->setSeriesNumberInfo($seriesNumberInfo);

                //If we get here, everything went ok, so we can save package into storage
                $this->packageStorage->savePackage($transporter, $seriesNumberInfo->getPackageNumber(), $package);
            }
            else
            {
                $sendPackagesResults = $iTransporter->doSendPackages([$package]);
                
                foreach ($sendPackagesResults AS $sendPackagesResult)
                {
                    $this->packageStorage->savePackage($transporter, $sendPackagesResult->getSeriesNumberInfo()->getPackageNumber(), $package, new \DateTime());
                }
                
                $this->packageStorage->setSendPackages($transporter, $sendPackagesResults, new \DateTime());
            }
        }
    }

    /**
     * @param array $packages
     * @param int $decomposition
     * @param null|string $savePath
     * @return string
     * @throws WrongDeliveryDataException
     * @throws \Exception
     */
    public function getLabels(array $packages, $decomposition = LabelDecomposition::QUARTER, $savePath = null)
    {
        if (!in_array($decomposition, LabelDecomposition::$list)) {
            throw new WrongDeliveryDataException(sprintf('unknown $decomposition only %s are allowed', implode(', ', LabelDecomposition::$list)));
        }

        $packageNumbers = [];
        /** @var Package $package */
        foreach($packages AS $package)
        {
            $packageNumbers[] = $package->getSeriesNumberInfo()->getSeriesNumber();
        }

        $pdf = new \TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('Adam Schubert');
        $pdf->SetTitle(sprintf('Package bot Label %s', implode(', ', $packageNumbers)));
        $pdf->SetSubject(sprintf('Package bot Label %s', implode(', ', $packageNumbers)));
        $pdf->SetKeywords('Package bot');
        $pdf->SetFont('freeserif');
        $pdf->setFontSubsetting(true);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        $quarterPosition = LabelPosition::TOP_LEFT;
        /** @var Package $package */
        foreach ($packages AS $package) {
            $transporter = $this->transportServiceToTransporter($package->getTransportService());
            //Get transporter class
            $iTransporter = $this->getTransporter($transporter);

            switch ($decomposition) {
                case LabelDecomposition::FULL:
                    $pdf->AddPage();
                    $pdf = $iTransporter->doGenerateLabelFull($pdf, $package);
                    break;
                case LabelDecomposition::QUARTER:
                    if ($quarterPosition > LabelPosition::BOTTOM_RIGHT) {
                        $quarterPosition = LabelPosition::TOP_LEFT;
                    }
                    if ($quarterPosition == LabelPosition::TOP_LEFT) {
                        $pdf->AddPage();
                    }
                    $pdf = $iTransporter->doGenerateLabelQuarter($pdf, $package, $quarterPosition);
                    $quarterPosition++;
                    break;
            }
        }


        if (is_null($savePath))
        {
            return $pdf->Output(null, 'S');
        }
        else
        {
            $fullPath = $savePath.'/'.md5(implode('-', $packageNumbers)).'.pdf';
            $pdf->Output($fullPath, 'F');
            return $fullPath;
        }
    }

    /**
     * @param Package $package
     * @return string
     */
    public function getTrackingUrl(Package $package)
    {
        //Get transporter from package
        $transporter = $this->transportServiceToTransporter($package->getTransportService());

        //Get transporter class
        $iTransporter = $this->getTransporter($transporter);

        return $iTransporter->doGenerateTrackingUrl($package);
    }

    /**
     * @return array
     */
    public function getTransporters()
    {
        return $this->transporters;
    }

    /**
     * @return array
     */
    public function getSender()
    {
        return $this->sender;
    }
}
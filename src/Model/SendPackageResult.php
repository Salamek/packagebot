<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot\Model;


use Nette\SmartObject;

class SendPackageResult
{
    use SmartObject;
    
    /** @var bool */
    private $success = false;

    /** @var null|integer */
    private $statusCode = null;

    /** @var null|string */
    private $statusMessage = null;

    /** @var SeriesNumberInfo */
    private $seriesNumberInfo;

    /**
     * SendPackageResult constructor.
     * @param bool $status
     * @param int|null $statusCode
     * @param null|string $statusMessage
     * @param SeriesNumberInfo $seriesNumberInfo
     */
    public function __construct($status, $statusCode, $statusMessage, SeriesNumberInfo $seriesNumberInfo)
    {
        $this->success = $status;
        $this->statusCode = $statusCode;
        $this->statusMessage = $statusMessage;
        $this->seriesNumberInfo = $seriesNumberInfo;
    }

    /**
     * @param boolean $success
     */
    public function setSuccess($success)
    {
        $this->success = $success;
    }

    /**
     * @param int|null $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @param null|string $statusMessage
     */
    public function setStatusMessage($statusMessage)
    {
        $this->statusMessage = $statusMessage;
    }

    /**
     * @param SeriesNumberInfo $seriesNumberInfo
     */
    public function setSeriesNumberInfo($seriesNumberInfo)
    {
        $this->seriesNumberInfo = $seriesNumberInfo;
    }

    /**
     * @return boolean
     */
    public function isSuccess()
    {
        return $this->success;
    }

    /**
     * @return int|null
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @return null|string
     */
    public function getStatusMessage()
    {
        return $this->statusMessage;
    }

    /**
     * @return SeriesNumberInfo
     */
    public function getSeriesNumberInfo()
    {
        return $this->seriesNumberInfo;
    }
}
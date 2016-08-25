<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot\Model;


class SendPackageResult
{
    /** @var bool */
    private $status = false;

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
        $this->status = $status;
        $this->statusCode = $statusCode;
        $this->statusMessage = $statusMessage;
        $this->seriesNumberInfo = $seriesNumberInfo;
    }

    /**
     * @param boolean $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
    public function isStatus()
    {
        return $this->status;
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
<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot\Model;


class SeriesNumberInfo
{
    /** @var integer|null */
    private $seriesId = null;

    /** @var integer */
    private $seriesNumber;

    /** @var string|null */
    private $packageNumber;

    /**
     * SeriesNumberInfo constructor.
     * @param $seriesNumber
     * @param null $seriesId
     * @param null $packageNumber
     */
    public function __construct($seriesNumber, $seriesId = null, $packageNumber = null)
    {
        $this->seriesId = $seriesId;
        $this->seriesNumber = $seriesNumber;
        $this->packageNumber = $packageNumber;
    }

    /**
     * @param int $seriesId
     */
    public function setSeriesId($seriesId)
    {
        $this->seriesId = $seriesId;
    }

    /**
     * @param int $seriesNumber
     */
    public function setSeriesNumber($seriesNumber)
    {
        $this->seriesNumber = $seriesNumber;
    }

    /**
     * @return int
     */
    public function getSeriesId()
    {
        return $this->seriesId;
    }

    /**
     * @return int
     */
    public function getSeriesNumber()
    {
        return $this->seriesNumber;
    }

    /**
     * @return null|string
     */
    public function getPackageNumber()
    {
        return $this->packageNumber;
    }

    /**
     * @param null|string $packageNumber
     */
    public function setPackageNumber($packageNumber)
    {
        $this->packageNumber = $packageNumber;
    }
}
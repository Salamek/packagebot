<?php
/**
 * Copyright (C) 2016 Adam Schubert <adam.schubert@sg1-game.net>.
 */

namespace Salamek\PackageBot\Model;


class SeriesNumberInfo
{
    /** @var integer */
    private $seriesId;

    /** @var integer */
    private $seriesNumber;

    /**
     * SeriesNumberInfo constructor.
     * @param int $seriesId
     * @param int $seriesNumber
     */
    public function __construct($seriesNumber, $seriesId = null)
    {
        $this->seriesId = $seriesId;
        $this->seriesNumber = $seriesNumber;
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
}
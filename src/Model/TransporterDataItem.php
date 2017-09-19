<?php
/**
 * Created by PhpStorm.
 * User: sadam
 * Date: 19.9.17
 * Time: 18:33
 */

namespace Salamek\PackageBot\Model;

/**
 * Class TransporterDataItem
 * @package Salamek\PackageBot\Model
 */
class TransporterDataItem implements ITransporterDataItem
{
    /** @var string */
    private $transporter;

    /** @var string */
    private $identifier;

    /** @var string */
    private $name;

    /** @var mixed */
    private $data;

    /** @var \DateTimeInterface */
    private $date;

    /** @var null|ITransporterDataGroup */
    private $group;

    /**
     * TransporterDataItem constructor.
     * @param string $transporter
     * @param string $identifier
     * @param string $name
     * @param mixed $data
     * @param \DateTimeInterface|null $date
     * @param ITransporterDataGroup|null $group
     */
    public function __construct($transporter, $identifier, $name, $data, \DateTimeInterface $date = null, ITransporterDataGroup $group = null)
    {
        $this->transporter = $transporter;
        $this->identifier = $identifier;
        $this->name = $name;
        $this->data = $data;
        $this->date = $date;
        $this->group = $group;
    }

    /**
     * @return string
     */
    public function getTransporter()
    {
        return $this->transporter;
    }

    /**
     * @param string $transporter
     */
    public function setTransporter($transporter)
    {
        $this->transporter = $transporter;
    }

    /**
     * @return string
     */
    public function getIdentifier()
    {
        return $this->identifier;
    }

    /**
     * @param string $identifier
     */
    public function setIdentifier($identifier)
    {
        $this->identifier = $identifier;
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @return null|ITransporterDataGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @param null|ITransporterDataGroup $group
     */
    public function setGroup(ITransporterDataGroup $group = null)
    {
        $this->group = $group;
    }

    /**
     * @return \DateTimeInterface
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * @param \DateTimeInterface|null $date
     */
    public function setDate(\DateTimeInterface $date = null)
    {
        $this->date = $date;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }
}
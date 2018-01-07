<?php
/**
 * Created by PhpStorm.
 * User: sadam
 * Date: 19.9.17
 * Time: 18:33
 */

namespace Salamek\PackageBot\Model;
use Nette\SmartObject;

/**
 * Class TransporterDataGroup
 * @package Salamek\PackageBot\Model
 */
class TransporterDataGroup implements ITransporterDataGroup
{
    use SmartObject;
    
    /** @var string */
    private $transporter;

    /** @var string */
    private $name;

    /** @var string */
    private $identifier;

    /** @var null|ITransporterDataGroup */
    private $parent;
    
    /**
     * ITransporterDataGroup constructor.
     * @param string $transporter
     * @param string $name
     * @param string $identifier
     * @param ITransporterDataGroup|null $parent
     */
    public function __construct($transporter, $name, $identifier, ITransporterDataGroup $parent = null)
    {
        $this->transporter = $transporter;
        $this->name = $name;
        $this->identifier = $identifier;
        $this->parent = $parent;
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
     * @return null|ITransporterDataGroup
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param null|ITransporterDataGroup $parent
     */
    public function setParent(ITransporterDataGroup $parent = null)
    {
        $this->parent = $parent;
    }
}
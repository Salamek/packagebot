<?php
/**
 * Created by PhpStorm.
 * User: sadam
 * Date: 19.9.17
 * Time: 18:33
 */

namespace Salamek\PackageBot\Model;


interface ITransporterDataGroup
{
    /**
     * ITransporterDataGroup constructor.
     * @param string $transporter
     * @param string $name
     * @param string $identifier
     * @param ITransporterDataGroup|null $parent
     */
    public function __construct($transporter, $name, $identifier, ITransporterDataGroup $parent = null);

    /**
     * @return string
     */
    public function getTransporter();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return ITransporterDataGroup
     */
    public function getParent();

    /**
     * @param ITransporterDataGroup $parent
     * @return void
     */
    public function setParent(ITransporterDataGroup $parent);

    /**
     * @param string $identifier
     * @return void
     */
    public function setIdentifier($identifier);

    /**
     * @param string $name
     * @return void
     */
    public function setName($name);

    /**
     * @param string $transporter
     * @return void
     */
    public function setTransporter($transporter);
}
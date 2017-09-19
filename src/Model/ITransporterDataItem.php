<?php
/**
 * Created by PhpStorm.
 * User: sadam
 * Date: 19.9.17
 * Time: 18:33
 */

namespace Salamek\PackageBot\Model;


interface ITransporterDataItem
{
    /**
     * ITransporterDataItem constructor.
     * @param string $transporter
     * @param string $identifier
     * @param string $name
     * @param mixed $data
     * @param \DateTimeInterface|null $date
     * @param ITransporterDataGroup|null $group
     */
    public function __construct($transporter, $identifier, $name, $data, \DateTimeInterface $date = null, ITransporterDataGroup $group = null);

    /**
     * @return string
     */
    public function getTransporter();

    /**
     * @return string
     */
    public function getIdentifier();

    /**
     * @return mixed
     */
    public function getData();

    /**
     * @return ITransporterDataGroup|null
     */
    public function getGroup();

    /**
     * @return \DateTimeInterface|null
     */
    public function getDate();

    /**
     * @param string $transporter
     * @return void
     */
    public function setTransporter($transporter);

    /**
     * @param string $identifier
     * @return void
     */
    public function setIdentifier($identifier);

    /**
     * @param mixed $data
     * @return void
     */
    public function setData($data);

    /**
     * @param \DateTimeInterface|null $date
     * @return void
     */
    public function setDate(\DateTimeInterface $date = null);

    /**
     * @param ITransporterDataGroup|null $group
     * @return void
     */
    public function setGroup(ITransporterDataGroup $group = null);
}
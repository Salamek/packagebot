<?php
/**
 * Created by PhpStorm.
 * User: sadam
 * Date: 19.9.17
 * Time: 16:18
 */

namespace Salamek\PackageBot\Storage;


use Salamek\PackageBot\Model\ITransporterDataGroup;

interface ITransporterDataGroupStorage
{
    /**
     * @param ITransporterDataGroup $dataGroup
     * @return ITransporterDataGroup
     */
    public function create(ITransporterDataGroup $dataGroup);

    /**
     * @param array $findBy
     * @param array $orderBy
     * @return ITransporterDataGroup
     */
    public function findOneBy(array $findBy, array $orderBy = []);

    /**
     * @param array $findBy
     * @param array $orderBy
     * @return ITransporterDataGroup[]
     */
    public function findBy(array $findBy, array $orderBy = []);

    /**
     * @param array $findBy
     * @return mixed
     */
    public function deleteBy(array $findBy);
}
<?php
/**
 * Created by PhpStorm.
 * User: sadam
 * Date: 19.9.17
 * Time: 16:18
 */

namespace Salamek\PackageBot\Storage;

use Salamek\PackageBot\Model\ITransporterDataItem;

interface ITransporterDataItemStorage
{
    /**
     * @param ITransporterDataItem $dataItem
     * @return ITransporterDataItem
     */
    public function create(ITransporterDataItem $dataItem);

    /**
     * @param array $findBy
     * @param array $orderBy
     * @return ITransporterDataItem
     */
    public function findOneBy(array $findBy, array $orderBy = []);

    /**
     * @param array $findBy
     * @param array $orderBy
     * @return ITransporterDataItem[]
     */
    public function findBy(array $findBy, array $orderBy = []);

    /**
     * @param array $findBy
     * @return mixed
     */
    public function deleteBy(array $findBy);
}
<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * Class AbstractObserver
 * @package Aitoc\OrdersExportImport\Observer
 */
abstract class AbstractObserver implements ObserverInterface
{
    use \Aitoc\OrdersExportImport\Traits\Additional;

    /**
     * @var \Aitoc\OrdersExportImport\Model\Profile
     */
    public $profile;

    /**
     * ExportCheckout constructor.
     * @param \Aitoc\OrdersExportImport\Model\Profile $profile
     */
    public function __construct(
        \Aitoc\OrdersExportImport\Model\Profile $profile
    ) {
        $this->profile = $profile;
    }

    /**
     * @param $orderId
     * @param int $flag_auto
     */
    public function getProfiles($orderId, $flag_auto = 1)
    {
        $collection = $this->profile->getCollection()->addFieldToFilter('flag_auto', $flag_auto);
        if ($collection->count()) {
            $params = ['order_id' => $orderId];
            foreach ($this->partCollection($collection) as $value) {
                $this->addExport($value, $params);
            }
        }
    }

    /**
     * @param \Aitoc\OrdersExportImport\Api\Data\ProfileInterface $profile
     * @param $params
     */
    public function addExport(\Aitoc\OrdersExportImport\Api\Data\ProfileInterface $profile, $params)
    {
        $config = $profile->getUnsConfig();
        $export = \Magento\Framework\App\ObjectManager::getInstance()->create('Aitoc\OrdersExportImport\Model\Export');
        $export->setProfileId($profile->getId());
        $export->setDt(date('Y-m-d H:i:s'));
        $export->setSerializedConfig(serialize($params));
        $export->setIsCron($config['export_type']);
        $export->setTypeFile(0);
        $export->setFileName('order_export_' . $config['prefix'] . $profile->getId() . date('YmdHis') . $profile->getTypeFile());
        if (!count($export->getCollection()->addFieldToFilter('filename', $export->getFileName()))) {
            $export->save();
            $export->addStack();
            \Magento\Framework\App\ObjectManager::getInstance()->get('Aitoc\OrdersExportImport\Cron\Export')->execute();
        }

    }
}

<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Cron;

/**
 * Class Export
 *
 * @package Aitoc\OrdersExportImport\Cron
 */
class Export
{

    use \Aitoc\OrdersExportImport\Traits\Additional;
    /**
     * @var \Aitoc\OrdersExportImport\Model\Stack
     */
    private $stack;

    /**
     * @var \Aitoc\OrdersExportImport\Model\Export
     */
    private $export;

    /**
     * @var \Aitoc\OrdersExportImport\Model\Profile
     */
    private $profile;

    /**
     * @var \Aitoc\OrdersExportImport\Model\Converter\Export
     */
    private $convert;


    public $run;

    /**
     * Export constructor.
     * @param \Aitoc\OrdersExportImport\Model\Stack $stack
     * @param \Aitoc\OrdersExportImport\Model\Export $export
     * @param \Aitoc\OrdersExportImport\Model\Profile $profile
     * @param \Aitoc\OrdersExportImport\Model\Converter $convert
     */
    public function __construct(
        \Aitoc\OrdersExportImport\Model\Stack $stack,
        \Aitoc\OrdersExportImport\Model\Export $export,
        \Aitoc\OrdersExportImport\Model\Profile $profile,
        \Aitoc\OrdersExportImport\Model\Converter $convert
    ) {
        $this->stack   = $stack;
        $this->export  = $export;
        $this->profile = $profile;
        $this->convert = $convert;
        $this->run     = 0;
    }

    /**
     * Delete all product flat tables for not existing stores
     *
     * @return void
     */
    public function execute()
    {
        if (!$this->run) {
            $collection = $this->stack->getCollection()
                ->addFieldToFilter('status', 0)
                ->addFieldToFilter('cron_date', ['lteq' => date('Y-m-d H:i:s')]);
        } else {
            $collection = $this->stack->getCollection()
                ->addFieldToFilter('stack_id', $this->run);
        }
        if ($collection->getSize()) {
            $model  = $collection->getFirstItem();
            $export = $this->getExport()->load($model->getExportId());
            $model->setStatus(1);
            $model->save();
            $export->setStatus(1);
            $export->save();
            $profile = $this->getProfile()->load($export->getProfileId());
            $config  = $profile->getUnsConfig();
            if ($export->getIsCron()) {
                if ($config['export_cron'] > 0) {
                    $newExport = \Magento\Framework\App\ObjectManager::getInstance()->create('Aitoc\OrdersExportImport\Model\Export');
                    $newExport->setProfileId($profile->getId());
                    $newExport->setDt(date('Y-m-d H:i:s'));
                    $newExport->setSerializedConfig($export->getSerializedConfig());
                    $newExport->setIsCron($export->getIsCron());
                    $newExport->setTypeFile($export->getTypeFile());
                    $newExport->setOrdersCount(0);
                    if ($config['prefix']) {
                        $prefix = $config['prefix'];
                    } else {
                        $prefix = 'order_export';
                    }
                    $newExport->setFileName(
                        $prefix . '_' . $profile->getId() . date('YmdHis') . $profile->getTypeFile()
                    );

                    $newExport->save();
                    $newDate = $this->addDate($config['export_cron']);
                    $newExport->addStack($newDate);
                }
            }
            $converter = $this->getConvert();
            $converter->setStack($model->getId());
            $converter->setConfig(unserialize($profile->getConfig()));
            $converter->setParams(unserialize($export->getSerializedConfig()));
            if ($export->getIsCron() && !$export->getOrdersCount()) {
                if ($config['export_only_orders'] && !$config['export_clear']) {
                    $dateArray    = [];
                    $dateArray[0] = $this->subDate($config['export_cron']);
                    $dateArray[1] = $this->addDate(0);
                    $collection   = \Magento\Framework\App\ObjectManager::getInstance()
                        ->create('Magento\Sales\Model\Order')
                        ->getCollection()
                        ->addFieldToFilter('created_at', ['gt' => $dateArray[0]])
                        ->addFieldToFilter('created_at', ['lt' => $dateArray[1]]);
                    if ($collection->getSize()) {
                        $orders = [];
                        foreach ($collection as $item) {
                            $orders[] = $item->getId();
                        }
                        $params             = unserialize($export->getSerializedConfig());
                        $params['selected'] = $orders;
                        $converter->setParams($params);
                    } else {
                        $params['selected'] = 0;
                    }
                }
            } else {
                if ($export->getOrdersCount()) {
                    $export->setOrdersCount(0);
                }
            }
            if ($config['export_clear']) {
                $configs                 = unserialize($profile->getConfig());
                $configs['export_clear'] = 0;
                $profile->setConfig(serialize($configs));
                $profile->save();
            }
            $model->setPercent(10);
            $model->save();
            try {
                $converter->convert($export->getFilename());
                $model->setPercent(100);
                $model->save();
                $export->setStatus(2);
                $export->save();
                $model->delete();
            } catch (\Exception $e) {
                $model->setError($e->getMessage());
                $model->save();
            }
        }
    }

    /**
     * @return \Aitoc\OrdersExportImport\Model\Export
     */
    public function getExport()
    {
        return $this->export;
    }

    /**
     * @return \Aitoc\OrdersExportImport\Model\Profile
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * @return \Aitoc\OrdersExportImport\Model\Converter|\Aitoc\OrdersExportImport\Model\Converter\Export
     */
    public function getConvert()
    {
        return $this->convert;
    }

    public function setRun($number)
    {
        $this->run = $number;

        return $this;
    }
}

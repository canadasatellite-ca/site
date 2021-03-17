<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Cron;

/**
 * Class Import
 * @package Aitoc\OrdersExportImport\Cron
 */
class Import
{
    /**
     * @var \Aitoc\OrdersExportImport\Model\Export
     */
    private $import;

    /**
     * @var \Aitoc\OrdersExportImport\Model\Converter\Export
     */
    private $convert;

    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * Export constructor.
     *
     * @param \Aitoc\OrdersExportImport\Model\Stack $stack
     * @param \Aitoc\OrdersExportImport\Model\Export $export
     * @param \Aitoc\OrdersExportImport\Model\Profile $profile
     * @param \Aitoc\OrdersExportImport\Model\Converter $convert
     */
    public function __construct(
        \Aitoc\OrdersExportImport\Model\Import $import,
        \Aitoc\OrdersExportImport\Model\Import\Converter $convert,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->import         = $import;
        $this->convert        = $convert;
        $this->messageManager = $messageManager;
    }

    /**
     * Delete all product flat tables for not existing stores
     *
     * @return void
     */
    public function execute()
    {
        $block      = \Magento\Framework\App\ObjectManager::getInstance()
            ->get('Aitoc\OrdersExportImport\Block\Adminhtml\Import\Frame\Result');
        $collection = $this->import->getCollection()
            ->addFieldToFilter('status', 0);
        if ($collection->getSize()) {
            $model = $collection->getFirstItem();
            $model->setStatus(1);
            $model->save();
            $converter = $this->getConvert();
            $converter->setParams(unserialize($model->getSerializedConfig()));
            $converter->setBlockResult($block);
            try {
                $converter->convert($model->getFilename());
                $model->setError(null);
                $model->setStatus(2);
            } catch (\Exception $e) {
                $model->setStatus(1);
                $converter->getBlockResult()->addError(__($e->getMessage()));
                $model->setError($converter->getBlockResult()->getResponseJson());
            }
            $model->setError(__('Result Import:' . $converter->getBlockResult()->getMessages()));
            $model->save();
        }
    }

    /**
     * @return \Aitoc\OrdersExportImport\Model\Converter|\Aitoc\OrdersExportImport\Model\Converter\Export
     */
    public function getConvert()
    {
        return $this->convert;
    }
}

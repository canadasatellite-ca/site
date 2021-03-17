<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\OrdersExportImport\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Aitoc\OrdersExportImport\Model\Import;
use Magento\Framework\Component\ComponentRegistrar;
use Magento\Framework\Controller\ResultFactory;

/**
 * Class Validate
 * @package Aitoc\OrdersExportImport\Controller\Adminhtml\Import
 */
class Validate extends \Magento\Backend\App\Action
{
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::import';

    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    public $resultRawFactory;

    /**
     * @var \Magento\Framework\Filesystem\Directory\ReadFactory
     */
    public $readFactory;

    /**
     * @var \Magento\Framework\Component\ComponentRegistrar
     */
    public $componentRegistrar;

    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    public $fileFactory;

    public $resultFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory
     * @param ComponentRegistrar $componentRegistrar
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Magento\Framework\Filesystem\Directory\ReadFactory $readFactory,
        \Magento\Framework\Component\ComponentRegistrar $componentRegistrar
    ) {
        parent::__construct(
            $context
        );
        $this->fileFactory        = $fileFactory;
        $this->resultFactory      = $context->getResultFactory();
        $this->resultRawFactory   = $resultRawFactory;
        $this->readFactory        = $readFactory;
        $this->componentRegistrar = $componentRegistrar;
    }

    /**
     * Download sample file action
     *
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        $resultRaw = $this->resultRawFactory->create();
        $result    = [];
        if ($this->getRequest()->getParam('isAjax')) {
            $resultLayout = $this->resultFactory->create(ResultFactory::TYPE_LAYOUT);
            $resultBlock  = $resultLayout->getLayout()->getBlock('import.frame.result');
            $data         = $this->getRequest()->getPostValue();
            $data         = $this->scopeData($data);
            $model        = \Magento\Framework\App\ObjectManager::getInstance()
                ->get('Aitoc\OrdersExportImport\Model\Import\Validate');
            $model->setResultBlock($resultBlock);
            $model->validate($data);
            if ($model->getResultBlock()->getErrors()) {
                $resultBlock->addAction(
                    'show',
                    'import_validation_container'
                );
            } else {
                $resultBlock->addAction(
                    'hide',
                    'import_validation_container'
                );
                $resultBlock->addSuccess(
                    __('File is valid! To start import process press "Start Import" button'),
                    true
                );
            }
            $result = $model->getResultBlock()->getResponseJson();
        }
        $resultRaw = $this->resultRawFactory->create();
        $resultRaw->setContents($result);

        return $resultRaw;
    }

    public function scopeData($data)
    {
        $newData              = [];
        $newData['import_id'] = null;
        $filename             = '';
        if (count($data['file_name'])) {
            foreach ($data['file_name'] as $value) {
                $filename .= $value['path'] . $value['file'];
                if (next($data['file_name'])) {
                    $filename .= ';';
                }
            }
        }
        $newData['filename']          = $filename;
        $newData['status']            = 0;
        $newData['serialized_config'] = serialize($data);
        $newData['dt']                = date('Y-m-d H:i:s');

        return $newData;

    }
}

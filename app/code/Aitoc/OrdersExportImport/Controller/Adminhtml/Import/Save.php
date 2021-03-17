<?php
/**
 *
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\OrdersExportImport\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use Aitoc\OrdersExportImport\Model\Import;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\LocalizedException;

class Save extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::import';

    /**
     * @var DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @param Action\Context $context
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Action\Context $context,
        DataPersistorInterface $dataPersistor
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Save action
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        try {
            $data  = $this->getRequest()->getPostValue();
            $model = $this->_objectManager->create('Aitoc\OrdersExportImport\Model\Import');

            $data = $this->scopeData($data);
            $model->setData($data);
            $model->save();
        } catch (LocalizedException $e) {
            $this->messageManager->addError($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addException($e, __('Something went wrong while saving the import.'));
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param $data
     *
     * @return array
     */
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

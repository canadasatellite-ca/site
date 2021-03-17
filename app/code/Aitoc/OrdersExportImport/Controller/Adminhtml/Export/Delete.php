<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Controller\Adminhtml\Export;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;

/**
 * Class Delete
 * @package Aitoc\OrdersExportImport\Controller\Adminhtml\Profile
 */
class Delete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::manage';

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        // check if we know what should be deleted
        $id = $this->getRequest()->getParam('export_id');
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($id) {
            try {
                // init model and delete
                $model = $this->_objectManager->create('Aitoc\OrdersExportImport\Model\Export')->load($id);
                if ($model->getStatus()) {
                    $profile = $this->_objectManager->create('Aitoc\OrdersExportImport\Model\Profile')
                        ->load($model->getProfileId());
                    $config  = $profile->getUnsConfig();
                    $path    = $this->_objectManager->create('Magento\Framework\Filesystem')
                        ->getDirectoryWrite(DirectoryList::ROOT)
                        ->getAbsolutePath('/');
                    unlink(
                        $path
                        . $config['path_local']
                        . "/"
                        . $model->getFilename()
                    );
                }
                $this->scopeStacks($id);
                $model->delete();
                $this->messageManager->addSuccess(__('The export has been deleted.'));

                return $resultRedirect->setPath('*/*/index');
            } catch (\Exception $e) {
                $this->messageManager->addError($e->getMessage());

                return $resultRedirect->setPath('*/*/index');
            }
        }
        $this->messageManager->addError(__('We can\'t find a export to delete.'));

        return $resultRedirect->setPath('*/*/');
    }

    public function scopeStacks($exportId)
    {
        $collection = $this->_objectManager
            ->create('Aitoc\OrdersExportImport\Model\Stack')
            ->getCollection()
            ->addFieldToFilter('export_id', $exportId);
        if ($collection->getSize()) {
            foreach ($collection as $item) {
                $item->delete();
            }
        }
    }
}

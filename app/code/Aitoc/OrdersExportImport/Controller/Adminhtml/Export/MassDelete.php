<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Controller\Adminhtml\Export;

use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Aitoc\OrdersExportImport\Model\ResourceModel\Export\CollectionFactory;

/**
 * Class MassDelete
 * @package Aitoc\OrdersExportImport\Controller\Adminhtml\Export
 */
class MassDelete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Aitoc_OrdersExportImport::manage';

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(Context $context, Filter $filter, CollectionFactory $collectionFactory)
    {
        $this->filter            = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    /**
     * Delete action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $collection     = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();
        foreach ($collection as $export) {
            if ($export->getStatus()) {
                $profile = $this->_objectManager->create('Aitoc\OrdersExportImport\Model\Profile')
                    ->load($export->getProfileId());
                $config  = $profile->getUnsConfig();
                $path    = $this->_objectManager->create('Magento\Framework\Filesystem')
                    ->getDirectoryWrite(DirectoryList::ROOT)
                    ->getAbsolutePath('/');
                unlink(
                    $path
                    . $config['path_local']
                    . "/"
                    . $export->getFilename()
                );
            }
            $this->scopeStacks($export->getId());
            $export->delete();
        }

        $this->messageManager->addSuccess(__('A total of %1 record(s) have been deleted.', $collectionSize));

        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

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

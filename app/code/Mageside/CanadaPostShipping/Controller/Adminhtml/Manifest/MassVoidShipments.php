<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Controller\Adminhtml\Manifest;

use Magento\Framework\Controller\ResultFactory;

class MassVoidShipments extends \Magento\Backend\App\Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mageside_CanadaPostShipping::mageside_canadapost_shipping';

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    private $_filter;

    /**
     * @var object
     */
    private $_collectionFactory;

    /**
     * @var \Mageside\CanadaPostShipping\Model\Service\Manifest
     */
    private $_manifestService;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Mageside\CanadaPostShipping\Model\ResourceModel\Shipment\CollectionFactory $collectionFactory
     * @param \Mageside\CanadaPostShipping\Model\Service\Manifest $manifestService
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Mageside\CanadaPostShipping\Model\ResourceModel\Shipment\CollectionFactory $collectionFactory,
        \Mageside\CanadaPostShipping\Model\Service\Manifest $manifestService
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->_manifestService = $manifestService;

        parent::__construct($context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException|\Exception
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        $response = null;
        try {
            $collection = $this->_filter->getCollection($this->_collectionFactory->create());
            $response = $this->_manifestService->voidShipments($collection);
            if (!empty($response['success'])) {
                $this->messageManager->addSuccessMessage(
                    __('Processed shipment ids: ') . implode(',', $response['success'])
                );
            } else {
                $this->messageManager->addSuccessMessage(__('No shipments was processed.'));
            }
            if (!empty($response['error'])) {
                foreach ($response['error'] as $error) {
                    $this->messageManager->addErrorMessage(
                        __('Shipment id: %1. Error Msg: %2', [$error['id'], $error['message']])
                    );
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        if (!empty($response['manifestId'])) {
            return $resultRedirect->setPath('canadapost/manifest/view', ['manifest_id' => (int)$response['manifestId']]);
        } else {
            return $resultRedirect->setPath('canadapost/manifest/index');
        }
    }
}

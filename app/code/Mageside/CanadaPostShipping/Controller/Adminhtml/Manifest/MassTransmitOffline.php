<?php
/**
 * Copyright © Mageside. All rights reserved.
 * See MS-LICENSE.txt for license details.
 */
namespace Mageside\CanadaPostShipping\Controller\Adminhtml\Manifest;

use Magento\Framework\Controller\ResultFactory;

class MassTransmitOffline extends \Magento\Backend\App\Action
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
     * @var \Mageside\CanadaPostShipping\Model\Service\Transmit
     */
    private $_transmitService;

    /**
     * Transmit constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Mageside\CanadaPostShipping\Model\ResourceModel\Manifest\CollectionFactory $collectionFactory
     * @param \Mageside\CanadaPostShipping\Model\Service\Transmit $transmitService
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Mageside\CanadaPostShipping\Model\ResourceModel\Manifest\CollectionFactory $collectionFactory,
        \Mageside\CanadaPostShipping\Model\Service\Transmit $transmitService
    ) {
        $this->_filter = $filter;
        $this->_collectionFactory = $collectionFactory;
        $this->_transmitService = $transmitService;

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

        try {
            $collection = $this->_filter->getCollection($this->_collectionFactory->create());
            $response = $this->_transmitService->transmitShipmentsOffline($collection);
            if (!empty($response['success'])) {
                $this->messageManager->addSuccessMessage(
                    __('Processed manifest ids: ') . implode(',', $response['success'])
                );
            } else {
                $this->messageManager->addSuccessMessage(__('No manifests was processed.'));
            }
            if (!empty($response['error'])) {
                foreach ($response['error'] as $error) {
                    $this->messageManager->addErrorMessage(
                        __('Manifest id: %1. Error Msg: %2', [$error['id'], $error['message']])
                    );
                }
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('canadapost/manifest/index');
    }
}

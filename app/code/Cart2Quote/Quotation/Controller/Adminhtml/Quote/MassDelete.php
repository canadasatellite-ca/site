<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote;

//Magento 2.2 fix
if (!interface_exists('Magento\Framework\App\Action\HttpPostActionInterface')) {
    class_alias(
        'Cart2Quote\Quotation\Legacy\Magento\Framework\App\Action\HttpPostActionInterface',
        'Magento\Framework\App\Action\HttpPostActionInterface'
    );
}

/**
 * Class MassDelete
 */
class MassDelete extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * Acl resource
     */
    const ADMIN_RESOURCE = 'Cart2Quote_Quotation::massDelete';

    /**
     * @var \Magento\Ui\Component\MassAction\Filter
     */
    protected $filter;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory
     */
    protected $collectionFactory;

    /**
     * @var string
     */
    protected $redirectUrl = '*/*/';

    /**
     * MassDelete constructor.
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());

            $countDeletedQuotes = 0;
            /**
             * @var \Cart2Quote\Quotation\Model\Quote $quote
             */
            foreach ($collection->getItems() as $quote) {
                $quote->setStatus(\Cart2Quote\Quotation\Model\Quote\Status::STATUS_CANCELED);
                $quote->setState(\Cart2Quote\Quotation\Model\Quote\Status::STATE_CANCELED);
                $quote->setIsQuote(\Cart2Quote\Quotation\Model\Quote::IS_QUOTE_DELETED);
                $quote->save();
                $countDeletedQuotes++;
            }
            $countNonDeletedQuotes = $collection->count() - $countDeletedQuotes;

            if ($countNonDeletedQuotes && $countDeletedQuotes) {
                $this->messageManager->addErrorMessage(
                    __('%1 quote(s) cannot be deleted.', $countNonDeletedQuotes)
                );
            } elseif ($countNonDeletedQuotes) {
                $this->messageManager->addErrorMessage(__('You cannot delete the quote(s).'));
            }

            if ($countDeletedQuotes) {
                $this->messageManager->addSuccessMessage(__('We deleted %1 quote(s).', $countDeletedQuotes));
            }
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath($this->getComponentRefererUrl());

            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

            return $resultRedirect->setPath($this->redirectUrl);
        }
    }

    /**
     * Return component referrer url
     *
     * @return null|string
     */
    protected function getComponentRefererUrl()
    {
        return $this->filter->getComponentRefererUrl() ?: 'quotation/quote/index';
    }
}

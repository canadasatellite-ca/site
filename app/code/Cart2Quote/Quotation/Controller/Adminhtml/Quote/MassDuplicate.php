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
 * Class MassDuplicate
 *
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class MassDuplicate extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpPostActionInterface
{
    /**
     * Acl resource
     */
    const ADMIN_RESOURCE = 'Cart2Quote_Quotation::create';

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
     * @var \Cart2Quote\Quotation\Helper\Cloning
     */
    private $cloneHelper;

    /**
     * MassDuplicate constructor.
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory
     * @param \Cart2Quote\Quotation\Helper\Cloning $cloneHelper
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     */
    public function __construct(
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory,
        \Cart2Quote\Quotation\Helper\Cloning $cloneHelper,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Ui\Component\MassAction\Filter $filter
    ) {
        parent::__construct($context);
        $this->filter = $filter;
        $this->cloneHelper = $cloneHelper;
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

            $countDuplicatedQuote = 0;
            foreach ($collection->getItems() as $quote) {
                $duplicatedQuote = $this->cloneHelper->cloneQuote($quote);
                if (!$duplicatedQuote->getId()) {
                    continue;
                }
                $countDuplicatedQuote++;
            }
            $countNonDuplicatedQuote = $collection->count() - $countDuplicatedQuote;

            if ($countNonDuplicatedQuote && $countDuplicatedQuote) {
                $this->messageManager->addErrorMessage(
                    __('%1 quote(s) cannot be duplicated.', $countNonDuplicatedQuote)
                );
            } elseif ($countNonDuplicatedQuote) {
                $this->messageManager->addErrorMessage(__('You cannot duplicate the quote(s).'));
            }

            if ($countDuplicatedQuote) {
                $this->messageManager->addSuccessMessage(__('We duplicated %1 quote(s).', $countDuplicatedQuote));
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

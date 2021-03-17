<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote;

//Magento 2.2 fix
if (!interface_exists('Magento\Framework\App\Action\HttpGetActionInterface')) {
    class_alias(
        'Cart2Quote\Quotation\Legacy\Magento\Framework\App\Action\HttpGetActionInterface',
        'Magento\Framework\App\Action\HttpGetActionInterface'
    );
}

/**
 * Class MassDuplicateAssignCustomer
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class MassDuplicateAssignCustomer extends \Magento\Backend\App\Action implements \Magento\Framework\App\Action\HttpGetActionInterface
{
    /**
     * Acl resource
     */
    const ADMIN_RESOURCE = 'Cart2Quote_Quotation::create';

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
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    private $dataHelper;

    /**
     * @var \Magento\Framework\Serialize\Serializer\Json
     */
    private $jsonHelper;

    /**
     * MassDuplicateAssignCustomer constructor.
     *
     * @param \Cart2Quote\Quotation\Helper\Data $dataHelper
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory
     * @param \Cart2Quote\Quotation\Helper\Cloning $cloneHelper
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     * @param \Magento\Framework\Serialize\Serializer\Json $jsonHelper
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data $dataHelper,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote\CollectionFactory $collectionFactory,
        \Cart2Quote\Quotation\Helper\Cloning $cloneHelper,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Framework\Serialize\Serializer\Json $jsonHelper
    ) {
        parent::__construct($context);
        $this->cloneHelper = $cloneHelper;
        $this->collectionFactory = $collectionFactory;
        $this->customerRepository = $customerRepository;
        $this->dataHelper = $dataHelper;
        $this->jsonHelper = $jsonHelper;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $collection = $this->collectionFactory->create();
            $quoteIds = $this->jsonHelper->unserialize($this->_getSession()->getDuplicateQuoteIds());
            $collection->addFieldToFilter(
                $collection->getIdFieldName(),
                ['in' => $quoteIds]
            );

            $customerId = $this->getRequest()->getParam('customer');
            $customer = $this->customerRepository->getById($customerId);

            $countDuplicatedQuote = 0;
            foreach ($collection->getItems() as $quote) {
                $duplicatedQuote = $this->cloneHelper->cloneQuote($quote);

                $duplicatedQuote->assignCustomer($customer);
                $duplicatedQuote->saveQuote();
                if (!$duplicatedQuote->getId()) {
                    continue;
                }
                $countDuplicatedQuote++;
            }
            $countNonDuplicatedQuote = $collection->count() - $countDuplicatedQuote;

            if ($countNonDuplicatedQuote && $countDuplicatedQuote) {
                $this->messageManager->addErrorMessage(
                    __(
                        '%1 quote(s) cannot be duplicated and assigned to customer %2.',
                        $countNonDuplicatedQuote,
                        $this->dataHelper->getCustomerFullname($customer)
                    )
                );
            } elseif ($countNonDuplicatedQuote) {
                $this->messageManager->addErrorMessage(
                    __(
                        'You cannot duplicate the quote(s) and assign to customer %1.',
                        $this->dataHelper->getCustomerFullname($customer)
                    )
                );
            }

            if ($countDuplicatedQuote) {
                $this->messageManager->addSuccessMessage(
                    __(
                        'We duplicated %1 quote(s) and assigned to customer %2.',
                        $countDuplicatedQuote,
                        $this->dataHelper->getCustomerFullname($customer)
                    )
                );
            }
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('quotation/quote/index');

            $this->_getSession()->setDuplicateQuoteIds(null);
            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

            $this->_getSession()->setDuplicateQuoteIds(null);
            return $resultRedirect->setPath($this->redirectUrl);
        }
    }
}

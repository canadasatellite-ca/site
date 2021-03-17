<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Adminhtml\Quote;

/**
 * Class DuplicateAssignCustomer
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Quote
 */
class DuplicateAssignCustomer extends \Magento\Backend\App\Action
{
    /**
     * Acl resource
     */
    const ADMIN_RESOURCE = 'Cart2Quote_Quotation::create';

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
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Quote
     */
    private $quoteResourceModel;
    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory
     */
    private $quoteFactory;

    /**
     * DuplicateAssignCustomer constructor.
     *
     * @param \Cart2Quote\Quotation\Helper\Data                 $dataHelper
     * @param \Cart2Quote\Quotation\Model\QuoteFactory          $quoteFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Quote   $quoteResourceModel
     * @param \Cart2Quote\Quotation\Helper\Cloning              $cloneHelper
     * @param \Magento\Backend\App\Action\Context               $context
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        \Cart2Quote\Quotation\Helper\Data $dataHelper,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Cart2Quote\Quotation\Model\ResourceModel\Quote $quoteResourceModel,
        \Cart2Quote\Quotation\Helper\Cloning $cloneHelper,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($context);
        $this->cloneHelper = $cloneHelper;
        $this->customerRepository = $customerRepository;
        $this->dataHelper = $dataHelper;
        $this->quoteResourceModel = $quoteResourceModel;
        $this->quoteFactory = $quoteFactory;
    }

    /**
     * Execute action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        try {
            $quote = $this->quoteFactory->create();
            $quoteId = $this->getRequest()->getPost('entity_id');
            if (empty($quoteId)) {
                throw new \Magento\Framework\Exception\LocalizedException(__('No quote ID provided'));
            }
            $this->quoteResourceModel->load($quote, $quoteId);

            if (!$quote->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __('Quote with ID: %1 does no exist', $quoteId)
                );
            }
            $countDuplicatedQuote = 0;
            $customers = $this->getRequest()->getPost('customers', []);
            foreach ($customers as $customer) {
                $customer = $this->customerRepository->getById($customer['entity_id']);
                $duplicatedQuote = $this->cloneHelper->cloneQuote($quote);
                $duplicatedQuote->assignCustomer($customer);
                $duplicatedQuote->saveQuote();
                if (!$duplicatedQuote->getId() && $duplicatedQuote->getCustomerId() !== $customer->getId()) {
                    $this->messageManager->addErrorMessage(
                        __(
                            'Quote cannot be duplicated and assign to customer %2.',
                            $this->dataHelper->getCustomerFullname($customer)
                        )
                    );
                    continue;
                }
                $countDuplicatedQuote++;
            }

            $countNonDuplicatedQuote = count($customers) - $countDuplicatedQuote;

            if ($countNonDuplicatedQuote) {
                $this->messageManager->addErrorMessage(
                    __(
                        'You cannot duplicate and assign to %1 selected customers',
                        $countNonDuplicatedQuote
                    )
                );
            }

            if ($countDuplicatedQuote) {
                $this->messageManager->addSuccessMessage(
                    __(
                        'We duplicated the quote and assigned to %1  selected customers.',
                        $countDuplicatedQuote
                    )
                );
            }
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $quoteId]);

            return $resultRedirect;
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultFactory->create(\Magento\Framework\Controller\ResultFactory::TYPE_REDIRECT);

            $resultRedirect->setPath('quotation/quote/view', ['quote_id' => $quoteId]);
        }
    }
}

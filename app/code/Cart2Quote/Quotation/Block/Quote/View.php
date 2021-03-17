<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Quote;

use Magento\Customer\Model\Context;

/**
 * Quote view block
 */
class View extends \Magento\Framework\View\Element\Template
{
    /**
     * @var string
     */
    protected $_template = 'quote/view.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection
     */
    protected $_statusCollection;

    /**
     * Data Helper
     *
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationHelper;

    /**
     * @var \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     */
    protected $orderCollectionFactory;

    /**
     * @var \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory
     */
    protected $quoteCollectionFactory;

    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $deploymentConfig;

    /**
     * View constructor
     *
     * @param \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory
     * @param \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Http\Context $httpContext
     * @param \Cart2Quote\Quotation\Helper\Data $quotationHelper
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection
     * @param \Magento\Framework\App\DeploymentConfig $deploymentConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Quote\Model\ResourceModel\Quote\CollectionFactory $quoteCollectionFactory,
        \Magento\Sales\Model\ResourceModel\Order\CollectionFactory $orderCollectionFactory,
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Http\Context $httpContext,
        \Cart2Quote\Quotation\Helper\Data $quotationHelper,
        \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig,
        array $data = []
    ) {
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->_coreRegistry = $registry;
        $this->httpContext = $httpContext;
        $this->quotationHelper = $quotationHelper;
        $this->_statusCollection = $statusCollection;
        $this->_isScopePrivate = true;
        $this->deploymentConfig = $deploymentConfig;

        parent::__construct(
            $context,
            $data
        );
    }

    /**
     * Get payment info in html
     *
     * @return string
     */
    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }

    /**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return $this->getUrl('*/*/history');
        }

        return $this->getUrl('*/*/form');
    }

    /**
     * Return back title for logged in and guest users
     *
     * @return \Magento\Framework\Phrase
     */
    public function getBackTitle()
    {
        if ($this->httpContext->getValue(Context::CONTEXT_AUTH)) {
            return __('Back to My Quote');
        }

        return __('View Another Quote');
    }

    /**
     * Return the data for the button on the customer dashboard quote detail view
     *
     * @return array
     */
    public function getCheckoutActionButtonData()
    {
        $status = $this->getQuoteStatus();
        $flag = $status->getFrontendButtonHtmlFlag();
        $buttonLabel = $this->getFrontendButtonLabel($status);
        $data = [
            'flag' => $flag,
            'label' => $buttonLabel
        ];

        return $data;
    }

    /**
     * Prepare layout
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        $this->pageConfig->getTitle()->set(__('Quote # %1', $this->getQuote()->getIncrementId()));
    }

    /**
     * Return back url for logged in and guest users
     *
     * @return string
     */
    public function getAcceptUrl()
    {
        $acceptWithoutCheckout = $this->isCheckoutDisabled();

        if ($acceptWithoutCheckout) {
            return $this->getUrl(
                'quotation/quote_checkout/acceptwithoutcheckout',
                ['quote_id' => $this->getQuote()->getId()]
            );
        }

        return $this->getUrl(
            'quotation/quote_checkout/accept',
            ['quote_id' => $this->getQuote()->getId()]
        );
    }

    /**
     * Retrieve current quote model instance
     *
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    public function getQuote()
    {
        return $this->_coreRegistry->registry('current_quote');
    }

    /**
     * Returns true if the button can be shown in quote view page in the customer dashboard
     *
     * @return bool
     */
    public function showCheckoutButton()
    {
        $button  = true;
        $status = $this->getQuoteStatus();
        if ($status->statusIsAccepted() && $this->isCheckoutDisabled()) {
            $button = false;
        }

        return $button;
    }

    /**
     * If checkout is disabled after accepting quotes, this method returns true
     *
     * @return bool
     */
    protected function isCheckoutDisabled()
    {
        return $this->quotationHelper->isCheckoutDisabled();
    }

    /**
     * Get the Label for the button on the customer dashboard quote detail view
     *
     * @param string $status
     * @return \Magento\Framework\Phrase|string
     */
    protected function getFrontendButtonLabel($status)
    {
        $buttonLabel = __('Accept & Checkout');
        if ($this->isCheckoutDisabled()) {
            $buttonLabel = __('Accept Quotation');
        } elseif ($status->statusIsAccepted()) {
            $buttonLabel = __('Checkout');
        }

        return $buttonLabel;
    }

    /**
     * Returns the status of a given quote.
     *
     * @return \Magento\Framework\DataObject
     */
    protected function getQuoteStatus()
    {
        $quote = $this->getQuote();
        $status = $this->_statusCollection->getItemByColumnValue('status', $quote->getStatus());

        return $status;
    }

    /**
     * Getter for the quotation helper
     *
     * @return \Cart2Quote\Quotation\Helper\Data
     */
    public function getQuotationHelper()
    {

        return $this->quotationHelper;
    }

    /**
     * @param int $quoteId
     * @return array
     */
    public function getOrderViewUrlByQuotationId($quoteId)
    {
        $resource = $this->quoteCollectionFactory->create()->getResource();
        $tableName = $resource->getTable('quote');
        $connectionConfig = $resource->getConnection()->getConfig();
        $dbName = $connectionConfig['dbname'];
        if ($dbName) {
            $tableName = sprintf("%s.%s", $dbName, $tableName);
        }

        $orderIds = $this->orderCollectionFactory
            ->create()
            ->addFieldToSelect(
                [
                    'increment_id',
                    'order_id' => 'entity_id'
                ]
            )->addFieldToFilter(
                'linked_quotation_id',
                $quoteId
            )->join(
                ['quote' => $tableName],
                'main_table.quote_id = quote.entity_id',
                'linked_quotation_id'
            )->getItems();

        $orderUrls = [];
        foreach ($orderIds as $val) {
            $orderUrls[$val->getIncrementId()] = $this->getUrl(
                'sales/order/view',
                [
                    'order_id' => $val->getOrderId()
                ]
            );
        }

        return $orderUrls;
    }
}

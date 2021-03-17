<?php
/**
 * Cart2Quote
 */

namespace MageSuper\Casat\Controller\Adminhtml;

/**
 * Adminhtml quotation quotes controller
 */
abstract class PurchaseOrder extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;
    /**
     * Array of actions which can be processed without secret key validation
     * @var string[]
     */
    protected $_publicActions = ['view', 'index'];
    /**
     * Core registry
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry = null;
    /**
     * @var \Magento\Framework\App\Response\Http\FileFactory
     */
    protected $_fileFactory;
    /**
     * @var \Magento\Framework\Translate\InlineInterface
     */
    protected $_translateInline;
    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $_helperData;
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $resultJsonFactory;
    /**
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
    protected $resultLayoutFactory;
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\QuoteFactory $poFactory
     */
    protected $poFactory;
    /**
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    protected $_currentPo;
    /**
     * @var \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection
     */
    protected $_statusCollection;
    protected $_supplierRepository;

    /**
     * Quote constructor.
     * @param \Magento\Framework\Escaper $escaper
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Framework\App\Response\Http\FileFactory $fileFactory
     * @param \Magento\Framework\Translate\InlineInterface $translateInline
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Cart2Quote\Quotation\Helper\Data $helperData
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $poFactory
     * @param \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection
     */
    public function __construct(
        \Magento\Framework\Escaper $escaper,
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Framework\App\Response\Http\FileFactory $fileFactory,
        \Magento\Framework\Translate\InlineInterface $translateInline,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Magento\Framework\Controller\Result\RawFactory $resultRawFactory,
        \Cart2Quote\Quotation\Helper\Data $helperData,
        \Magestore\PurchaseOrderSuccess\Model\PurchaseOrderFactory $poFactory,
        \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository,
        \Cart2Quote\Quotation\Model\ResourceModel\Status\Collection $statusCollection
    ) {
        $this->escaper = $escaper;
        $this->_coreRegistry = $coreRegistry;
        $this->_fileFactory = $fileFactory;
        $this->_translateInline = $translateInline;
        $this->resultPageFactory = $resultPageFactory;
        $this->resultJsonFactory = $resultJsonFactory;
        $this->resultLayoutFactory = $resultLayoutFactory;
        $this->resultRawFactory = $resultRawFactory;
        $this->_helperData = $helperData;
        $this->poFactory = $poFactory;
        $this->_statusCollection = $statusCollection;
        $this->_supplierRepository = $supplierRepository;
        parent::__construct($context);
    }

    /**
     * Init layout, menu and breadcrumb
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function _initAction()
    {
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Cart2Quote_Quotation::quotation_quote');
        $resultPage->addBreadcrumb(__('Quotation'), __('Quotation'));
        $resultPage->addBreadcrumb(__('Quotes'), __('Quotes'));

        return $resultPage;
    }

    /**
     * Initialize quote model instance
     * @return \Cart2Quote\Quotation\Model\Quote|false
     */
    protected function _initPurchaseOrder()
    {
        $id = $this->getRequest()->getParam('purchase_id');
        if (!$id) {
            $this->messageManager->addError(__('ID is not provided.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);
            return;
        }

        $this->_currentPo = $this->poFactory->create()->load($id);

        if (!$this->_currentPo->getId()) {
            $this->messageManager->addError(__('This quote no longer exists.'));
            $this->_actionFlag->set('', self::FLAG_NO_DISPATCH, true);

            return false;
        }

        $supplier = $this->_supplierRepository->getById($this->_currentPo->getSupplierId());
        $this->_coreRegistry->register('current_purchase_order_supplier', $supplier);

        $this->_coreRegistry->unregister('current_purchase_order');
        $this->_coreRegistry->register('current_purchase_order', $this->_currentPo);

        return $this->_currentPo;
    }

    /**
     * Retrieve session object
     * @return \Magento\Backend\Model\Session\Quote
     */
    protected function _getSession()
    {
        return $this->_objectManager->get('Magento\Backend\Model\Session\Quote');
    }

    /**
     * Acl check for admin
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Cart2Quote_Quotation::quotes');
    }

    /**
     * Quotes grid
     * @return null|\Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        return null;
    }

    /**
     * Initialize quote creation session data
     * @return $this
     */
    protected function _initSession()
    {
        /**
         * Identify quote
         */
        if ($quoteId = $this->getRequest()->getParam('quote_id')) {
            $this->_getSession()->setQuoteId((int)$quoteId);
        } else {
            if ($quote = $this->getCurrentQuote()) {
                $this->_getSession()->setQuoteId((int)$quote->getId());
            }
        }

        /**
         * Identify customer
         */
        if ($customerId = $this->getRequest()->getParam('customer_id')) {
            $this->_getSession()->setCustomerId((int)$customerId);
        } else {
            if ($quote = $this->getCurrentQuote()) {
                if ($customerId = $quote->getCustomerId()) {
                    $this->_getSession()->setCustomerId((int)$customerId);
                }
            }
        }

        /**
         * Identify store
         */
        if ($storeId = $this->getRequest()->getParam('store_id')) {
            $this->_getSession()->setStoreId((int)$storeId);
        } else {
            if ($quote = $this->getCurrentQuote()) {
                if ($storeId = $quote->getStoreId()) {
                    $this->_getSession()->setStoreId((int)$storeId);
                }
            }
        }

        /**
         * Identify currency
         */
        if ($currencyId = $this->getRequest()->getParam('currency_id')) {
            $this->_getSession()->setCurrencyId((string)$currencyId);
            $this->getCurrentQuote()->setRecollect(true);
        } else {
            if ($quote = $this->getCurrentQuote()) {
                if ($currencyId = $quote->getCurrencyId()) {
                    $this->_getSession()->setCurrencyId((string)$currencyId);
                    $this->getCurrentQuote()->setRecollect(true);
                }
            }
        }


        return $this;
    }

    /**
     * Retrieve quote create model
     * @return \Cart2Quote\Quotation\Model\Quote
     */
    protected function getCurrentQuote()
    {
        if (!isset($this->_currentPo)) {
            if ($this->_coreRegistry->registry('current_quote')) {
                return $this->_currentPo = $this->_coreRegistry->registry('current_quote');
            }

            //if quote isn't set, return new quote model
            return $this->_currentPo = $this->poFactory->create();
        }

        return $this->_currentPo;
    }

    /**
     * Processing request data
     * @return $this
     */
    protected function _processData()
    {
        return $this->_processActionData();
    }

    /**
     * Process request data with additional logic for saving quote and creating order
     * @param string $action
     * @return $this
     */
    protected function _processActionData($action = null)
    {
        $eventData = [
            'quote_model'   => $this->getCurrentQuote(),
            'request_model' => $this->getRequest(),
            'session'       => $this->_getSession(),
        ];

        $this->_eventManager->dispatch('adminhtml_quotation_quote_view_process_data_before', $eventData);
        $data = $this->getRequest()->getPost('quote');

        /**
         * Saving order data
         */
        if ($data) {
            $this->getCurrentQuote()->importPostData($data);
            $quote = $this->getRequest()->getParam('quote', false);
            if (!isset($data['expiry_enabled'])) {
                $this->getCurrentQuote()->setExpiryEnabled(false);
            }
            if (!isset($data['reminder_enabled'])) {
                $this->getCurrentQuote()->setReminderEnabled(false);
            }
            if (isset($quote['status'])) {
                $newStatus = $quote['status'];
                $status = $this->_statusCollection->getItemByColumnValue('status', $newStatus);
                $state = $status->getState();
                $this->getCurrentQuote()->setState($state);
            }
        }

        /**
         * Set correct currency
         */
        $this->processCurrency();

        /**
         * Initialize catalog rule data
         */
        $this->getCurrentQuote()->initRuleData();

        /**
         * Process addresses
         */
        $this->_processAddresses();

        /**
         * Process shipping
         */
        $this->_processShipping();

        /**
         * Adding product to quote from shopping cart, wishlist etc.
         */
        if ($productId = (int)$this->getRequest()->getPost('add_product')) {
            $this->getCurrentQuote()->addProduct($productId, $this->getRequest()->getPostValue());
        }

        /**
         * Adding products to quote from special grid
         */
        if ($this->getRequest()->has('item') && !$this->getRequest()->getPost('update_items') && !($action == 'save')) {
            $items = $this->getRequest()->getPost('item');
            $items = $this->_processFiles($items);
            $this->getCurrentQuote()->addProducts($items);
        }

        /**
         * Update quote items
         */
        $this->_updateQuoteItems();

        /**
         * Remove quote item
         */
        $this->_removeQuoteItem();

        /**
         * Save payment data
         */
        if ($paymentData = $this->getRequest()->getPost('payment')) {
            $this->getCurrentQuote()->getPayment()->addData($paymentData);
        }

        /**
         * Set Subtotal Proposal
         */
        $this->_setSubtotalProposal();

        /**
         * Set Original Subtotal
         */
        $this->getCurrentQuote()->recalculateOriginalSubtotal();

        /**
         * Set Custom Price Total
         */
        $this->getCurrentQuote()->recalculateCustomPriceTotal();

        /**
         * Set qui
         */
        $this->getCurrentQuote()->recalculateQuoteAdjustmentTotal();

        /**
         * Process gift message
         */
        $this->_processGiftMessage();

        $couponCode = '';
        if (isset($data) && isset($data['coupon']['code'])) {
            $couponCode = trim($data['coupon']['code']);
        }

        if (!empty($couponCode)) {
            $isApplyDiscount = false;
            foreach ($this->getCurrentQuote()->getAllItems() as $item) {
                if (!$item->getNoDiscount()) {
                    $isApplyDiscount = true;
                    break;
                }
            }
            if (!$isApplyDiscount) {
                $this->messageManager->addError(
                    __(
                        '"%1" coupon code was not applied. Do not apply discount is selected for item(s)',
                        $this->escaper->escapeHtml($couponCode)
                    )
                );
            } else {
                if ($this->getCurrentQuote()->getCouponCode() !== $couponCode) {
                    $this->messageManager->addError(
                        __(
                            '"%1" coupon code is not valid.',
                            $this->escaper->escapeHtml($couponCode)
                        )
                    );
                } else {
                    $this->messageManager->addSuccess(__('The coupon code has been accepted.'));
                }
            }
        }

        $eventData = [
            'quote_model' => $this->getCurrentQuote(),
            'request'     => $this->getRequest()->getPostValue(),
        ];
        $this->_eventManager->dispatch('adminhtml_quotation_quote_view_process_data', $eventData);

        $this->getCurrentQuote()->saveQuote();

        return $this;
    }

    /**
     * Function Process the quote addresses
     */
    protected function _processAddresses()
    {
        /**
         * init first billing address, need for virtual products
         */
        $this->getCurrentQuote()->getBillingAddress();

        /**
         * Flag for using billing address for shipping
         */
        if (!$this->getCurrentQuote()->isVirtual()) {
            $syncFlag = $this->getRequest()->getPost('shipping_as_billing');
            $shippingMethod = $this->getCurrentQuote()->getShippingAddress()->getShippingMethod();
            if ($syncFlag === null
                && $this->getCurrentQuote()->getShippingAddress()->getSameAsBilling() && empty($shippingMethod)
            ) {
                $this->getCurrentQuote()->setShippingAsBilling(1);
            } else {
                $this->getCurrentQuote()->setShippingAsBilling((int)$syncFlag);
            }
        }
    }

    /**
     * Function Process the quote shipping method
     */
    protected function _processShipping()
    {
        /**
         * Change shipping address flag
         */
        if (!$this->getCurrentQuote()->isVirtual() && $this->getRequest()->getPost('reset_shipping')) {
            $this->getCurrentQuote()->resetShippingMethod();
        }

        /**
         * Collecting shipping rates
         */
        if (!$this->getCurrentQuote()->isVirtual() && $this->getRequest()->getPost('collect_shipping_rates')) {
            $this->getCurrentQuote()->save();
            $this->getCurrentQuote()->collectShippingRates();
        }
    }

    /**
     * Process buyRequest file options of items
     * @param array $items
     * @return array
     */
    protected function _processFiles($items)
    {
        /** @var $productHelper \Magento\Catalog\Helper\Product */
        $productHelper = $this->_objectManager->get('Magento\Catalog\Helper\Product');
        foreach ($items as $id => $item) {
            $buyRequest = new \Magento\Framework\DataObject($item);
            $params = ['files_prefix' => 'item_' . $id . '_'];
            $buyRequest = $productHelper->addParamsToBuyRequest($buyRequest, $params);
            if ($buyRequest->hasData()) {
                $items[$id] = $buyRequest->toArray();
            }
        }

        return $items;
    }

    /**
     * Update the quote items based on the data provided in the post data
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _updateQuoteItems()
    {
        if ($this->getRequest()->getPost('update_items')) {
            //IMPORTANT update tier items first otherwise items use previous custom price
            $tierItems = $this->getRequest()->getPost('tierItem', []);
            $items = $this->getRequest()->getPost('item', []);

            //untill full tier support, we have to set the qty field to the selected tier
            foreach ($items as $item) {
                if (isset($item['selectedTier']) && !empty($tierItems) && isset($item['qty'])) {
                    $tierItems[$item['selectedTier']]['qty'] = $item['qty'];
                }
            }

            $this->getCurrentQuote()->updateTierItems($tierItems);
            $items = $this->_processTierItemSelection($items, $tierItems);
            $items = $this->_processFiles($items);
            $this->getCurrentQuote()->updateQuoteItems($items);
            $this->getCurrentQuote()->updateBaseCustomPrice();

            if ($this->getRequest()->getPost('remove_items')) {
                foreach ($items as $key => $item) {
                    if ($item['action'] == 'remove') {
                        $this->getCurrentQuote()->removeItem($key);
                    }
                }
            }
        }
    }

    /**
     * Set the currency, collected from the post data, on the quote.
     *
     * @return $this;
     */
    protected function processCurrency()
    {
        if ($currency = $this->getRequest()->getPost('currency_id')) {
            if ($currency != $this->getCurrentQuote()->getQuoteCurrency()->getCode()) {
                if ($currency == "false") {
                    $this->getCurrentQuote()->setQuoteCurrencyCode(
                        $this->getCurrentQuote()->getBaseCurrency()->getCode()
                    );
                } else {
                    $this->getCurrentQuote()->setQuoteCurrencyCode($currency);
                }

                $this->getCurrentQuote()->setBaseToQuoteRate(
                    $this->getCurrentQuote()->getBaseCurrency()->getRate($currency)
                );
                $this->getCurrentQuote()->resetQuoteCurrency();
            }
        }

        return $this;
    }

    /**
     * Function that adds the custom price of the selected tier item
     *
     * @param array $items
     * @param array $tierItems
     * @return array
     */
    protected function _processTierItemSelection($items = [], $tierItems = [])
    {
        //loop trough all the items to check if a tier item is selected
        foreach ($items as $key => $item) {
            if (isset($item['selectedTier'])) {
                $tierKey = $item['selectedTier'];
                if (isset($tierItems[$tierKey])) {
                    $tierItem = $tierItems[$tierKey];
                    if (isset($tierItem['custom_price'])) {
                        $customPrice = $tierItem['custom_price'];
                        //we could als check here on the qty, but we already use the key to define the tier option
                        $items[$key]['custom_price'] = $customPrice;
                    }
                }
            }
        }

        return $items;
    }

    /**
     * Remove a quote item based on the post data
     */
    protected function _removeQuoteItem()
    {
        $removeItemId = (int)$this->getRequest()->getPost('remove_item');
        $removeFrom = (string)$this->getRequest()->getPost('from');
        if ($removeItemId && $removeFrom) {
            $this->getCurrentQuote()->removeItem($removeItemId);
        }
    }

    /**
     * Sets the proposal subtotal
     */
    protected function _setSubtotalProposal()
    {
        $proposal = $this->getRequest()->getPost('proposal');
        if (isset($proposal) && isset($proposal['subtotal_proposal'])) {
            if (isset($proposal['proposal_is_percentage']) && $proposal['proposal_is_percentage'] === 'true') {
                $isPercentage = true;
            } else {
                $isPercentage = false;
            }
            $amount = (float)$proposal['subtotal_proposal'];
            $this->getCurrentQuote()->setSubtotalProposal($amount, $isPercentage);
        }
    }

    /**
     * Trigers the giftmessage methods
     * @return mixed
     */
    protected function _processGiftMessage()
    {
        /**
         * Saving of giftmessages
         */
        $this->_saveGiftMessage();

        /**
         * Importing gift message allow items from specific product grid
         */
        $data = $this->_importGiftMessageAllowQuoteItemsFromProducts();

        /**
         * Importing gift message allow items on update quote items
         */
        $this->_importGiftMessageAllowQuoteItemsFromItems();

        return $data;
    }

    /**
     * Saves Gift message
     */
    protected function _saveGiftMessage()
    {
        $giftmessages = $this->getRequest()->getPost('giftmessage');
        if ($giftmessages) {
            $this->_getGiftmessageSaveModel()->setGiftmessages($giftmessages)->saveAllInQuote();
        }
    }

    /**
     * Retrieve gift message save model
     * @return \Magento\GiftMessage\Model\Save
     */
    protected function _getGiftmessageSaveModel()
    {
        return $this->_objectManager->get('Magento\GiftMessage\Model\Save');
    }

    /**
     * importAllowQuoteItemsFromProducts
     * @return mixed
     */
    protected function _importGiftMessageAllowQuoteItemsFromProducts()
    {
        if ($data = $this->getRequest()->getPost('add_products')) {
            $this->_getGiftmessageSaveModel()->importAllowQuoteItemsFromProducts(
                $this->_objectManager->get('Magento\Framework\Json\Helper\Data')->jsonDecode($data)
            );

            return $data;
        }

        return $data;
    }

    /**
     * importAllowQuoteItemsFromItems
     */
    protected function _importGiftMessageAllowQuoteItemsFromItems()
    {
        if ($this->getRequest()->getPost('update_items')) {
            $items = $this->getRequest()->getPost('item', []);
            $this->_getGiftmessageSaveModel()->importAllowQuoteItemsFromItems($items);
        }
    }

    /**
     * @return $this
     */
    protected function _reloadQuote()
    {
        $this->_currentPo = $this->poFactory->create()->load($this->getCurrentQuote()->getId());
        $this->_coreRegistry->unregister('current_quote');
        $this->_coreRegistry->register('current_quote', $this->_currentPo);

        return $this;
    }
}

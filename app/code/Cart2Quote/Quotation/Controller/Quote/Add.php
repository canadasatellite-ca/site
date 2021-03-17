<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Controller\Quote;

use Cart2Quote\Quotation\Model\QuotationCart;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Add
 *
 * @package Cart2Quote\Quotation\Controller\Quote
 */
class Add extends \Cart2Quote\Quotation\Controller\Quote
{
    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var \Cart2Quote\Quotation\Helper\Data
     */
    protected $quotationDataHelper;

    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $resolverInterface;

    /**
     * @var \Magento\Framework\Escaper
     */
    protected $escaper;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    private $jsonHelper;

    /**
     * @var \Cart2Quote\Quotation\Model\Quote\Request\Strategy\Provider
     */
    private $strategyProvider;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var \Magento\Checkout\Model\Cart\RequestInfoFilterInterface
     */
    private $requestInfoFilter;

    /**
     * Add constructor
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator
     * @param \Cart2Quote\Quotation\Model\QuotationCart $cart
     * @param \Cart2Quote\Quotation\Model\Session $quotationSession
     * @param \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Cart2Quote\Quotation\Helper\Data $quotationDataHelper
     * @param \Magento\Framework\Locale\ResolverInterface $resolverInterface
     * @param \Magento\Framework\Escaper $escaper
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Cart2Quote\Quotation\Model\Quote\Request\Strategy\Provider $strategyProvider
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Data\Form\FormKey\Validator $formKeyValidator,
        \Cart2Quote\Quotation\Model\QuotationCart $cart,
        \Cart2Quote\Quotation\Model\Session $quotationSession,
        \Cart2Quote\Quotation\Model\QuoteFactory $quoteFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        ProductRepositoryInterface $productRepository,
        \Magento\Catalog\Helper\Product $productHelper,
        \Cart2Quote\Quotation\Helper\Data $quotationDataHelper,
        \Magento\Framework\Locale\ResolverInterface $resolverInterface,
        \Magento\Framework\Escaper $escaper,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Cart2Quote\Quotation\Model\Quote\Request\Strategy\Provider $strategyProvider,
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->quotationDataHelper = $quotationDataHelper;
        $this->productHelper = $productHelper;
        $this->productRepository = $productRepository;
        $this->resolverInterface = $resolverInterface;
        $this->escaper = $escaper;
        $this->jsonHelper = $jsonHelper;
        $this->strategyProvider = $strategyProvider;
        $this->customerSession = $customerSession;

        parent::__construct(
            $context,
            $scopeConfig,
            $storeManager,
            $formKeyValidator,
            $cart,
            $quotationSession,
            $quoteFactory,
            $resultPageFactory,
            $logger
        );
    }

    /**
     * Add product to quote cart action
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Redirect|\Magento\Framework\Controller\ResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new \Zend_Filter_LocalizedToNormalized(
                    [
                        'locale' => $this->resolverInterface->getLocale()
                    ]
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                return $this->goBack();
            }

            //check if product is quoteable
            $checkProduct = $product;

            //if product is configurable check if Dynamic AddButtons is Enabled
            if ($checkProduct->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
                //if that is enabled, extract the target simple product
                if ($this->quotationDataHelper->isDynamicAddButtonsEnabled()) {
                    $checkProduct = $this->_getProduct($checkProduct);
                    $request = $this->_getProductRequest($params);
                    $cartCandidates = $checkProduct->getTypeInstance()
                        ->prepareForCartAdvanced(
                            $request,
                            $checkProduct,
                            \Magento\Catalog\Model\Product\Type\AbstractType::PROCESS_MODE_FULL
                        );

                    foreach ($cartCandidates as $cartCandidate) {
                        if ($cartCandidate->getTypeId() == \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE) {
                            //we found a simple product
                            $checkProduct = $cartCandidate;
                            break;
                        }
                    }
                }
            }

            //check the product for quotability
            $quotable = $this->quotationDataHelper->isQuotable(
                $checkProduct,
                $this->customerSession->getCustomerGroupId()
            );

            if (!$quotable) {
                $this->messageManager->addErrorMessage(
                    __('This product is not quotable.')
                );

                return $this->goBack();
            }

            if (!$this->quotationDataHelper->isStockEnabledFrontend()) {
                $this->productHelper->setSkipSaleableCheck(true);
                $this->cart->getQuote()->setIsSuperMode(true);
            }

            $this->cart->addProduct($product, $params);
            if (!empty($related)) {
                $this->cart->addProductsByIds(explode(',', $related));
            }

            $this->cart->getQuote()->setIsQuotationQuote(true);
            $this->cart->save();

            $this->_eventManager->dispatch(
                'checkout_cart_add_product_complete',
                ['product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse()]
            );

            $strategy = $this->strategyProvider->getStrategy();
            $redirectToCart = $this->_quotationSession->getNoCartRedirect(true);
            if (!$redirectToCart || $strategy == 'quick_quote') {
                if (!$this->cart->getQuote()->getHasError()) {
                    $message = __(
                        'You added %1 to <a href="%2">your quote</a>.',
                        $this->escaper->escapeHtml($product->getName()),
                        $this->_url->getUrl('quotation/quote')
                    );

                    //still using the depricated addSuccess as addSuccessMessage removes the url
                    $this->messageManager->addSuccess($message);
                }

                return $this->goBack(null, $product);
            }
        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            if ($this->_quotationSession->getUseNotice(true)) {
                $this->messageManager->addNoticeMessage(
                    $this->escaper->escapeHtml($e->getMessage())
                );
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->messageManager->addErrorMessage(
                        $this->escaper->escapeHtml($message)
                    );
                }
            }
            $url = $this->_quotationSession->getRedirectUrl(true);
            if ($url) {
                return $this->goBack($url);
            }
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('We can\'t add this item to your quote right now.'));
            $this->logger->critical($e);
        }

        return $this->goBack();
    }

    /**
     * Initialize product instance from request data
     *
     * @return bool|\Magento\Catalog\Api\Data\ProductInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function _initProduct()
    {
        $productId = (int)$this->getRequest()->getParam('product');
        if ($productId) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                return $this->productRepository->getById($productId, false, $storeId);
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                return false;
            }
        }

        return false;
    }

    /**
     * Resolve response
     *
     * @param null|string $backUrl
     * @param null|\Magento\Catalog\Model\Product $product
     * @param bool $addedFlag
     * @return \Magento\Framework\Controller\Result\Redirect
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function goBack($backUrl = null, $product = null, $addedFlag = true)
    {
        if (!$this->getRequest()->isAjax()) {
            return parent::_goBack($backUrl);
        }

        $result = [];
        if ($backUrl || $backUrl = $this->getBackUrl()) {
            $result['backUrl'] = $backUrl;
        }

        if (!$addedFlag) {
            $result['added'] = false;
        }

        $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($result)
        );
    }

    /**
     * Get resolved back url
     *
     * @param null|string $defaultUrl
     * @return string|null
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getBackUrl($defaultUrl = null)
    {
        $returnUrl = $this->getRequest()->getParam('return_url');
        if ($returnUrl && $this->_isInternalUrl($returnUrl)) {
            $this->messageManager->getMessages()->clear();

            return $returnUrl;
        }

        //use magento cart settings
        $shouldRedirectToCart = $this->_scopeConfig->getValue(
            'checkout/cart/redirect_to_cart',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        //get quote strategy
        $strategy = $this->strategyProvider->getStrategy();

        if (($shouldRedirectToCart && $strategy != 'quick_quote') || $this->getRequest()->getParam('in_quote')) {
            if ($this->getRequest()->getActionName() == 'add' && !$this->getRequest()->getParam('in_quote')) {
                $this->_quotationSession->setContinueShoppingUrl($this->_redirect->getRefererUrl());
            }

            return $this->_url->getUrl('quotation/quote');
        }

        return $defaultUrl;
    }

    /**
     * Get product object based on requested product information
     * See: \Magento\Checkout\Model\Cart::_getProduct
     *
     * @param   Product|int|string $productInfo
     * @return  Product
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getProduct($productInfo)
    {
        $product = null;
        if ($productInfo instanceof Product) {
            $product = $productInfo;
            if (!$product->getId()) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("The product wasn't found. Verify the product and try again.")
                );
            }
        } elseif (is_int($productInfo) || is_string($productInfo)) {
            $storeId = $this->_storeManager->getStore()->getId();
            try {
                $product = $this->productRepository->getById($productInfo, false, $storeId);
            } catch (NoSuchEntityException $e) {
                throw new \Magento\Framework\Exception\LocalizedException(
                    __("The product wasn't found. Verify the product and try again."),
                    $e
                );
            }
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("The product wasn't found. Verify the product and try again.")
            );
        }
        $currentWebsiteId = $this->_storeManager->getStore()->getWebsiteId();
        if (!is_array($product->getWebsiteIds()) || !in_array($currentWebsiteId, $product->getWebsiteIds())) {
            throw new \Magento\Framework\Exception\LocalizedException(
                __("The product wasn't found. Verify the product and try again.")
            );
        }
        return $product;
    }

    /**
     * Get request for product add to cart procedure
     * See: \Magento\Checkout\Model\Cart::_getProductRequest
     *
     * @param   \Magento\Framework\DataObject|int|array $requestInfo
     * @return  \Magento\Framework\DataObject
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getProductRequest($requestInfo)
    {
        if ($requestInfo instanceof \Magento\Framework\DataObject) {
            $request = $requestInfo;
        } elseif (is_numeric($requestInfo)) {
            $request = new \Magento\Framework\DataObject(['qty' => $requestInfo]);
        } elseif (is_array($requestInfo)) {
            $request = new \Magento\Framework\DataObject($requestInfo);
        } else {
            throw new \Magento\Framework\Exception\LocalizedException(
                __('We found an invalid request for adding product to quote.')
            );
        }
        $this->getRequestInfoFilter()->filter($request);

        return $request;
    }

    /**
     * Getter for RequestInfoFilter
     * See: \Magento\Checkout\Model\Cart::getRequestInfoFilter
     *
     * @deprecated 100.1.2
     * @return \Magento\Checkout\Model\Cart\RequestInfoFilterInterface
     */
    private function getRequestInfoFilter()
    {
        if ($this->requestInfoFilter === null) {
            $this->requestInfoFilter = \Magento\Framework\App\ObjectManager::getInstance()
                ->get(\Magento\Checkout\Model\Cart\RequestInfoFilterInterface::class);
        }
        return $this->requestInfoFilter;
    }
}

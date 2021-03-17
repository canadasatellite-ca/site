<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Carrier;

use \Magento\Shipping\Model\Carrier\AbstractCarrier;

/**
 * Quotation shipping model
 */
class QuotationShipping extends AbstractCarrier implements \Magento\Shipping\Model\Carrier\CarrierInterface
{

    use \Cart2Quote\Features\Traits\Model\Carrier\QuotationShipping {
        collectRates as private traitCollectRates;
        getQuoteId as private traitGetQuoteId;
        getQuote as private traitGetQuote;
        getSessionQuoteConfigData as private traitGetSessionQuoteConfigData;
        hasFixedShipping as private traitHasFixedShipping;
        isBackend as private traitIsBackend;
        existsInSession as private traitExistsInSession;
        getShippingPrice as private traitGetShippingPrice;
        getFixedShippingPrice as private traitGetFixedShippingPrice;
        createResultMethod as private traitCreateResultMethod;
        getAllowedMethods as private traitGetAllowedMethods;
        isActive as private traitIsActive;
        getShippingTitle as private traitGetShippingTitle;
        getShippingMethod as private traitGetShippingMethod;
    }

    const CODE = 'quotation';

    /**
     * Path to Method enabled
     */
    const XML_PATH_CARRIER_QUOTATION_ENABLED = 'carriers/quotation/active';

    /**
     * Path to method name in system.xml
     */
    const XML_PATH_CARRIER_QUOTATION_METHOD = 'carriers/quotation/name';

    /**
     * Path to title in system.xml
     */
    const XML_PATH_CARRIER_QUOTATION_TITLE = 'carriers/quotation/title';
    
    /**
     * Code
     *
     * @var string
     */
    protected $_code = self::CODE;

    /**
     * Is Fixed
     *
     * @var bool
     */
    protected $_isFixed = true;

    /**
     * Rate \Magento\Shipping\Model\Rate\Result Factory
     *
     * @var \Magento\Shipping\Model\Rate\ResultFactory
     */
    protected $_rateResultFactory;

    /**
     * Rate Method Factory
     *
     * @var \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory
     */
    protected $_rateMethodFactory;
    /**
     * Quotation Quote Session
     *
     * @var \Cart2Quote\Quotation\Model\Session
     */
    protected $quoteSession;
    /**
     * Magento App State
     *
     * @var \Magento\Framework\App\State
     */
    protected $appState;
    /**
     * Quotation Quote Model
     *
     * @var \Cart2Quote\Quotation\Model\Quote
     */
    private $quote;

    /**
     * QuotationShipping constructor.
     *
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory
     * @param \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory
     * @param \Cart2Quote\Quotation\Model\Quote $quote
     * @param \Cart2Quote\Quotation\Model\Session $quoteSession
     * @param \Magento\Framework\App\State $appState
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory $rateErrorFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Shipping\Model\Rate\ResultFactory $rateResultFactory,
        \Magento\Quote\Model\Quote\Address\RateResult\MethodFactory $rateMethodFactory,
        \Cart2Quote\Quotation\Model\Quote $quote,
        \Cart2Quote\Quotation\Model\Session $quoteSession,
        \Magento\Framework\App\State $appState,
        array $data = []
    ) {
        $this->_rateResultFactory = $rateResultFactory;
        $this->_rateMethodFactory = $rateMethodFactory;
        $this->quote = $quote;
        $this->quoteSession = $quoteSession;
        $this->appState = $appState;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * Collect the shipping rates
     * - Only when using a quotation quote the shipping will be collected
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return \Magento\Shipping\Model\Rate\Result|bool
     */
    public function collectRates(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        return $this->traitCollectRates($request);
    }

    /**
     * Get the quote ID
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return int
     */
    protected function getQuoteId(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        return $this->traitGetQuoteId($request);
    }

    /**
     * Get the quote
     *
     * @param \Magento\Quote\Model\Quote\Address\RateRequest $request
     * @return int
     */
    protected function getQuote(\Magento\Quote\Model\Quote\Address\RateRequest $request)
    {
        return $this->traitGetQuote($request);
    }

    /**
     * Get quote data from the session
     *
     * @param int $quoteId
     * @return array
     */
    protected function getSessionQuoteConfigData($quoteId)
    {
        return $this->traitGetSessionQuoteConfigData($quoteId);
    }

    /**
     * Has fixed shipping price
     *
     * @param array $configData
     * @return bool
     */
    protected function hasFixedShipping($configData)
    {
        return $this->traitHasFixedShipping($configData);
    }

    /**
     * Check if the request is done in the backend
     *
     * @return bool
     */
    protected function isBackend()
    {
        return $this->traitIsBackend();
    }

    /**
     * Check if the quote id in the session is the same as the given quote id
     *
     * @param int $quoteId
     * @return bool
     */
    protected function existsInSession($quoteId)
    {
        return $this->traitExistsInSession($quoteId);
    }

    /**
     * Get the shipping price
     *
     * @param array $configData
     * @return float
     */
    protected function getShippingPrice($configData)
    {
        return $this->traitGetShippingPrice($configData);
    }

    /**
     * Get fixed shipping price
     *
     * @param array $configData
     * @return string
     */
    protected function getFixedShippingPrice($configData)
    {
        return $this->traitGetFixedShippingPrice($configData);
    }

    /**
     * Create the result method
     *
     * @param int|float $shippingPrice
     * @return \Magento\Quote\Model\Quote\Address\RateResult\Method
     */
    protected function createResultMethod($shippingPrice)
    {
        return $this->traitCreateResultMethod($shippingPrice);
    }

    /**
     * Get allowed methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return $this->traitGetAllowedMethods();
    }

    /**
     * Check if the Shipping method is Active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->traitIsActive();
    }

    /**
     * Get quotation shipping title
     *
     * @return string
     */
    public function getShippingTitle()
    {
        return $this->traitGetShippingTitle();
    }

    /**
     * Get quotation shipping method name
     *
     * @return string
     */
    public function getShippingMethod()
    {
        return $this->traitGetShippingMethod();
    }
}

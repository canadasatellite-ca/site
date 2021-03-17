<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Carrier;

use Magento\Amazon\Api\Data\OrderItemInterface;
use Magento\Amazon\Api\OrderRepositoryInterface;
use Magento\Amazon\Model\CurrencyConversion;
use Magento\Amazon\Model\ResourceModel\Amazon\Account\Listing\CollectionFactory as AccountListingCollectionFactory;
use Magento\Amazon\Model\ResourceModel\Amazon\Order\Item\CollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\ErrorFactory;
use Magento\Quote\Model\Quote\Address\RateResult\MethodFactory;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Shipping\Model\Carrier\CarrierInterface;
use Magento\Shipping\Model\Rate\ResultFactory;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;

/**
 * Class Method
 */
class Method extends AbstractCarrier implements CarrierInterface
{
    /** @var string $code */
    const SHIPPING_CODE = 'amazonshipping';

    /** @var ResultFactory */
    protected $rateResultFactory;
    /** @var MethodFactory */
    protected $rateMethodFactory;
    /** @var StoreManagerInterface $storeManager */
    protected $storeManager;
    /** @var DataPersistorInterface $dataPersistor */
    protected $dataPersistor;
    /** @var CollectionFactory $itemCollectionFactory */
    protected $itemCollectionFactory;
    /** @var OrderRepositoryInterface $orderRepository */
    protected $orderRepository;
    /** @var AccountListingCollectionFactory $accountListingCollectionFactory */
    protected $accountListingCollectionFactory;
    /** @var CurrencyConversion $currencyConversion */
    protected $currencyConversion;
    /** @var string */
    protected $_code = self::SHIPPING_CODE;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param ErrorFactory $rateErrorFactory
     * @param LoggerInterface $logger
     * @param ResultFactory $rateResultFactory
     * @param MethodFactory $rateMethodFactory
     * @param StoreManagerInterface $storeManager
     * @param DataPersistorInterface $dataPersistor
     * @param CollectionFactory $itemCollectionFactory
     * @param OrderRepositoryInterface $orderRepository
     * @param AccountListingCollectionFactory $accountListingCollectionFactory
     * @param CurrencyConversion $currencyConversion
     * @param array $data
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ErrorFactory $rateErrorFactory,
        LoggerInterface $logger,
        ResultFactory $rateResultFactory,
        MethodFactory $rateMethodFactory,
        StoreManagerInterface $storeManager,
        DataPersistorInterface $dataPersistor,
        CollectionFactory $itemCollectionFactory,
        OrderRepositoryInterface $orderRepository,
        AccountListingCollectionFactory $accountListingCollectionFactory,
        CurrencyConversion $currencyConversion,
        array $data = []
    ) {
        $this->rateResultFactory = $rateResultFactory;
        $this->rateMethodFactory = $rateMethodFactory;
        $this->storeManager = $storeManager;
        $this->dataPersistor = $dataPersistor;
        $this->itemCollectionFactory = $itemCollectionFactory;
        $this->orderRepository = $orderRepository;
        $this->accountListingCollectionFactory = $accountListingCollectionFactory;
        $this->currencyConversion = $currencyConversion;
        parent::__construct($scopeConfig, $rateErrorFactory, $logger, $data);
    }

    /**
     * FreeShipping Rates Collector
     *
     * @param RateRequest $request
     * @return ResultFactory | bool
     */
    public function collectRates(RateRequest $request)
    {
        if (!$orderId = $this->dataPersistor->get('marketplace_order_id')) {
            return false;
        }

        try {
            $order = $this->orderRepository->getOrderByMarketplaceOrderId($orderId);
        } catch (NoSuchEntityException $e) {
            return false;
        }

        /** @var AccountInterface */
        $account = $this->accountListingCollectionFactory->create()
            ->addFieldToFilter('merchant_id', $order->getMerchantId())->getFirstItem();

        /** @var OrderItemInterface */
        if ($collection = $this->itemCollectionFactory->create()
            ->addFieldToFilter('order_id', $orderId)
        ) {

            /** @var float */
            $price = 0.00;
            /** @var string */
            $conversionCode = '';

            foreach ($collection as $item) {
                $price += $item->getShippingPrice();
            }

            if ($account->getCcIsActive()) {
                $conversionCode = $account->getCcRate();
            }

            if ($conversionCode) {
                $price = $this->currencyConversion->convert($price, $conversionCode);
            }

            /** @var ResultFactory */
            $result = $this->rateResultFactory->create();

            /** @var MethodFactory */
            $method = $this->rateMethodFactory->create();

            $method->setCarrier(self::SHIPPING_CODE);
            $method->setCarrierTitle('Amazon ' . $order->getShipServiceLevel());

            $method->setMethod(self::SHIPPING_CODE);
            $method->setMethodTitle($order->getServiceLevel());

            $method->setPrice($price);
            $method->setCost($price);

            $result->append($method);

            return $result;
        }

        return false;
    }

    /**
     * @return array
     */
    public function getAllowedMethods()
    {
        return [self::SHIPPING_CODE => $this->getConfigData('name')];
    }
}

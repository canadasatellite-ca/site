<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PurchaseOrderSuccess\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Price
 * @package Magestore\PurchaseOrderSuccess\Ui\Component\Listing\Columns
 */
class Price extends Column
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var []
     */
    protected $currency;

    protected $registry;
    protected $purchaseOrderRepository;

    /**
     * Price constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PriceCurrencyInterface $priceFormatter
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Framework\Registry $registry,
        \Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface $purchaseOrderRepository,
        array $components = [],
        array $data = []
    )
    {
        $this->registry = $registry;
        $this->purchaseOrderRepository = $purchaseOrderRepository;
        $this->priceCurrency = $priceCurrency;
        $this->currencyFactory = $currencyFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $currencyRate = $this->context->getRequestParam('currency_rate', 1);
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (isset($item['currency_rate']))
                    $currencyRate = $item['currency_rate'];

                if (isset($item['purchase_order_id'])) {
                    $purchaseOrder = $this->getCurrentPurchaseOrder($item['purchase_order_id']);
                    $currencyRate = $purchaseOrder['currency_rate'];
                }

                $price = $this->priceCurrency->convert($item[$this->getData('name')] * $currencyRate);
                $currencyCode = isset($item['currency_code']) ? $item['currency_code'] : 'default';
                if ($currencyCode == 'default') {
                    $currencyCode = $this->context->getRequestParam('currency_code', 'default');
                }
                if ($currencyCode != 'default' && !isset($this->currency[$currencyCode]))
                    $this->currency[$currencyCode] = $this->currencyFactory->create()->load($currencyCode);
                else if (!isset($this->currency[$currencyCode]))
                    $this->currency['default'] = $this->currencyFactory->create()->load(null);

                if (!in_array($currencyCode, array('CAD', 'default')) && $price > 0) {
                    if (!isset($this->currency['CAD']))
                        $this->currency['CAD'] = $this->currencyFactory->create()->load('CAD');

                    $cadPrice = $this->currency['CAD']->formatTxt($price);
                    $otherPrice = $this->currency[$currencyCode]->formatTxt($item[$this->getData('name')]);
                    $item[$this->getData('name')] = $cadPrice . '; ' . $otherPrice;
                    continue;
                }
                $item[$this->getData('name')] = $this->currency[$currencyCode]->formatTxt($price);

//                $this->priceFormatter->convertAndFormat(
//                    $item[$this->getData('name')],
//                    false,
//                    null,
//                    null,
//                    $currencyCode
//                );
            }
        }
        return $dataSource;
    }

    public function getCurrentPurchaseOrder($id)
    {
        $purchaseOrder = $this->registry->registry('current_purchase_order');
        if (!$purchaseOrder || !$purchaseOrder->getId())
            $purchaseOrder = $this->purchaseOrderRepository->get($id);
        return $purchaseOrder;
    }
}

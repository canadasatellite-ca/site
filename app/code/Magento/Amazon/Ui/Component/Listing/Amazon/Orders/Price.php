<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Orders;

use Magento\Amazon\Api\AccountRepositoryInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Price
 */
class Price extends Column
{
    /** @var PriceCurrencyInterface $priceFormatter */
    protected $priceFormatter;
    /** @var Http $request */
    protected $request;
    /** @var AccountRepositoryInterface $accountRepository */
    protected $accountRepository;

    /**
     * Constructor
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PriceCurrencyInterface $priceFormatter
     * @param Http $request
     * @param AccountRepositoryInterface $accountRepository
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PriceCurrencyInterface $priceFormatter,
        Http $request,
        AccountRepositoryInterface $accountRepository,
        array $components = [],
        array $data = []
    ) {
        $this->priceFormatter = $priceFormatter;
        $this->request = $request;
        $this->accountRepository = $accountRepository;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepares data source for grid
     *
     * @return array
     * @var array
     */
    public function prepareDataSource(array $dataSource)
    {
        $currency = null;

        if (isset($dataSource['data'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $merchantId = $item['merchant_id'];
                try {
                    /** @var AccountInterface */
                    $account = $this->accountRepository->getByMerchantId($merchantId);
                    $countryCode = $account->getCountryCode();
                    if ($countryCode) {
                        $currency = Definitions::getCurrencyCode($countryCode, 'USD');
                    }
                } catch (NoSuchEntityException $e) {
                    $currency = 'USD';
                }

                $item['total'] = $this->priceFormatter->format(
                    $item['total'],
                    false,
                    null,
                    null,
                    $currency
                );
            }
        }

        return $dataSource;
    }
}

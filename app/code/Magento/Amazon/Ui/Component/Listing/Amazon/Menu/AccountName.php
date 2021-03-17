<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Menu;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class AccountName
 */
class AccountName extends Column
{
    /**
     * @var \Magento\Amazon\Ui\FrontendUrl
     */
    private $frontendUrl;
    /**
     * @var \Magento\Amazon\Api\AccountRepositoryInterface
     */
    private $accountRepository;

    public function __construct(
        \Magento\Framework\View\Element\UiComponent\ContextInterface $context,
        \Magento\Framework\View\Element\UiComponentFactory $uiComponentFactory,
        \Magento\Amazon\Ui\FrontendUrl $frontendUrl,
        \Magento\Amazon\Api\AccountRepositoryInterface $accountRepository,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->frontendUrl = $frontendUrl;
        $this->accountRepository = $accountRepository;
    }

    public function prepareDataSource(array $dataSource)
    {
        $accounts = [];

        if (isset($dataSource['data'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                /** @var int */
                $merchantId = (int)$item['merchant_id'];
                /** @var string */
                $name = $item['name'];

                if (!array_key_exists($merchantId, $accounts)) {
                    try {
                        $accounts[$merchantId] = $this->accountRepository->getByMerchantId($merchantId);
                    } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                        $accounts[$merchantId] = null;
                    }
                }

                $url = isset($accounts[$merchantId]) ? $this->frontendUrl->getStoreDetailsUrl($accounts[$merchantId]) : null;

                $item['name'] = $url
                    ? sprintf("<a class='action-menu-item' href='%s'>%s</a>", $url, $name)
                    : $name;
            }
        }
        return $dataSource;
    }
}

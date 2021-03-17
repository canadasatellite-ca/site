<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Listing\Amazon\Account\Listing\Thirdparty\Manual;

use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Actions
 */
class Actions extends Column
{
    /** @var string */
    const URL_MANUAL_ASSIGN_SAVE = 'channel/amazon/account_listing_thirdparty_manual_save';

    /** @var UrlInterface $urlBuilder */
    protected $urlBuilder;
    /** @var RequestInterface $request */
    protected $request;
    /** @var DataPersistorInterface $dataPersistor */
    protected $dataPersistor;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        RequestInterface $request,
        DataPersistorInterface $dataPersistor,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->dataPersistor = $dataPersistor;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepares action grid field
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (!isset($dataSource['data']['items'])) {
            return $dataSource;
        }

        /** @var int */
        if ($merchantId = $this->request->getParam('merchant_id')) {
            $this->dataPersistor->set('manual_merchant_id', $merchantId);
        } else {
            $merchantId = $this->dataPersistor->get('manual_merchant_id');
            $this->dataPersistor->clear('manual_merchant_id');
        }

        /** @var int */
        if ($listingId = $this->request->getParam('id')) {
            $this->dataPersistor->set('manual_id', $listingId);
        } else {
            $listingId = $this->dataPersistor->get('manual_id');
            $this->dataPersistor->clear('manual_id');
        }

        foreach ($dataSource['data']['items'] as & $item) {
            if (isset($item['entity_id']) && isset($item['sku'])) {

                /** @var int */
                $id = $item['entity_id'];
                /** @var string */
                $sku = $item['sku'];
                /** @var string */
                $url = $this->urlBuilder->getUrl(static::URL_MANUAL_ASSIGN_SAVE, [
                    "id" => $id,
                    "sku" => $sku,
                    "listing_id" => $listingId,
                    "merchant_id" => $merchantId
                ]);

                $message = 'Once assigned, this product will be used to synchronize the pricing ';
                $message .= 'and stock levels on Amazon.<br><br>Are you sure you want to assign this product?';

                $item[$this->getData('name')] = [
                    'manual_assign' => [
                        'href' => $url,
                        'label' => __('Assign Catalog Product'),
                        'confirm' => [
                            'title' => __('Assign Catalog Product'),
                            'message' => __($message)
                        ],
                    ]
                ];
            }
        }

        return $dataSource;
    }
}

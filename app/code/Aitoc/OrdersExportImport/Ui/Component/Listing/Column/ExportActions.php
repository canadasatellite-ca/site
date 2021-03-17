<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Aitoc\OrdersExportImport\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Aitoc\OrdersExportImport\Block\Adminhtml\Profile\Grid\Renderer\Action\UrlBuilder;
use Magento\Framework\UrlInterface;

/**
 * Class ProfileActions
 *
 * @package Aitoc\OrdersExportImport\Ui\Component\Listing\Column
 */
class ExportActions extends Column
{
    /** Url path */
    const CMS_URL_PATH_DOWNLOAD = 'ordersexportimport/export/download';
    const CMS_URL_PATH_VIEW_ORDERS = 'ordersexportimport/export/vieworders';
    const CMS_URL_PATH_DELETE = 'ordersexportimport/export/delete';

    /** @var UrlBuilder */
    public $actionUrlBuilder;

    /** @var UrlInterface */
    public $urlBuilder;

    private $export;

    private $profile;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlBuilder $actionUrlBuilder
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     * @param string $editUrl
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlBuilder $actionUrlBuilder,
        UrlInterface $urlBuilder,
        \Aitoc\OrdersExportImport\Model\Export $export,
        \Aitoc\OrdersExportImport\Model\Profile $profile,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder       = $urlBuilder;
        $this->actionUrlBuilder = $actionUrlBuilder;
        $this->export           = $export;
        $this->profile          = $profile;
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
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['export_id'])) {
                    if ($item['status'] == 2) {
                        $this->export->load($item['export_id']);
                        $profile                 = $this->profile->load($this->export->getProfileId());
                        $config                  = $profile->getUnsConfig();
                        $url                     = \Magento\Framework\App\ObjectManager::getInstance()
                            ->get('Magento\Store\Model\StoreManagerInterface')
                            ->getStore()
                            ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_WEB);
                        $item[$name]['download'] = [
                            'href' => $this->urlBuilder->getUrl(
                                $url
                                . $config['path_local']
                                . "/"
                                . $this->export->getFilename()
                            ),
                            'label' => __('Download')
                        ];
                    }
                    $item[$name]['delete'] = [
                        'href' => $this->urlBuilder->getUrl(
                            self::CMS_URL_PATH_DELETE,
                            ['export_id' => $item['export_id']]
                        ),
                        'label' => __('Delete'),
                        'confirm' => [
                            'title' => __('Delete ${ $.$data.filename }'),
                            'message' => __('Are you sure you wan\'t to delete a ${ $.$data.filename } record?')
                        ]
                    ];
                }
                if (isset($item['identifier'])) {
                    $item[$name]['preview'] = [
                        'href' => $this->actionUrlBuilder->getUrl(
                            $item['identifier'],
                            isset($item['_first_store_id']) ? $item['_first_store_id'] : null,
                            isset($item['store_code']) ? $item['store_code'] : null
                        ),
                        'label' => __('View')
                    ];
                }
            }
        }

        return $dataSource;
    }
}

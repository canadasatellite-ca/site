<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */

namespace Aitoc\OrdersExportImport\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class Status
 * @package Aitoc\OrdersExportImport\Component\Listing\Columns
 */
class Status extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * Status constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components,
        array $data
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if (!$item['status']) {
                    $item[$this->getData('name')] = __('In queue');
                } elseif ($item['status']) {
                    switch ($item['status']) {
                        case 1:
                            $stack = \Magento\Framework\App\ObjectManager::getInstance()
                                ->create('Aitoc\OrdersExportImport\Model\Stack');
                            $list  = $stack->getCollection()
                                ->addFieldToFilter('export_id', $item['export_id'])
                                ->addFieldToFilter('status', 1);
                            foreach ($list as $value) {
                                if (!$value->getError()) {
                                    $item[$this->getData('name')] = __('Process: ') . $value->getPercent() . '%';
                                } else {
                                    $item[$this->getData('name')] = __('Error: ') . $value->getError();
                                }
                            }
                            break;
                        case 2:
                            $item[$this->getData('name')] = __('Complete');
                    }
                }
            }
        }

        return $dataSource;
    }
}

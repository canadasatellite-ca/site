<?php

namespace Interactivated\Quotecheckout\Ui\Component\Listing\Column;

use Magento\Framework\Escaper;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Sales\Model\OrderFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class CustomerComment extends Column
{
    /**
     * @var \Magento\Framework\Escaper
     */
    protected $_escaper;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param OrderFactory $orderFactory
     * @param Escaper $escaper
     * @param array $components
     * @param array $data
     */
    function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        OrderFactory $orderFactory,
        Escaper $escaper,
        array $components = [],
        array $data = []
    ) {
        $this->_orderFactory = $orderFactory;
        $this->_escaper = $escaper;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$this->getData('name')] = $this->prepareItem($item);
            }
        }

        return $dataSource;
    }

    /**
     * Get data
     *
     * @param array $item
     * @return string
     */
    protected function prepareItem(array $item)
    {
        $orderId = $item['entity_id'];
        if (empty($orderId)) {
            return '';
        }

        $order = $this->_orderFactory->create()->load($orderId);
        return $this->_escaper->escapeHtml($order->getMwCustomercommentInfo());
    }
}

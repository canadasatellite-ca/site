<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Report\Quotereport;

/**
 * Class Grid
 * @package Cart2Quote\Quotation\Block\Adminhtml\Report\Quotereport
 */
class Grid extends \Magento\Reports\Block\Adminhtml\Grid\AbstractGrid
{
    /**
     * GROUP BY criteria
     *
     * @var string
     */
    protected $_columnGroupBy = 'period';

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setCountTotals(true);
    }

    /**
     * {@inheritdoc}
     */
    public function getResourceCollectionName()
    {
        return \Cart2Quote\Quotation\Model\ResourceModel\Report\QuoteReport\Collection::class;
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'period',
            [
                'header' => __('Interval'),
                'index' => 'period',
                'sortable' => false,
                'period_type' => $this->getPeriodType(),
                'renderer' => \Magento\Reports\Block\Adminhtml\Sales\Grid\Column\Renderer\Date::class,
                'totals_label' => __('Total'),
                'html_decorators' => ['nobr'],
                'header_css_class' => 'col-period',
                'column_css_class' => 'col-period'
            ]
        );

        $this->addColumn(
            'quotes_count',
            [
                'header' => __('Quotes'),
                'index' => 'quotes_count',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-quotes',
                'column_css_class' => 'col-quotes'
            ]
        );

        $this->addColumn(
            'total_item_qty_quoted',
            [
                'header' => __('Quote Items'),
                'index' => 'total_item_qty_quoted',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-items',
                'column_css_class' => 'col-items'
            ]
        );

        $this->addColumn(
            'total_quoted_amount',
            [
                'header' => __('Quotes Total'),
                'index' => 'total_quoted_amount',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-items',
                'column_css_class' => 'col-items'
            ]
        );

        $this->addColumn(
            'total_tax_amount',
            [
                'header' => __('Quotes Tax'),
                'index' => 'total_tax_amount',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-items',
                'column_css_class' => 'col-items'
            ]
        );

        $this->addColumn(
            'total_shipping_amount',
            [
                'header' => __('Quotes Shipping'),
                'index' => 'total_shipping_amount',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-items',
                'column_css_class' => 'col-items'
            ]
        );

        $this->addColumn(
            'total_qty_quoted',
            [
                'header' => __('Quotes Requested'),
                'index' => 'total_qty_quoted',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-items',
                'column_css_class' => 'col-items'
            ]
        );

        $this->addColumn(
            'total_qty_proposal',
            [
                'header' => __('Quote Proposals'),
                'index' => 'total_qty_proposal',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-items',
                'column_css_class' => 'col-items'
            ]
        );

        $this->addColumn(
            'total_qty_ordered',
            [
                'header' => __('Quotes Ordered'),
                'index' => 'total_qty_ordered',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-items',
                'column_css_class' => 'col-items'
            ]
        );

        $this->addColumn(
            'total_qty_canceled',
            [
                'header' => __('Quotes Canceled'),
                'index' => 'total_qty_canceled',
                'type' => 'number',
                'total' => 'sum',
                'sortable' => false,
                'header_css_class' => 'col-items',
                'column_css_class' => 'col-items'
            ]
        );

        if ($this->getFilterData()->getStoreIds()) {
            $this->setStoreIds(explode(',', $this->getFilterData()->getStoreIds()));
        }

        $this->addExportType('*/*/exportQuotationReportCsv', __('CSV'));
        $this->addExportType('*/*/exportQuotationReportExcel', __('Excel XML'));

        return parent::_prepareColumns();
    }

    /**
     * Add quote status filter
     *
     * @param \Magento\Reports\Model\ResourceModel\Report\Collection\AbstractCollection $collection
     * @param \Magento\Framework\DataObject $filterData
     * @return $this
     */
    protected function _addOrderStatusFilter($collection, $filterData)
    {
        $collection->addOrderStatusFilter($filterData->getData('quote_statuses'));
        return $this;
    }

    /**
     * Apply sorting and filtering to collection
     *
     * @return $this|\Magento\Backend\Block\Widget\Grid
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function _prepareCollection()
    {
        $filterData = $this->getFilterData();
        $quoteStatuses = $filterData->getData('quote_statuses');

        if (is_array($quoteStatuses)) {
            if (count($quoteStatuses) == 1 && strpos($quoteStatuses[0], ',') !== false) {
                $filterData->setData('quote_statuses', explode(',', $quoteStatuses[0]));
            }
        }

        return parent::_prepareCollection();
    }
}
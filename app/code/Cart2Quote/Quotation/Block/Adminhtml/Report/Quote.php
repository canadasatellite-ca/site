<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Block\Adminhtml\Report;

/**
 * Class Quote
 * @package Cart2Quote\Quotation\Block\Adminhtml\Report
 */
class Quote extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Template file
     *
     * @var string
     */
    protected $_template = 'Magento_Reports::report/grid/container.phtml';

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_controller = 'adminhtml_report_quotereport';
        $this->_blockGroup = 'Cart2Quote_Quotation';
        $this->_headerText = __('Quotes Report');
        parent::_construct();

        $this->buttonList->remove('add');
        $this->addButton(
            'filter_form_submit',
            ['label' => __('Show Report'), 'onclick' => 'filterFormSubmit()', 'class' => 'primary']
        );
    }

    /**
     * Get filter URL
     *
     * @return string
     */
    public function getFilterUrl()
    {
        $this->getRequest()->setParam('filter', null);
        return $this->getUrl('quotation/report_quote/quote', ['_current' => true]);
    }
}
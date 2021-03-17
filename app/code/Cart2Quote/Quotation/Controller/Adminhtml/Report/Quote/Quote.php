<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Quotation\Controller\Adminhtml\Report\Quote;

use Magento\Framework\App\Action\HttpGetActionInterface as HttpGetActionInterface;

//Magento 2.2 fix
if (!interface_exists('Magento\Framework\App\Action\HttpGetActionInterface')) {
    class_alias(
        'Cart2Quote\Quotation\Legacy\Magento\Framework\App\Action\HttpGetActionInterface',
        'Magento\Framework\App\Action\HttpGetActionInterface'
    );
}

/**
 * Class Quote
 * @package Cart2Quote\Quotation\Controller\Adminhtml\Report\Quote
 */
class Quote extends \Magento\Reports\Controller\Adminhtml\Report\Sales implements HttpGetActionInterface
{
    /**
     * Quotes report action
     *
     * @return void
     */
    public function execute()
    {
        $this->_showLastExecutionTime(\Cart2Quote\Quotation\Model\Flag::REPORT_QUOTATION_FLAG_CODE, 'quotation');

        $this->_initAction()->_setActiveMenu(
            'Cart2Quote_Quotation::report_quote'
        )->_addBreadcrumb(
            __('Quotes Report'),
            __('Quotes Report')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Quotes Report'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_report_quotereport.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }
}

<?php
/**
 * Magedelight
 * Copyright (C) 2019 Magedelight <info@magedelight.com>
 *
 * @category  Magedelight
 * @package   Magedelight_Subscribenow
 * @copyright Copyright (c) 2019 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Controller\Adminhtml\Report\Sales;

use Magedelight\Subscribenow\Model\Flag;

class FutureProducts extends \Magento\Reports\Controller\Adminhtml\Report\Sales
{
    /**
     * Check is allowed for report.
     *
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Magedelight_Subscribenow::subscribenow_futureproducts');
    }
    
    public function execute()
    {
        $this->_showLastExecutionTime(Flag::REPORT_SUBSCRIBENOW_FUTUREPRODUCTS_FLAG_CODE, 'futureproducts_subscription');

        $this->_initAction()->_setActiveMenu(
            'Magedelight_Subscribenow::report_futureproducts'
        )->_addBreadcrumb(
            __('Future Products Subscription Report'),
            __('Future Products Subscription Report')
        );
        $this->_view->getPage()->getConfig()->getTitle()->prepend(__('Future Products Subscription Report'));

        $gridBlock = $this->_view->getLayout()->getBlock('adminhtml_sales_futureproducts.grid');
        $filterFormBlock = $this->_view->getLayout()->getBlock('grid.filter.form');

        $this->_initReportAction([$gridBlock, $filterFormBlock]);

        $this->_view->renderLayout();
    }
}
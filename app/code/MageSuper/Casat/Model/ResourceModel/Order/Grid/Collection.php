<?php
namespace MageSuper\Casat\Model\ResourceModel\Order\Grid;
use Magento\Sales\Model\ResourceModel\Order\Grid\Collection as OriginalCollection;
use Vendor\ExtendGrid\Helper\Data as Helper;
/**
 * Order grid extended collection
 */
class Collection extends OriginalCollection
{
    protected function _renderFiltersBefore()
    {
        $joinTable = $this->getTable('magesuper_casat_ordercomment');
        $this->getSelect()->joinLeft($joinTable, 'main_table.entity_id = magesuper_casat_ordercomment.order_id', ['comment']);
        parent::_renderFiltersBefore();
    }
}
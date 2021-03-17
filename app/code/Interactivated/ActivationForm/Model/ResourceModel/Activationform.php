<?php

namespace Interactivated\ActivationForm\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\AbstractModel;

/**
 * Faq post mysql resource
 * @method array|null getProductsData()
 */
class Activationform extends AbstractDb {

    /**
     * Initialize resource model
     *
     * @return void
     */
    // @codingStandardsIgnoreStart
    protected function _construct() {
        // Table Name and Primary Key column
        $this->_init('interactivated_activationform', 'request_id');
    }
}

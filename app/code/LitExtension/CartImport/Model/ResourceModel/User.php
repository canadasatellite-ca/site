<?php

namespace LitExtension\CartImport\Model\ResourceModel;

class User extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    protected function _construct()
    {
        $this->_init('leci_user', 'id');
    }
}

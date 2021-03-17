<?php

namespace LitExtension\CartImport\Model\ResourceModel\User;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    protected function _construct()
    {
        $this->_init('LitExtension\CartImport\Model\User', 'LitExtension\CartImport\Model\ResourceModel\User');
    }

}

<?php
/**
 * Copyright © 2016 Aitoc. All rights reserved.
 */
namespace Aitoc\OrdersExportImport\Helper;

/**
 * Class Helper
 *
 * @package Aitoc\OrdersExportImport\Helper
 */
class Helper extends \Magento\Framework\DB\Helper
{
    /**
     * @param $table
     * @return array
     */
    public function getFields($table)
    {
        return array_keys($this->getConnection()->describeTable($table));
    }
}

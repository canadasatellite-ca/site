<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\Quotation\Setup;

use Cart2Quote\Quotation\Model\ResourceModel\Quote\Status\Collection as StatusCollection;

/**
 * Class QuoteSetup
 * Backwards compatible with Magento 2.1 for Commerce Split Database
 * @package Cart2Quote\Quotation\Setup
 */
class QuoteSetup extends \Magento\Quote\Setup\QuoteSetup
{
    /**
     * @var string
     */
    const CONNECTION_NAME = 'checkout';

    /**
     * Get quote connection
     *
     * @return \Magento\Framework\DB\Adapter\AdapterInterface|StatusCollection
     */
    public function getConnection()
    {
        if (method_exists(parent::class, 'getConnection')) {
            return parent::getConnection();
        }

        return $this->getSetup()->getConnection(self::CONNECTION_NAME);
    }

    /**
     * Get table name
     *
     * @param string $table
     * @return string
     */
    public function getTable($table)
    {
        if (method_exists(parent::class, 'getTable')) {
            return parent::getTable($table);
        }

        return $this->getSetup()->getTable($table, self::CONNECTION_NAME);
    }

    /**
     * Drop table
     *
     * @param string $tableName
     */
    public function dropTable($tableName)
    {
        if ($this->getConnection()->isTableExists($tableName)) {
            $this->getConnection()->dropTable($tableName);
        }
    }

    /**
     * Drop column
     *
     * @param string $tableName
     * @param string $columnName
     */
    public function dropColumn($tableName, $columnName)
    {
        if ($this->getConnection()->tableColumnExists($tableName, $columnName)) {
            $this->getConnection()->dropColumn($tableName, $columnName);
        }
    }
}

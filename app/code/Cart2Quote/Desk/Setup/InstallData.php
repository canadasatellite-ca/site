<?php
/**
 *
 * CART2QUOTE CONFIDENTIAL
 * __________________
 *
 *  [2009] - [2016] Cart2Quote B.V.
 *  All Rights Reserved.
 *
 * NOTICE OF LICENSE
 *
 * All information contained herein is, and remains
 * the property of Cart2Quote B.V. and its suppliers,
 * if any.  The intellectual and technical concepts contained
 * herein are proprietary to Cart2Quote B.V.
 * and its suppliers and may be covered by European and Foreign Patents,
 * patents in process, and are protected by trade secret or copyright law.
 * Dissemination of this information or reproduction of this material
 * is strictly forbidden unless prior written permission is obtained
 * from Cart2Quote B.V.
 *
 * @category    Cart2Quote
 * @package     Desk
 * @copyright   Copyright (c) 2016 Cart2Quote B.V. (https://www.cart2quote.com)
 * @license     https://www.cart2quote.com/ordering-licenses(https://www.cart2quote.com)
 */

namespace Cart2Quote\Desk\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

/**
 * Class InstallData
 * @package Cart2Quote\Desk\Setup
 */
class InstallData implements InstallDataInterface
{
    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $this->installStatuses($installer);
        $this->installPriorityType($installer);
    }

    /**
     * Install the statuses values
     *
     * @param ModuleDataSetupInterface $installer
     *
     * @return void
     */
    protected function installStatuses(ModuleDataSetupInterface $installer)
    {
        $query = $installer->getConnection()
            ->query('SELECT * FROM ' . $installer->getTable('desk_ticket_status'));
        if ($query->rowCount() == 0) {
            $this->installTypes(
                $installer,
                $this->getStatusesValues(),
                $installer->getTable('desk_ticket_status')
            );
        }
    }

    /**
     * Install the priority types values
     *
     * @param ModuleDataSetupInterface $installer
     *
     * @return void
     */
    protected function installPriorityType(ModuleDataSetupInterface $installer)
    {
        $query = $installer->getConnection()
            ->query('SELECT * FROM ' . $installer->getTable('desk_ticket_priority'));
        if ($query->rowCount() == 0) {
            $this->installTypes(
                $installer,
                $this->getPriorityValues(),
                $installer->getTable('desk_ticket_priority')
            );
        }
    }

    /**
     * Default install type function
     *
     * @param ModuleDataSetupInterface $installer
     * @param array $values
     * @param string $table
     *
     * @return void
     */
    protected function installTypes(ModuleDataSetupInterface $installer, array $values, $table)
    {
        foreach ($values as $value) {
            $installer->getConnection()->insert($table, ['code' => $value]);
        }
    }

    /**
     * The priority values
     *
     * @return array
     */
    protected function getPriorityValues()
    {
        return [
            'low',
            'normal',
            'high',
            'urgent'
        ];
    }

    /**
     * The status values
     *
     * @return array
     */
    protected function getStatusesValues()
    {
        return [
            'open',
            'pending',
            'solved'
        ];
    }
}

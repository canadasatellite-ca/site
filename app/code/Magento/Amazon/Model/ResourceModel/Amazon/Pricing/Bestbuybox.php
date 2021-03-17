<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\ResourceModel\Amazon\Pricing;

use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Bestbuybox
 */
class Bestbuybox extends AbstractDb
{
    const CHUNK_SIZE = 1000;

    /**
     * Constructor
     */
    protected function _construct()
    {
        $this->_init(
            'channel_amazon_pricing_bestbuybox',
            'id'
        );
    }

    /**
     * Removes entries by asins
     *
     * @param array $removals
     * @param void
     * @throws LocalizedException
     */
    public function removeByAsins(array $removals)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $tableName = $this->getMainTable();

        foreach ($removals as $countryCode => $asins) {
            // where clause
            $where = [
                'asin IN (?)' => array_unique($asins),
                // todo: I don't think this should be int, but how we got in the DB integers other than 0 and 1?!
                'country_code = ?' => (int)$countryCode
            ];

            $connection->delete($tableName, $where);
        }
    }

    /**
     * Removes entries by asins
     *
     * @param string $countryCode
     * @param array $asins
     * @throws LocalizedException
     */
    public function removeAsinsByCountryCode(string $countryCode, array $asins)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $tableName = $this->getMainTable();

        foreach (array_chunk(array_unique($asins), 1000) as $asinsChunk) {
            // where clause
            $where = [
                'asin IN (?)' => $asinsChunk,
                'country_code = ?' => $countryCode
            ];

            $connection->delete($tableName, $where);
        }
    }

    /**
     * Inserts bbb pricing
     *
     * @param array $data
     * @return void
     * @throws LocalizedException
     */
    public function insert(array $data)
    {
        /** @var AdapterInterface */
        $connection = $this->getConnection();
        /** @var string */
        $connection->insertOnDuplicate($this->getMainTable(), $data, []);
    }
}

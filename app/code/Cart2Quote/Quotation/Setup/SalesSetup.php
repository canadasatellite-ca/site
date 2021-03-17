<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 * @codingStandardsIgnoreFile
 */

namespace Cart2Quote\Quotation\Setup;

/**
 * Setup Model of Quotation Module
 */
class SalesSetup extends \Magento\Sales\Setup\SalesSetup
{
    /**
     * List of entities converted from EAV to flat data structure
     *
     * @var array $_flatEntityTables
     */
    protected $_flatEntityTables = [
        'quote' => 'quotation_quote',
    ];

    /**
     * List of entities used with separate grid table
     *
     * @var string[] $_flatEntitiesGrid
     */
    protected $_flatEntitiesGrid = ['quote'];

    /**
     * Get default entities
     *
     * @return array
     */
    public function getDefaultEntities()
    {
        $entities = [
            'quote' => [
                'entity_model' => \Cart2Quote\Quotation\Model\ResourceModel\Quote::class,
                'table' => 'quotation_quote',
                'increment_model' => \Magento\Eav\Model\Entity\Increment\Alphanum::class,
                'increment_per_store' => true,
                'attributes' => [],
            ],
        ];

        return $entities;
    }
}

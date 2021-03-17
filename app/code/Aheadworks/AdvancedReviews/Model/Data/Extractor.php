<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Data;

use Magento\Framework\Stdlib\ArrayManager;

/**
 * Class Extractor
 *
 * @package Aheadworks\AdvancedReviews\Model\Data
 */
class Extractor
{
    /**
     * @var ArrayManager
     */
    private $arrayManager;

    /**
     * @var array
     */
    private $fieldNames = [];

    /**
     * @param ArrayManager $arrayManager
     * @param array $fieldNames
     */
    public function __construct(
        ArrayManager $arrayManager,
        array $fieldNames = []
    ) {
        $this->arrayManager = $arrayManager;
        $this->fieldNames = $fieldNames;
    }

    /**
     * Extract fields from specific data array
     *
     * @param array $data
     * @return array
     */
    public function extractFields($data)
    {
        $fieldsData = [];
        foreach ($this->fieldNames as $fieldName) {
            $fieldValue = $this->arrayManager->get($fieldName, $data);
            $fieldsData = $this->arrayManager->set($fieldName, $fieldsData, $fieldValue);
        }
        return $fieldsData;
    }
}

<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Data\Processor;

use Aheadworks\AdvancedReviews\Model\Data\ProcessorInterface;
use Magento\Framework\Stdlib\BooleanUtils;

/**
 * Class Boolean
 *
 * @package Aheadworks\AdvancedReviews\Model\Data\Processor
 */
class Boolean implements ProcessorInterface
{
    /**
     * @var BooleanUtils
     */
    private $booleanUtils;

    /**
     * @var string
     */
    private $fieldName;

    /**
     * @param BooleanUtils $booleanUtils
     * @param string $fieldName
     */
    public function __construct(
        BooleanUtils $booleanUtils,
        string $fieldName = ''
    ) {
        $this->booleanUtils = $booleanUtils;
        $this->fieldName = $fieldName;
    }

    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (isset($data[$this->fieldName])) {
            $data[$this->fieldName] = $this->booleanUtils->toBoolean($data[$this->fieldName]);
        }
        return $data;
    }
}

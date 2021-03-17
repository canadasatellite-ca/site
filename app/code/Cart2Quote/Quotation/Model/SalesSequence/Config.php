<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\SalesSequence;

/**
 * Class Config
 * - configuration container for sequence
 *
 * @package Cart2Quote\Quotation\Model\SalesSequence
 */
class Config extends \Magento\SalesSequence\Model\Config
{
    use \Cart2Quote\Features\Traits\Model\SalesSequence\Config {
        toOptionArray as private traitToOptionArray;
    }

    /**
     * Default sequence values
     * - Prefix represents prefix for sequence: AA000
     * - Suffix represents suffix: 000AA
     * - startValue represents initial value
     * - warning value will be using for alert messages when increment closing to overflow
     * - maxValue represents last available increment id in system
     *
     * @var array
     */
    protected $defaultValues = [
        'prefix' => 'Q15.',
        'suffix' => '',
        'startValue' => 1,
        'step' => 1,
        'warningValue' => 4294966295,
        'maxValue' => 4294967295,
    ];

    /**
     * Default toOptionArray function
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->traitToOptionArray();
    }
}

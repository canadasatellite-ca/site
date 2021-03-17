<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\Form\Amazon\Attribute\Value;

use Magento\Catalog\Model\Product\AttributeSet\Options;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class AttributeSetIds
 */
class AttributeSetIds implements OptionSourceInterface
{
    /** @var Options $options */
    protected $options;

    /**
     * @param Options $options
     */
    public function __construct(
        Options $options
    ) {
        $this->options = $options;
    }

    /**
     * Creates the core attribute ids
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->options->toOptionArray();
    }
}

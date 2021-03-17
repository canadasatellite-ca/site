<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\Quotation\Model\Config\Source;

/**
 * Class Options
 *
 * @package Cart2Quote\Quotation\Model\Config\Source\Quote
 */
class Options implements \Magento\Framework\Data\OptionSourceInterface
{
    use \Cart2Quote\Features\Traits\Model\Config\Source\Options {
        toOptionArray as private traitToOptionArray;
        getOptions as private traitGetOptions;
    }

    /**
     * @var \Cart2Quote\Quotation\Model\Config\Source\Form\Field\Select\OptionInterface[]
     */
    private $options;

    /**
     * Strategies constructor.
     *
     * @param \Cart2Quote\Quotation\Model\Config\Source\Form\Field\Select\OptionInterface[] $options
     */
    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    /**
     * Return array of options as value-label pairs, eg. value => label
     *
     * @return array
     */
    public function toOptionArray()
    {
        return $this->traitToOptionArray();
    }

    /**
     * Get options
     *
     * @return \Cart2Quote\Quotation\Model\Config\Source\Form\Field\Select\OptionInterface[]
     */
    public function getOptions()
    {
        return $this->traitGetOptions();
    }
}

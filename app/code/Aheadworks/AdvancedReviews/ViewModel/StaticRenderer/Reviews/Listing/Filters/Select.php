<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Listing\Filters;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Select
 *
 * @package Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Listing\Filters
 */
class Select implements ArgumentInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var OptionSourceInterface
     */
    private $optionsProvider;

    /**
     * @param string $id
     * @param OptionSourceInterface $optionsProvider
     */
    public function __construct(
        string $id,
        OptionSourceInterface $optionsProvider
    ) {
        $this->id = $id;
        $this->optionsProvider = $optionsProvider;
    }

    /**
     * Retrieve filter id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Retrieve options array
     *
     * @return array
     */
    public function getOptions()
    {
        return $this->optionsProvider->toOptionArray();
    }

    /**
     * Retrieve option value from option data array
     *
     * @param array $optionData
     * @return string
     */
    public function getOptionValue($optionData)
    {
        return (is_array($optionData) && isset($optionData['value']))
            ? $optionData['value']
            : '';
    }

    /**
     * Retrieve option label from option data array
     *
     * @param array $optionData
     * @return string
     */
    public function getOptionLabel($optionData)
    {
        return (is_array($optionData) && isset($optionData['label']))
            ? $optionData['label']
            : '';
    }
}

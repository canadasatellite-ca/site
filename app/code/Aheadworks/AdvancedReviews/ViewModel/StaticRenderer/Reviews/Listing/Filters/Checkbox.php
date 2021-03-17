<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Listing\Filters;

use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class Checkbox
 *
 * @package Aheadworks\AdvancedReviews\ViewModel\StaticRenderer\Reviews\Listing\Filters
 */
class Checkbox implements ArgumentInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $label;

    /**
     * @param string $id
     * @param string $label
     */
    public function __construct(
        string $id = "",
        string $label = ""
    ) {
        $this->id = $id;
        $this->label = $label;
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
     * Retrieve filter label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
}

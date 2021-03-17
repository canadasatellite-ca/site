<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\ViewModel;

/**
 * Interface IdentityInterface
 *
 * @package Aheadworks\AdvancedReviews\ViewModel
 */
interface IdentityInterface extends \Magento\Framework\View\Element\Block\ArgumentInterface
{
    /**
     * Return unique block ID(s)
     *
     * @return string[]
     */
    public function getBlockIdentities();
}

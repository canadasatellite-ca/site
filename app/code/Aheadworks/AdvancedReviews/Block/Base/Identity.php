<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Block\Base;

use Aheadworks\AdvancedReviews\Block\Base;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * Class Identity
 *
 * @method \Aheadworks\AdvancedReviews\ViewModel\IdentityInterface getViewModel()
 *
 * @package Aheadworks\AdvancedReviews\Block\Base
 */
class Identity extends Base implements IdentityInterface
{
    /**
     * {@inheritdoc}
     */
    public function getIdentities()
    {
        $identities = [];
        $identities = array_merge($identities, $this->getViewModel()->getBlockIdentities());
        return $identities;
    }
}

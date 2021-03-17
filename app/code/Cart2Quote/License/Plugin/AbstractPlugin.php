<?php
/**
 * Copyright (c) 2019. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Cart2Quote\License\Plugin;

/**
 * Class AbstractPlugin
 * @package Cart2Quote\License\Plugin
 * @SuppressWarnings(PHPMD.FinalImplementation)
 */
abstract class AbstractPlugin
{
    /**
     * @return bool
     */
    final public function isAllowed()
    {
        return $this->getFeature()->isAllowed();
    }

    /**
     * @return null|\Cart2Quote\License\Feature\AbstractFeature
     */
    final private function getFeature()
    {
        return $this->getFeatureList()->getFeatureByPlugin(get_class($this));
    }

    /**
     * @return \Cart2Quote\License\Feature\FeatureList
     */
    final private function getFeatureList()
    {
        return \Cart2Quote\Features\Feature\FeatureList::getInstance($this);
    }
}

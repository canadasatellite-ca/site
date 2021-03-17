<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Status;

use Aheadworks\AdvancedReviews\Model\Config;

/**
 * Class Comment
 * @package Aheadworks\AdvancedReviews\Model\Status\Resolver
 */
abstract class AbstractResolver
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @param Config $config
     */
    public function __construct(
        Config $config
    ) {
        $this->config = $config;
    }

    /**
     * Resolve status for new instance
     *
     * @param int|null $storeId
     * @return int
     */
    abstract public function getNewInstanceStatus($storeId);
}

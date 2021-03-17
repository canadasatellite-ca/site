<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Author\Resolver;

/**
 * Class Pool
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Author\Resolver
 */
class Pool
{
    /**
     * @var ResolverInterface[]
     */
    private $resolvers;

    /**
     * @param array $resolvers
     */
    public function __construct(
        $resolvers = []
    ) {
        $this->resolvers = $resolvers;
    }

    /**
     * Retrieve resolver instance by author type
     *
     * @param int $authorType
     * @return ResolverInterface|null
     */
    public function getResolverByAuthorType($authorType)
    {
        if (isset($this->resolvers[$authorType])
            && ($this->resolvers[$authorType] instanceof ResolverInterface)
        ) {
            return $this->resolvers[$authorType];
        }

        return null;
    }
}

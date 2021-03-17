<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Processor;

use Aheadworks\AdvancedReviews\Model\Review\ProcessorInterface;
use Aheadworks\AdvancedReviews\Model\Review\Author\Type\Resolver as AuthorTypeResolver;

/**
 * Class AuthorType
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Processor
 */
class AuthorType implements ProcessorInterface
{
    /**
     * @var AuthorTypeResolver
     */
    private $authorTypeResolver;

    /**
     * @param AuthorTypeResolver $authorTypeResolver
     */
    public function __construct(
        AuthorTypeResolver $authorTypeResolver
    ) {
        $this->authorTypeResolver = $authorTypeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function process($review)
    {
        $storeId = $review->getStoreId();
        if (isset($storeId)) {
            $authorType = $this->authorTypeResolver->resolveAuthorType(
                $storeId,
                $review->getCustomerId()
            );
            $review->setAuthorType($authorType);
        }
        return $review;
    }
}

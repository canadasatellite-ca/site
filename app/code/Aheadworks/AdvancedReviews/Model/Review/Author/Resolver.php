<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Author;

use Aheadworks\AdvancedReviews\Model\Review\Author\Resolver\Pool as ResolverPool;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Class Resolver
 * @package Aheadworks\AdvancedReviews\Model\Review\Author
 */
class Resolver
{
    /**
     * @var ResolverPool
     */
    private $resolverPool;

    /**
     * @param ResolverPool $resolverPool
     */
    public function __construct(
        ResolverPool $resolverPool
    ) {
        $this->resolverPool = $resolverPool;
    }

    /**
     * Get author label for backend
     *
     * @param int $authorType
     * @param int|null $customerId
     * @return \Magento\Framework\Phrase|string
     */
    public function getBackendLabel($authorType, $customerId)
    {
        $authorLabel = '';

        $resolver = $this->resolverPool->getResolverByAuthorType($authorType);
        if ($resolver) {
            $authorLabel = $resolver->getBackendLabel($customerId);
        }

        return $authorLabel;
    }

    /**
     * Get author url for backend
     *
     * @param int $authorType
     * @param int|null $customerId
     * @return string
     */
    public function getBackendUrl($authorType, $customerId)
    {
        $authorUrl = '';

        $resolver = $this->resolverPool->getResolverByAuthorType($authorType);
        if ($resolver) {
            $authorUrl = $resolver->getBackendUrl($customerId);
        }

        return $authorUrl;
    }

    /**
     * Get author name
     *
     * @param ReviewInterface $review
     * @return string|null
     */
    public function getName($review)
    {
        $authorName = null;

        $resolver = $this->resolverPool->getResolverByAuthorType($review->getAuthorType());
        if ($resolver) {
            $authorName = $resolver->getName($review);
        }

        return $authorName;
    }

    /**
     * Get author email
     *
     * @param ReviewInterface $review
     * @return string|null
     */
    public function getEmail($review)
    {
        $authorEmail = null;

        $resolver = $this->resolverPool->getResolverByAuthorType($review->getAuthorType());
        if ($resolver) {
            $authorEmail = $resolver->getEmail($review);
        }

        return $authorEmail;
    }
}

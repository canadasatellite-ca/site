<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Review\Processor;

use Aheadworks\AdvancedReviews\Model\Review\ProcessorInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Aheadworks\AdvancedReviews\Model\Store\Resolver as StoreResolver;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\AdvancedReviews\Model\Source\Review\AuthorType as ReviewAuthorType;

/**
 * Class GuestEmail
 *
 * @package Aheadworks\AdvancedReviews\Model\Review\Processor
 */
class GuestEmail implements ProcessorInterface
{
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * @var StoreResolver
     */
    private $storeResolver;

    /**
     * @param CustomerRepositoryInterface $customerRepository
     * @param StoreResolver $storeResolver
     */
    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        StoreResolver $storeResolver
    ) {
        $this->customerRepository = $customerRepository;
        $this->storeResolver = $storeResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function process($review)
    {
        if ($review->getAuthorType() == ReviewAuthorType::GUEST) {
            $storeId = $review->getStoreId();
            $email = $review->getEmail();
            if (isset($storeId) && isset($email)) {
                $websiteId = $this->storeResolver->getWebsiteIdByStoreId($storeId);
                if (isset($websiteId)) {
                    try {
                        $customer = $this->customerRepository->get($email, $websiteId);
                        $review->setCustomerId($customer->getId());
                        $review->setEmail('');
                    } catch (LocalizedException $exception) {
                        $review->setCustomerId(null);
                        $review->setEmail($email);
                    }
                }
            }
        }
        return $review;
    }
}

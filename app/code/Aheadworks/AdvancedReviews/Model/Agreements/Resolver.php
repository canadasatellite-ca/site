<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Agreements;

use Magento\CheckoutAgreements\Api\Data\AgreementInterface;
use Magento\CheckoutAgreements\Api\CheckoutAgreementsRepositoryInterface;
use Magento\CheckoutAgreements\Model\AgreementModeOptions;
use Magento\CheckoutAgreements\Model\ResourceModel\Agreement\CollectionFactory as CheckoutAgreementsCollectionFactory;
use Magento\CheckoutAgreements\Model\ResourceModel\Agreement\Collection as CheckoutAgreementsCollection;

/**
 * Class Resolver
 *
 * @package Aheadworks\AdvancedReviews\Model\Agreements
 */
class Resolver
{
    /**
     * @var CheckoutAgreementsRepositoryInterface
     */
    private $checkoutAgreementsRepository;

    /**
     * @var CheckoutAgreementsCollectionFactory
     */
    private $checkoutAgreementsCollectionFactory;

    /**
     * @param CheckoutAgreementsRepositoryInterface $checkoutAgreementsRepository
     * @param CheckoutAgreementsCollectionFactory $checkoutAgreementsCollectionFactory
     */
    public function __construct(
        CheckoutAgreementsRepositoryInterface $checkoutAgreementsRepository,
        CheckoutAgreementsCollectionFactory $checkoutAgreementsCollectionFactory
    ) {
        $this->checkoutAgreementsRepository = $checkoutAgreementsRepository;
        $this->checkoutAgreementsCollectionFactory = $checkoutAgreementsCollectionFactory;
    }

    /**
     * Retrieve array of agreements for reviews
     *
     * @param int $storeId
     * @param bool $onlyRequiredAgreementsFlag
     * @return AgreementInterface[]
     */
    public function getAgreementsForReviews($storeId, $onlyRequiredAgreementsFlag = false)
    {
        /** @var CheckoutAgreementsCollection $checkoutAgreementsCollection
         *
         * Direct usage of collection, because it's impossible to specify search criteria for M2.2.X
         * @see \Magento\CheckoutAgreements\Api\CheckoutAgreementsRepositoryInterface
         * @see \Magento\CheckoutAgreements\Api\CheckoutAgreementsListInterface
         */
        $checkoutAgreementsCollection = $this->checkoutAgreementsCollectionFactory->create();

        $checkoutAgreementsCollection
            ->addStoreFilter($storeId)
            ->addFieldToFilter(AgreementInterface::IS_ACTIVE, true);

        if ($onlyRequiredAgreementsFlag) {
            $checkoutAgreementsCollection->addFieldToFilter(
                AgreementInterface::MODE,
                AgreementModeOptions::MODE_MANUAL
            );
        }

        $checkoutAgreements = [];
        /** @var AgreementInterface $item */
        foreach ($checkoutAgreementsCollection->getItems() as $item) {
            $checkoutAgreements[] = $this->checkoutAgreementsRepository->get($item->getAgreementId());
        }

        return $checkoutAgreements;
    }
}

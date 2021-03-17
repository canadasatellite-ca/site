<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Data\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Data\ProcessorInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Agreements\Checker as AgreementsChecker;
use Aheadworks\AdvancedReviews\Model\Agreements\Resolver as AgreementsResolver;
use Magento\CheckoutAgreements\Api\Data\AgreementInterface;

/**
 * Class Agreements
 *
 * @package Aheadworks\AdvancedReviews\Model\Data\Processor\Review
 */
class Agreements implements ProcessorInterface
{
    /**
     * Agreements data key in the post data array
     */
    const AGREEMENTS_DATA_KEY = 'agreement';

    /**
     * @var AgreementsChecker
     */
    private $agreementsChecker;

    /**
     * @var AgreementsResolver
     */
    private $agreementsResolver;

    /**
     * @param AgreementsChecker $agreementsChecker
     * @param AgreementsResolver $agreementsResolver
     */
    public function __construct(
        AgreementsChecker $agreementsChecker,
        AgreementsResolver $agreementsResolver
    ) {
        $this->agreementsChecker = $agreementsChecker;
        $this->agreementsResolver = $agreementsResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        if (isset($data[ReviewInterface::STORE_ID])) {
            $currentStoreId = $data[ReviewInterface::STORE_ID];
            if ($this->agreementsChecker->areAgreementsEnabled($currentStoreId)) {
                $confirmedAgreementsIds = $this->getConfirmedAgreementsIds($data);
                $requiredAgreements = $this->agreementsResolver->getAgreementsForReviews(
                    $currentStoreId,
                    true
                );
                $data[ReviewInterface::ARE_AGREEMENTS_CONFIRMED] = $this->areAllRequiredAgreementsConfirmed(
                    $requiredAgreements,
                    $confirmedAgreementsIds
                );
            }
        }
        return $data;
    }

    /**
     * Retrieve array of confirmed agreements ids from data array
     *
     * @param array $data
     * @return array
     */
    private function getConfirmedAgreementsIds($data)
    {
        $confirmedAgreementsIds = [];
        $agreementsData = isset($data[self::AGREEMENTS_DATA_KEY]) ? $data[self::AGREEMENTS_DATA_KEY] : [];
        foreach ($agreementsData as $agreementId => $agreementFlag) {
            if (!empty($agreementFlag)) {
                $confirmedAgreementsIds[] = $agreementId;
            }
        }
        return $confirmedAgreementsIds;
    }

    /**
     * Check if all required agreements are confirmed
     *
     * @param AgreementInterface[] $requiredAgreements
     * @param array $confirmedAgreementsIds
     * @return bool
     */
    private function areAllRequiredAgreementsConfirmed($requiredAgreements, $confirmedAgreementsIds)
    {
        $areAllRequiredAgreementsConfirmed = true;
        /** @var AgreementInterface $agreement */
        foreach ($requiredAgreements as $agreement) {
            if (!in_array($agreement->getAgreementId(), $confirmedAgreementsIds)) {
                $areAllRequiredAgreementsConfirmed = false;
                break;
            }
        }
        return $areAllRequiredAgreementsConfirmed;
    }
}

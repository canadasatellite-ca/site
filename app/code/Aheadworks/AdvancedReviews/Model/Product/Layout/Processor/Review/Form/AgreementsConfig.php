<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review\Form;

use Aheadworks\AdvancedReviews\Model\Agreements\Checker as AgreementsChecker;
use Aheadworks\AdvancedReviews\Model\Agreements\Resolver as AgreementsResolver;
use Magento\Framework\Escaper;
use Magento\CheckoutAgreements\Api\Data\AgreementInterface;
use Magento\CheckoutAgreements\Model\AgreementModeOptions;

/**
 * Class AgreementsConfig
 *
 * @package Aheadworks\AdvancedReviews\Model\Product\Layout\Processor\Review\Form
 */
class AgreementsConfig
{
    /**
     * @var AgreementsChecker
     */
    private $agreementsChecker;

    /**
     * @var AgreementsResolver
     */
    private $agreementsResolver;

    /**
     * @var Escaper
     */
    private $escaper;

    /**
     * @param AgreementsChecker $agreementsChecker
     * @param AgreementsResolver $agreementsResolver
     * @param Escaper $escaper
     */
    public function __construct(
        AgreementsChecker $agreementsChecker,
        AgreementsResolver $agreementsResolver,
        Escaper $escaper
    ) {
        $this->agreementsChecker = $agreementsChecker;
        $this->agreementsResolver = $agreementsResolver;
        $this->escaper = $escaper;
    }

    /**
     * Retrieve config data array for review agreements
     *
     * @param int|null $storeId
     * @return array
     */
    public function getConfigData($storeId = null)
    {
        $areAgreementsEnabled = $this->agreementsChecker->areAgreementsEnabled($storeId);
        return [
            'are_agreements_enabled' => $areAgreementsEnabled,
            'is_need_to_show_for_guests' => $this->agreementsChecker->isNeedToShowForGuests($storeId),
            'is_need_to_show_for_customers' => $this->agreementsChecker->isNeedToShowForCustomers($storeId),
            'agreements_data' => $areAgreementsEnabled ? $this->getReviewAgreementsData($storeId) : [],
        ];
    }

    /**
     * Retrieve review agreements data array
     *
     * @param int|null $storeId
     * @return array
     */
    protected function getReviewAgreementsData($storeId)
    {
        $agreementsData = [];
        if (!empty($storeId)) {
            $agreementsList = $this->agreementsResolver->getAgreementsForReviews($storeId);
            /** @var AgreementInterface $agreement */
            foreach ($agreementsList as $agreement) {
                $agreementsData[] = [
                    'content' => $agreement->getIsHtml()
                        ? $agreement->getContent()
                        : nl2br($this->escaper->escapeHtml($agreement->getContent())),
                    'checkboxText' => $agreement->getCheckboxText(),
                    'isRequired' => ($agreement->getMode() == AgreementModeOptions::MODE_MANUAL),
                    'agreementId' => $agreement->getAgreementId()
                ];
            }
        }
        return $agreementsData;
    }
}

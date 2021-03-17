<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\AbstractModifier;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Source\Review\IsVerifiedBuyer as IsVerifiedBuyerSource;

/**
 * Class IsVerifiedBuyer
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend
 */
class IsVerifiedBuyer extends AbstractModifier
{
    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if ($this->isSetId($data)) {
            $this->prepareIsVerifiedBuyerField($data);
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Prepare is verified buyer field
     *
     * @param array $data
     * @return array
     */
    private function prepareIsVerifiedBuyerField(&$data)
    {
        if (isset($data[ReviewInterface::IS_VERIFIED_BUYER])) {
            $verifiedBuyerLabel = $this->getVerifiedBuyerLabel($data[ReviewInterface::IS_VERIFIED_BUYER]);
            $data[ReviewInterface::IS_VERIFIED_BUYER] = $verifiedBuyerLabel;
        }
        return $data;
    }

    /**
     * Retrieve label for verified buyer column
     *
     * @param int $verifiedBuyerValue
     * @return \Magento\Framework\Phrase|string
     */
    private function getVerifiedBuyerLabel($verifiedBuyerValue)
    {
        $verifiedBuyerLabel = "";
        if ($verifiedBuyerValue == IsVerifiedBuyerSource::YES) {
            $verifiedBuyerLabel = __("Verified Buyer");
        }
        return $verifiedBuyerLabel;
    }
}

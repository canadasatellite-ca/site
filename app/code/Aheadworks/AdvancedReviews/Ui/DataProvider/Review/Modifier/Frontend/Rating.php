<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\AbstractModifier;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\Review\Rating\Resolver as RatingResolver;

/**
 * Class Rating
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend
 */
class Rating extends AbstractModifier
{
    /**
     * @var RatingResolver
     */
    private $ratingResolver;

    /**
     * @param RatingResolver $ratingResolver
     */
    public function __construct(
        RatingResolver $ratingResolver
    ) {
        $this->ratingResolver = $ratingResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if ($this->isSetId($data)) {
            $this->prepareRatingField($data);
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
     * Prepare rating field
     *
     * @param array $data
     * @return array
     */
    private function prepareRatingField(&$data)
    {
        if (isset($data[ReviewInterface::RATING])) {
            $ratingTitle = $this->ratingResolver->getRatingTitle($data[ReviewInterface::RATING]);
            $ratingAbsoluteValue = $this->ratingResolver->getRatingAbsoluteValue($data[ReviewInterface::RATING]);
            $data[ReviewInterface::RATING . '_title'] = $ratingTitle;
            $data[ReviewInterface::RATING . '_absolute_value'] = $ratingAbsoluteValue;
        }
        return $data;
    }
}

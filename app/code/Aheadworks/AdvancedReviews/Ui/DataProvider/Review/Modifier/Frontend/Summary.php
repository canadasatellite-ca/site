<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\AbstractModifier;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Class Summary
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend
 */
class Summary extends AbstractModifier
{
    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        return $meta;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if ($this->isSetId($data)) {
            $this->prepareSummaryField($data);
        }
        return $data;
    }

    /**
     * Prepare summary field
     *
     * @param array $data
     * @return array
     */
    private function prepareSummaryField(&$data)
    {
        if ((!isset($data[ReviewInterface::SUMMARY]))
            || (isset($data[ReviewInterface::SUMMARY]) && empty($data[ReviewInterface::SUMMARY]))
        ) {
            $data[ReviewInterface::SUMMARY] = __("Not specified");
        }
        return $data;
    }
}

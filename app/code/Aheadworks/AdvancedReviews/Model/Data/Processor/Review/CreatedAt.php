<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\Data\Processor\Review;

use Aheadworks\AdvancedReviews\Model\Data\ProcessorInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Model\DateTime\Formatter as DateTimeFormatter;

/**
 * Class CreatedAt
 *
 * @package Aheadworks\AdvancedReviews\Model\Data\Processor\Review
 */
class CreatedAt implements ProcessorInterface
{
    /**
     * @var DateTimeFormatter
     */
    private $dateTimeFormatter;

    /**
     * @param DateTimeFormatter $dateTimeFormatter
     */
    public function __construct(
        DateTimeFormatter $dateTimeFormatter
    ) {
        $this->dateTimeFormatter = $dateTimeFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function process($data)
    {
        $data[ReviewInterface::CREATED_AT] = $this->dateTimeFormatter->getDateTimeInDbFormat(
            isset($data[ReviewInterface::CREATED_AT]) ?
                $data[ReviewInterface::CREATED_AT] :
                null
        );
        return $data;
    }
}

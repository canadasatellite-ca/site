<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend;

use Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\AbstractModifier;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Aheadworks\AdvancedReviews\Model\DateTime\Formatter as DateTimeFormatter;
use Magento\Framework\Stdlib\DateTime as StdlibDateTime;

/**
 * Class CreatedAt
 *
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review\Modifier\Frontend
 */
class CreatedAt extends AbstractModifier
{
    /**
     * @var DateTimeFormatter
     */
    private $dateTimeFormatter;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var int
     */
    private $dateFormat;

    /**
     * @param DateTimeFormatter $dateTimeFormatter
     * @param StoreManagerInterface $storeManager
     * @param int $dateFormat
     */
    public function __construct(
        DateTimeFormatter $dateTimeFormatter,
        StoreManagerInterface $storeManager,
        $dateFormat = \IntlDateFormatter::MEDIUM
    ) {
        $this->dateTimeFormatter = $dateTimeFormatter;
        $this->storeManager = $storeManager;
        $this->dateFormat = $dateFormat;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        if ($this->isSetId($data)) {
            $this->prepareCreatedAtField($data);
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
     * Prepare created at field
     *
     * @param array $data
     * @return array
     */
    private function prepareCreatedAtField(&$data)
    {
        if (isset($data[ReviewInterface::CREATED_AT])) {
            $createdAt = $data[ReviewInterface::CREATED_AT];
            $currentStoreId = $this->getCurrentStoreId();
            $formattedDate = $this->dateTimeFormatter->getLocalizedDate(
                $createdAt,
                $currentStoreId,
                $this->dateFormat
            );
            $dateInIsoFormat = $this->dateTimeFormatter->getLocalizedDateTime(
                $createdAt,
                $currentStoreId,
                StdlibDateTime::DATE_INTERNAL_FORMAT
            );
            $data[ReviewInterface::CREATED_AT] = $formattedDate;
            $data[ReviewInterface::CREATED_AT . '_in_iso_format'] = $dateInIsoFormat;
        }
        return $data;
    }

    /**
     * Retrieve current store id
     *
     * @return int|null
     */
    private function getCurrentStoreId()
    {
        try {
            $currentStoreId = $this->storeManager->getStore(true)->getId();
        } catch (NoSuchEntityException $exception) {
            $currentStoreId = null;
        }
        return $currentStoreId;
    }
}

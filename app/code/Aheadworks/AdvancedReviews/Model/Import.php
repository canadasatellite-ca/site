<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model;

use Aheadworks\AdvancedReviews\Model\ResourceModel\Import as ImportResource;
use Aheadworks\AdvancedReviews\Api\ReviewRepositoryInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterfaceFactory;
use Aheadworks\AdvancedReviews\Model\Source\Review\RatingValue;
use Magento\Framework\Api\DataObjectHelper;
use Magento\Framework\Exception\LocalizedException;
use Aheadworks\AdvancedReviews\Model\Import\Exception\ImportReviewsException;
use Magento\Store\Model\Store;
use Aheadworks\AdvancedReviews\Model\Review\Author\Type\Resolver as AuthorTypeResolver;

/**
 * Class Import
 * @package Aheadworks\AdvancedReviews\Model
 */
class Import
{
    /**
     * @var ImportResource
     */
    private $importResource;

    /**
     * @var ReviewInterfaceFactory
     */
    private $reviewDataFactory;

    /**
     * @var DataObjectHelper
     */
    private $dataObjectHelper;

    /**
     * @var ReviewRepositoryInterface
     */
    private $reviewRepository;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var AuthorTypeResolver
     */
    private $authorTypeResolver;

    /**
     * @param ImportResource $importResource
     * @param ReviewRepositoryInterface $reviewRepository
     * @param ReviewInterfaceFactory $reviewDataFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param Config $config
     * @param AuthorTypeResolver $authorTypeResolver
     */
    public function __construct(
        ImportResource $importResource,
        ReviewRepositoryInterface $reviewRepository,
        ReviewInterfaceFactory $reviewDataFactory,
        DataObjectHelper $dataObjectHelper,
        Config $config,
        AuthorTypeResolver $authorTypeResolver
    ) {
        $this->importResource = $importResource;
        $this->reviewRepository = $reviewRepository;
        $this->reviewDataFactory = $reviewDataFactory;
        $this->dataObjectHelper = $dataObjectHelper;
        $this->config = $config;
        $this->authorTypeResolver = $authorTypeResolver;
    }

    /**
     * Import existing reviews
     *
     * @return int
     * @throws ImportReviewsException
     */
    public function importExistingReviews()
    {
        $lastImportedReviewId = $this->getLastImportedReviewId();
        $reviewsData = $this->importResource->getExistingReviewsData($lastImportedReviewId);
        $totalImported = 0;

        try {
            while (!empty($reviewsData)) {
                foreach ($reviewsData as $reviewData) {
                    $preparedReviewData = $this->prepareReviewData($reviewData);
                    $review = $this->getReviewObject($preparedReviewData);
                    $this->reviewRepository->save($review);
                    $lastImportedReviewId = $preparedReviewData[ImportResource::NATIVE_REVIEW_ID];
                    $totalImported++;
                }
                $reviewsData = $this->importResource->getExistingReviewsData($lastImportedReviewId);
            }
        } catch (LocalizedException $e) {
            $this->saveLastImportedReviewId($lastImportedReviewId);
            throw new ImportReviewsException($e->getMessage());
        } catch (\Exception $e) {
            $this->saveLastImportedReviewId($lastImportedReviewId);
            throw new ImportReviewsException($e->getMessage());
        }
        $this->saveLastImportedReviewId($lastImportedReviewId);

        return $totalImported;
    }

    /**
     * Prepare review data
     *
     * @param array $reviewData
     * @return array
     */
    private function prepareReviewData($reviewData)
    {
        if (isset($reviewData[ReviewInterface::SHARED_STORE_IDS])) {
            $preparedSharedStores = $this->prepareSharedStores(
                $reviewData[ReviewInterface::SHARED_STORE_IDS],
                $reviewData[ReviewInterface::STORE_ID]
            );
            $reviewData[ReviewInterface::SHARED_STORE_IDS] = $preparedSharedStores;
        }
        if (isset($reviewData[ReviewInterface::RATING])) {
            $preparedRating = $this->prepareRating($reviewData[ReviewInterface::RATING]);
            $reviewData[ReviewInterface::RATING] = $preparedRating;
        }
        $reviewData[ReviewInterface::AUTHOR_TYPE] = $this->authorTypeResolver->resolveAuthorType(
            $reviewData[ReviewInterface::STORE_ID],
            $reviewData[ReviewInterface::CUSTOMER_ID]
        );

        return $reviewData;
    }

    /**
     * Prepare shared stores data
     *
     * @param string $sharedStoresStr
     * @param string $submittedFromStoreId
     * @return array
     */
    private function prepareSharedStores($sharedStoresStr, $submittedFromStoreId)
    {
        $storesArr = explode(',', $sharedStoresStr);
        foreach ($storesArr as $key => $value) {
            if ($value == Store::DEFAULT_STORE_ID
                || $value == $submittedFromStoreId
            ) {
                unset($storesArr[$key]);
            }
        }

        return $storesArr;
    }

    /**
     * Prepare rating
     *
     * @param float $rating
     * @return int
     */
    private function prepareRating($rating)
    {
        $absoluteRoundedRating = round($rating / RatingValue::VALUE_DELTA);
        $preparedRating = $absoluteRoundedRating * RatingValue::VALUE_DELTA;

        return (int)$preparedRating;
    }

    /**
     * Create review object by data
     *
     * @param array $data
     * @return ReviewInterface
     */
    private function getReviewObject($data)
    {
        $reviewDataObject = $this->reviewDataFactory->create();

        $this->dataObjectHelper->populateWithArray(
            $reviewDataObject,
            $data,
            ReviewInterface::class
        );

        return $reviewDataObject;
    }

    /**
     * Save last imported id value
     *
     * @param $lastImportedId
     */
    private function saveLastImportedReviewId($lastImportedId)
    {
        $this->config->setLastImportedReviewId($lastImportedId);
    }

    /**
     * Get last imported review id
     *
     * @return int|null
     */
    private function getLastImportedReviewId()
    {
        return $this->config->getLastImportedReviewId();
    }
}

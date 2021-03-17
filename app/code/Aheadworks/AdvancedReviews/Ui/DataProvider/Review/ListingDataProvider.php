<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Review;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\CollectionFactory;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Collection;
use Magento\Framework\App\RequestInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;

/**
 * Class ListingDataProvider
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Review
 */
class ListingDataProvider extends AbstractDataProvider
{
    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->request = $request;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if ($productId = $this->request->getParam('current_product_id', 0)) {
            $this->getCollection()->addFieldToFilter(ReviewInterface::PRODUCT_ID, $productId);
        }
        if ($customerId = $this->request->getParam('current_customer_id', 0)) {
            $this->getCollection()->addFieldToFilter(ReviewInterface::CUSTOMER_ID, $customerId);
        }

        $this->getCollection()
            ->setNeedTruncateReviewContent(true)
            ->setNeedTruncateReviewSummary(true)
            ->setNeedToAttachAbuseReportData(true)
            ->load();

        return $this->getCollection()->toArray();
    }
}

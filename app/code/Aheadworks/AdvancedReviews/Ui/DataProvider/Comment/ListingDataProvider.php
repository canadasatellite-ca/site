<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Ui\DataProvider\Comment;

use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment\CollectionFactory;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment\Collection;
use Magento\Framework\App\RequestInterface;

/**
 * Class ListingDataProvider
 * @package Aheadworks\AdvancedReviews\Ui\DataProvider\Comment
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
     * {@inheritdoc}
     */
    public function getData()
    {
        $this->applyDefaultFilter();
        $this->getCollection()->setNeedToAttachAbuseReportData(true);

        return parent::getData();
    }

    /**
     * {@inheritdoc}
     */
    public function getAllIds()
    {
        $this->applyDefaultFilter();

        return parent::getAllIds();
    }

    /**
     * Apply default filter
     */
    private function applyDefaultFilter()
    {
        $reviewId = $this->request->getParam('current_review_id', 0);
        $this->getCollection()->addFieldToFilter(CommentInterface::REVIEW_ID, $reviewId);
    }
}

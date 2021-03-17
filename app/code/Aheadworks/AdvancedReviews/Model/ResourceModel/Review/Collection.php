<?php
/**
 * Copyright 2019 aheadWorks. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Aheadworks\AdvancedReviews\Model\ResourceModel\Review;

use Aheadworks\AdvancedReviews\Api\Data\AbuseReportInterface;
use Aheadworks\AdvancedReviews\Api\Data\ReviewAttachmentInterface;
use Aheadworks\AdvancedReviews\Model\ResourceModel\AbstractCollection;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review as ReviewResource;
use Aheadworks\AdvancedReviews\Model\ResourceModel\Review\Comment as CommentResource;
use Aheadworks\AdvancedReviews\Model\ResourceModel\AbuseReport as AbuseReportResource;
use Aheadworks\AdvancedReviews\Model\Review as ReviewModel;
use Aheadworks\AdvancedReviews\Api\Data\ReviewInterface;
use Aheadworks\AdvancedReviews\Api\Data\CommentInterface;
use Aheadworks\AdvancedReviews\Model\Source\Review\Comment\Status as CommentStatus;
use Aheadworks\AdvancedReviews\Model\Source\AbuseReport\Status as AbuseReportStatus;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Attribute;
use Magento\Framework\Api\SortOrder;
use Magento\Framework\DB\Select;
use Magento\Framework\Exception\LocalizedException;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Eav\Api\AttributeRepositoryInterface;
use Magento\Framework\Filter\Truncate;
use Zend_Db_Expr;

/**
 * Class Collection
 * @package Aheadworks\AdvancedReviews\Model\ResourceModel\Review
 */
class Collection extends AbstractCollection
{
    /**#@+
     * Constants for attach data to review collection
     */
    const PRODUCT_NAME_COLUMN_NAME = 'product_name';
    const PRODUCT_SKU_COLUMN_NAME = 'product_sku';
    const PRODUCT_ENTITY_ID = 'entity_id';
    const LINKAGE_COLUMN_NAME = 'review_id';
    const COMMENTS_COUNT = 'comments_count';
    const ATTACHMENTS_COUNT = 'attachments_count';
    const NEW_COMMENTS_COUNT = 'new_comments_count';
    const NEW_ABUSE_REPORTS_COUNT = 'new_abuse_reports_count';
    const REPORTED_ENTITY_ID = 'reported_entity_id';
    const AGGREGATED_CONTENT_COLUMN_NAME = 'aggregated_content';
    /**#@-*/

    /**
     * {@inheritdoc}
     */
    protected $_idFieldName = ReviewInterface::ID;

    /**
     * {@inheritdoc}
     */
    protected $_map = [
        'fields' => [
            ReviewInterface::ID => 'main_table.' . ReviewInterface::ID,
            ReviewInterface::STORE_ID => 'main_table.' . ReviewInterface::STORE_ID,
            ReviewInterface::CONTENT => 'main_table.' . ReviewInterface::CONTENT,
            ReviewInterface::CREATED_AT => 'main_table.' . ReviewInterface::CREATED_AT,
            ReviewInterface::STATUS => 'main_table.' . ReviewInterface::STATUS,
            ReviewInterface::NICKNAME => 'main_table.' . ReviewInterface::NICKNAME
        ]
    ];

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var string
     */
    protected $linkField;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var AttributeRepositoryInterface
     */
    protected $attributeRepository;

    /**
     * @var Truncate
     */
    protected $truncate;

    /**
     * @var Zend_Db_Expr
     */
    protected $aggregatedContentExpression;

    /**
     * @var bool
     */
    protected $needTruncateReviewContent = false;

    /**
     * @var bool
     */
    protected $needTruncateReviewSummary = false;

    /**
     * @var bool
     */
    protected $needToAttachProductData = false;

    /**
     * @var bool
     */
    protected $needToAttachAbuseReportData = false;

    /**
     * @var bool
     */
    protected $isAggregatedContentAdded = false;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param MetadataPool $metadataPool
     * @param AttributeRepositoryInterface $attributeRepository
     * @param StoreManagerInterface $storeManager
     * @param Truncate $truncate
     * @param Zend_Db_Expr $aggregatedContentExpression
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        MetadataPool $metadataPool,
        AttributeRepositoryInterface $attributeRepository,
        StoreManagerInterface $storeManager,
        Truncate $truncate,
        Zend_Db_Expr $aggregatedContentExpression,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->metadataPool = $metadataPool;
        $this->storeManager = $storeManager;
        $this->attributeRepository = $attributeRepository;
        $this->truncate = $truncate;
        $this->aggregatedContentExpression = $aggregatedContentExpression;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $connection,
            $resource
        );
    }

    protected function _construct()
    {
        $this->_init(ReviewModel::class, ReviewResource::class);
    }

    /**
     * {@inheritdoc}
     */
    protected function _afterLoad()
    {
        $this->attachAdditionalReviewsData();
        if ($this->needTruncateReviewContent) {
            $this->truncateReviewContentFields();
        }
        if ($this->needTruncateReviewSummary) {
            $this->truncateReviewSummary();
        }

        return parent::_afterLoad();
    }

    /**
     * Attach additional data for reviews
     *
     * @return $this
     */
    private function attachAdditionalReviewsData()
    {
        $this->attachAttachments();
        $this->attachSharedStores();
        $this->attachCommentsData();
        $this->attachOrderItemId();
        if ($this->needToAttachProductData) {
            $this->attachProductData();
        }
        if ($this->needToAttachAbuseReportData) {
            $this->attachAbuseReportData();
        }

        return $this;
    }

    /**
     * Attach file attachments
     */
    private function attachAttachments()
    {
        $this->attachRelationTable(
            ReviewResource::REVIEW_ATTACHMENT_TABLE_NAME,
            ReviewInterface::ID,
            self::LINKAGE_COLUMN_NAME,
            [ReviewAttachmentInterface::NAME, ReviewAttachmentInterface::FILE_NAME],
            ReviewInterface::ATTACHMENTS,
            [],
            [],
            true
        );
    }

    /**
     * Attach shared store ids to collection items
     */
    private function attachSharedStores()
    {
        $this->attachRelationTable(
            ReviewResource::SHARED_STORE_TABLE_NAME,
            ReviewInterface::ID,
            self::LINKAGE_COLUMN_NAME,
            ReviewInterface::STORE_ID,
            ReviewInterface::SHARED_STORE_IDS,
            [],
            [],
            true
        );
    }

    /**
     * Attach comments data to collection items
     */
    private function attachCommentsData()
    {
        $this->_attachComments();
        $this->_attachCommentsCountData();
    }

    /**
     * Attach comments to collection items
     */
    private function _attachComments()
    {
        $this->attachRelationTable(
            CommentResource::MAIN_TABLE_NAME,
            ReviewInterface::ID,
            self::LINKAGE_COLUMN_NAME,
            [
                CommentInterface::ID,
                CommentInterface::TYPE,
                CommentInterface::REVIEW_ID,
                CommentInterface::STATUS,
                CommentInterface::NICKNAME,
                CommentInterface::CONTENT,
                CommentInterface::CREATED_AT
            ],
            ReviewInterface::COMMENTS,
            [],
            [
                'field' => CommentInterface::CREATED_AT,
                'direction' => SortOrder::SORT_ASC
            ],
            true
        );
    }

    /**
     * Attach comments count data to collection items
     */
    private function _attachCommentsCountData()
    {
        $this->attachRelationTable(
            $this->_getCommentsCountSelect(),
            ReviewInterface::ID,
            self::LINKAGE_COLUMN_NAME,
            self::COMMENTS_COUNT,
            self::COMMENTS_COUNT,
            [],
            [],
            false,
            [self::COMMENTS_COUNT => 0]
        );

        $this->attachRelationTable(
            $this->_getNewCommentsCountSelect(),
            ReviewInterface::ID,
            self::LINKAGE_COLUMN_NAME,
            self::NEW_COMMENTS_COUNT,
            self::NEW_COMMENTS_COUNT,
            [],
            [],
            false,
            [self::NEW_COMMENTS_COUNT => 0]
        );
    }

    /**
     * Attach product data
     */
    private function attachProductData()
    {
        $this->attachRelationTable(
            'catalog_product_entity',
            ReviewInterface::PRODUCT_ID,
            self::PRODUCT_ENTITY_ID,
            ProductInterface::SKU,
            self::PRODUCT_SKU_COLUMN_NAME
        );
        $this->attachRelationTable(
            $this->getProductNameQuery(),
            ReviewInterface::PRODUCT_ID,
            self::PRODUCT_ENTITY_ID,
            self::PRODUCT_NAME_COLUMN_NAME,
            self::PRODUCT_NAME_COLUMN_NAME
        );
    }

    /**
     * Attach order item id
     */
    private function attachOrderItemId()
    {
        $this->attachRelationTable(
            ReviewResource::REMINDER_ORDER_ITEM_TABLE_NAME,
            ReviewInterface::ID,
            self::LINKAGE_COLUMN_NAME,
            ReviewInterface::ORDER_ITEM_ID,
            ReviewInterface::ORDER_ITEM_ID
        );
    }

    /**
     * Attach abuse report data
     */
    private function attachAbuseReportData()
    {
        $this->attachRelationTable(
            $this->_getNewAbuseReportsSelect(),
            ReviewInterface::ID,
            self::REPORTED_ENTITY_ID,
            self::NEW_ABUSE_REPORTS_COUNT,
            self::NEW_ABUSE_REPORTS_COUNT
        );
    }

    /**
     * Truncate review content fields for loaded collection items
     */
    private function truncateReviewContentFields()
    {
        /** @var ReviewModel $item */
        foreach ($this as $item) {
            $this->truncateReviewField($item, ReviewInterface::CONTENT);
            if ($this->isAggregatedContentAdded) {
                $this->truncateReviewField($item, self::AGGREGATED_CONTENT_COLUMN_NAME);
            }
        }
    }

    /**
     * Truncate review summary for loaded collection items
     */
    private function truncateReviewSummary()
    {
        /** @var ReviewModel $item */
        foreach ($this as $item) {
            $this->truncateReviewField($item, ReviewInterface::SUMMARY);
        }
    }

    /**
     * Truncate specific field of the review
     *
     * @param ReviewModel $review
     * @param string $fieldName
     * @return ReviewModel
     */
    private function truncateReviewField($review, $fieldName)
    {
        $truncatedField = $this->truncate->filter($review->getData($fieldName));
        $review->setData($fieldName, $truncatedField);
        return $review;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToSelect($field, $alias = null)
    {
        if (is_array($field)) {
            return parent::addFieldToSelect($field, $alias);
        }
        if ($this->isFieldRequiresProductDataAttached($field)) {
            $this->setNeedToAttachProductData(true);
        } elseif ($field == self::AGGREGATED_CONTENT_COLUMN_NAME) {
            $this->addAggregatedContentToSelect();
        } else {
            parent::addFieldToSelect($field, $alias);
        }
        return $this;
    }

    /**
     * Check if specified field is related to the product data
     *
     * @param string $fieldName
     * @return bool
     */
    private function isFieldRequiresProductDataAttached($fieldName)
    {
        return (in_array($fieldName, [self::PRODUCT_NAME_COLUMN_NAME, self::PRODUCT_SKU_COLUMN_NAME]));
    }

    /**
     * Add aggregated content field to select
     */
    private function addAggregatedContentToSelect()
    {
        $this->getSelect()->columns([self::AGGREGATED_CONTENT_COLUMN_NAME => $this->aggregatedContentExpression]);
        $this->isAggregatedContentAdded = true;
    }

    /**
     * {@inheritdoc}
     */
    public function addOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->joinFieldsForSortOrder($field);
        return parent::addOrder($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->joinFieldsForSortOrder($field);
        return parent::setOrder($field, $direction);
    }

    /**
     * {@inheritdoc}
     */
    public function unshiftOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        $this->joinFieldsForSortOrder($field);
        return parent::unshiftOrder($field, $direction);
    }

    /**
     * Join fields for sort order
     *
     * @param string $field
     * @return $this
     * @throws \Exception
     */
    private function joinFieldsForSortOrder($field)
    {
        if ($field == self::PRODUCT_SKU_COLUMN_NAME) {
            $this->joinProductSku();
        }
        if ($field == self::PRODUCT_NAME_COLUMN_NAME) {
            $this->joinProductName();
        }
        if ($field == ReviewInterface::ORDER_ITEM_ID) {
            $this->joinOrderItemId();
        }
        if ($field == self::COMMENTS_COUNT) {
            $this->joinCommentsCount();
        }
        if ($field == self::NEW_COMMENTS_COUNT) {
            $this->joinNewCommentsCount();
        }
        if ($field == self::NEW_ABUSE_REPORTS_COUNT) {
            $this->joinAbuseReportData();
        }
        if ($field == self::AGGREGATED_CONTENT_COLUMN_NAME) {
            $this->joinAggregatedContent();
        }
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function addFieldToFilter($field, $condition = null)
    {
        $fieldsToProcess = $this->processAddFieldToFilter($field, $condition);

        if (!empty($fieldsToProcess)) {
            return parent::addFieldToFilter($fieldsToProcess, $condition);
        }

        return $this;
    }

    /**
     * Process adding fields to filter
     *
     * @param string|array $field
     * @param null|string|array $condition
     * @return array|string
     */
    private function processAddFieldToFilter($field, $condition = null)
    {
        $fieldsToProcess = null;
        if (is_array($field)) {
            $fieldsToProcess = [];
            foreach ($field as $fieldName) {
                if ($this->isNeedToApplyPublicFilterToField($fieldName)) {
                    $this->addFilter($fieldName, $condition, 'public');
                } else {
                    $fieldsToProcess[] = $fieldName;
                }
            }
        } else {
            if ($this->isNeedToApplyPublicFilterToField($field)) {
                $this->addFilter($field, $condition, 'public');
            } else {
                $fieldsToProcess = $field;
            }
        }

        return $fieldsToProcess;
    }

    /**
     * Check if need to apply public filter instead of native logic
     *
     * @param string $fieldName
     * @return bool
     */
    private function isNeedToApplyPublicFilterToField($fieldName)
    {
        return (in_array(
            $fieldName,
            [
                self::PRODUCT_NAME_COLUMN_NAME,
                self::PRODUCT_SKU_COLUMN_NAME,
                ReviewInterface::ORDER_ITEM_ID,
                self::COMMENTS_COUNT,
                self::NEW_COMMENTS_COUNT,
                self::ATTACHMENTS_COUNT,
                self::AGGREGATED_CONTENT_COLUMN_NAME,
            ]
        ));
    }

    /**
     * {@inheritdoc}
     */
    protected function _renderFiltersBefore()
    {
        if ($this->getFilter(self::PRODUCT_SKU_COLUMN_NAME)) {
            $this->joinProductSku();
        }
        if ($this->getFilter(self::PRODUCT_NAME_COLUMN_NAME)) {
            $this->joinProductName();
        }
        if ($this->getFilter(ReviewInterface::ORDER_ITEM_ID)) {
            $this->joinOrderItemId();
        }
        if ($this->getFilter(self::COMMENTS_COUNT)) {
            $this->joinCommentsCount();
        }
        if ($this->getFilter(self::ATTACHMENTS_COUNT)) {
            $this->joinAttachmentsCount();
        }
        if ($this->getFilter(self::NEW_COMMENTS_COUNT)) {
            $this->joinNewCommentsCount();
        }
        if ($this->getFilter(self::NEW_ABUSE_REPORTS_COUNT)) {
            $this->joinAbuseReportData();
        }
        if ($this->getFilter(self::AGGREGATED_CONTENT_COLUMN_NAME)) {
            $this->joinAggregatedContent();
        }

        parent::_renderFiltersBefore();
    }

    /**
     * Join shared store ids to collection items
     */
    private function joinSharedStores()
    {
        $this->joinLinkageTable(
            ReviewResource::SHARED_STORE_TABLE_NAME,
            ReviewInterface::ID,
            self::LINKAGE_COLUMN_NAME,
            ReviewInterface::STORE_ID,
            ReviewInterface::SHARED_STORE_IDS
        );
        $this->getSelect()->group(ReviewInterface::ID);
    }

    /**
     * Join product sku
     */
    private function joinProductSku()
    {
        $this->joinLinkageTable(
            'catalog_product_entity',
            ReviewInterface::PRODUCT_ID,
            self::PRODUCT_ENTITY_ID,
            self::PRODUCT_SKU_COLUMN_NAME,
            ProductInterface::SKU
        );
    }

    /**
     * Join product name
     *
     * @throws \Exception
     */
    private function joinProductName()
    {
        $this->joinLinkageTable(
            $this->getProductNameQuery(),
            ReviewInterface::PRODUCT_ID,
            self::PRODUCT_ENTITY_ID,
            self::PRODUCT_NAME_COLUMN_NAME,
            self::PRODUCT_NAME_COLUMN_NAME
        );
    }

    /**
     * Join comments count
     */
    private function joinCommentsCount()
    {
        $this->joinLinkageTable(
            $this->_getCommentsCountSelect(),
            ReviewInterface::ID,
            self::LINKAGE_COLUMN_NAME,
            self::COMMENTS_COUNT,
            self::COMMENTS_COUNT
        );
    }

    /**
     * Join new comments count
     */
    private function joinAttachmentsCount()
    {
        $this->joinLinkageTable(
            $this->getAttachmentsCountSelect(),
            ReviewInterface::ID,
            self::LINKAGE_COLUMN_NAME,
            self::ATTACHMENTS_COUNT,
            self::ATTACHMENTS_COUNT
        );
    }

    /**
     * Join new comments count
     */
    private function joinNewCommentsCount()
    {
        $this->joinLinkageTable(
            $this->_getNewCommentsCountSelect(),
            ReviewInterface::ID,
            self::LINKAGE_COLUMN_NAME,
            self::NEW_COMMENTS_COUNT,
            self::NEW_COMMENTS_COUNT
        );
    }

    /**
     * Join order item id
     */
    private function joinOrderItemId()
    {
        $this->joinLinkageTable(
            ReviewResource::REMINDER_ORDER_ITEM_TABLE_NAME,
            ReviewInterface::ID,
            self::LINKAGE_COLUMN_NAME,
            ReviewInterface::ORDER_ITEM_ID,
            ReviewInterface::ORDER_ITEM_ID
        );
    }

    /**
     * Join abuse reports data
     */
    private function joinAbuseReportData()
    {
        $this->joinLinkageTable(
            $this->_getNewAbuseReportsSelect(),
            ReviewInterface::ID,
            self::REPORTED_ENTITY_ID,
            self::NEW_ABUSE_REPORTS_COUNT,
            self::NEW_ABUSE_REPORTS_COUNT
        );
    }

    /**
     * Join aggregated content column
     */
    private function joinAggregatedContent()
    {
        $this->addFilterToMap(self::AGGREGATED_CONTENT_COLUMN_NAME, $this->aggregatedContentExpression);
    }

    /**
     * Get product name query
     *
     * @return \Magento\Framework\DB\Select
     * @throws \Exception
     */
    private function getProductNameQuery()
    {
        $attributeId = $this->getAttributeIdByCode(Product::NAME);
        $catalogLinkField = $this->getLinkField();
        $select = $this->getConnection()->select()
            ->from(
                ['tmp_table' => $this->getTable('catalog_product_entity')],
                [
                    'tmp_table.' . self::PRODUCT_ENTITY_ID,
                    'IF(at_name.value_id > 0, at_name.value, at_name_default.value) AS '
                    . self::PRODUCT_NAME_COLUMN_NAME
                ]
            )
            ->join(
                ['at_name_default' => $this->getTable('catalog_product_entity_varchar')],
                'at_name_default.' . $catalogLinkField . ' = tmp_table.' . $catalogLinkField . ''
                . ' AND at_name_default.attribute_id = ' . $attributeId
                . ' AND at_name_default.store_id = 0',
                []
            )->joinLeft(
                ['at_name' => $this->getTable('catalog_product_entity_varchar')],
                'at_name.' . $catalogLinkField . ' = tmp_table.' . $catalogLinkField . ''
                . ' AND at_name.attribute_id = ' . $attributeId
                . ' AND at_name.store_id = ' . $this->storeManager->getDefaultStoreView()->getId(),
                []
            );

        return $select;
    }

    /**
     * Retrieve comments count select
     *
     * @return Select
     */
    private function _getCommentsCountSelect()
    {
        $commentsCountExpr = new \Zend_Db_Expr('COALESCE(COUNT(id), 0)');
        $select = $this->getConnection()->select()
            ->from(
                ['tmp_table' => $this->getTable(CommentResource::MAIN_TABLE_NAME)],
                [
                    CommentInterface::REVIEW_ID => CommentInterface::REVIEW_ID,
                    self::COMMENTS_COUNT => $commentsCountExpr
                ]
            )->group(CommentInterface::REVIEW_ID);

        return $select;
    }

    /**
     * Retrieve attachments count select
     *
     * @return Select
     */
    private function getAttachmentsCountSelect()
    {
        $attachmentsCountExpr = new \Zend_Db_Expr('COALESCE(COUNT(' . ReviewAttachmentInterface::REVIEW_ID . '), 0)');
        $select = $this->getConnection()->select()
            ->from(
                ['tmp_table' => $this->getTable(ReviewResource::REVIEW_ATTACHMENT_TABLE_NAME)],
                [
                    ReviewAttachmentInterface::REVIEW_ID => ReviewAttachmentInterface::REVIEW_ID,
                    self::ATTACHMENTS_COUNT => $attachmentsCountExpr
                ]
            )->group(CommentInterface::REVIEW_ID);

        return $select;
    }

    /**
     * Retrieve new comments count select
     *
     * @return Select
     */
    private function _getNewCommentsCountSelect()
    {
        $newCommentsCountExpr = new \Zend_Db_Expr('COALESCE(COUNT(id), 0)');
        $select = $this->getConnection()->select()
            ->from(
                ['tmp_table' => $this->getTable(CommentResource::MAIN_TABLE_NAME)],
                [
                    CommentInterface::REVIEW_ID => CommentInterface::REVIEW_ID,
                    self::NEW_COMMENTS_COUNT => $newCommentsCountExpr
                ]
            )->where(
                CommentInterface::STATUS . ' = ?',
                CommentStatus::getDefaultStatus()
            )->group(CommentInterface::REVIEW_ID);

        return $select;
    }

    /**
     * Retrieve new abuse reports select
     *
     * @return Select
     */
    private function _getNewAbuseReportsSelect()
    {
        $newAbuseReportsCountExpr = new \Zend_Db_Expr('COALESCE(COUNT(*), 0)');
        $reviewIdExpr = new \Zend_Db_Expr('COALESCE(comment_table.review_id, main_table.entity_id)');
        $select = $this->getConnection()->select()
            ->from(
                ['main_table' => $this->getTable(AbuseReportResource::MAIN_TABLE_NAME)],
                [
                    self::REPORTED_ENTITY_ID => $reviewIdExpr,
                    self::NEW_ABUSE_REPORTS_COUNT => $newAbuseReportsCountExpr
                ]
            )->joinLeft(
                ['comment_table' => $this->getTable(CommentResource::MAIN_TABLE_NAME)],
                'main_table.entity_id = comment_table.id',
                []
            )->where('main_table.' . AbuseReportInterface::STATUS . ' = ?', AbuseReportStatus::getDefaultStatus())
            ->group(self::REPORTED_ENTITY_ID);

        return $this->getConnection()->select()->from(['tmp_table' => $select]);
    }

    /**
     * Get attribute id
     *
     * @param string $attributeCode
     * @return bool|int
     */
    private function getAttributeIdByCode($attributeCode)
    {
        $attributeId = false;
        try {
            /** @var Attribute $attribute */
            $attribute = $this->attributeRepository->get(Product::ENTITY, $attributeCode);
            if ($attribute) {
                $attributeId = $attribute->getId();
            }
        } catch (LocalizedException $e) {
        }
        return $attributeId;
    }

    /**
     * Retrieve link field and cache it
     *
     * @return bool|string
     * @throws \Exception
     */
    private function getLinkField()
    {
        if ($this->linkField === null) {
            $this->linkField = $this->metadataPool->getMetadata(ProductInterface::class)->getLinkField();
        }
        return $this->linkField;
    }

    /**
     * Set boolean flag for truncate review content
     *
     * @param bool $value
     * @return $this
     */
    public function setNeedTruncateReviewContent($value)
    {
        $this->needTruncateReviewContent = $value;

        return $this;
    }

    /**
     * Set boolean flag for truncate review summary
     *
     * @param bool $value
     * @return $this
     */
    public function setNeedTruncateReviewSummary($value)
    {
        $this->needTruncateReviewSummary = $value;

        return $this;
    }

    /**
     * Set boolean flag for adding corresponding product data
     *
     * @param bool $value
     * @return $this
     */
    public function setNeedToAttachProductData($value)
    {
        $this->needToAttachProductData = $value;

        return $this;
    }

    /**
     * Set boolean flag for adding corresponding abuse report data
     *
     * @param bool $value
     * @return $this
     */
    public function setNeedToAttachAbuseReportData($value)
    {
        $this->needToAttachAbuseReportData = $value;

        return $this;
    }

    /**
     * Add review store filter
     *
     * @param int $storeId
     * @return $this
     */
    public function addStoreFilter($storeId)
    {
        if ($storeId) {
            $this->joinSharedStores();
            $fields = [
                'main_table.' . ReviewInterface::STORE_ID,
                ReviewResource::SHARED_STORE_TABLE_NAME . '.' . ReviewInterface::STORE_ID
            ];
            $conditions = [
                $storeId,
                $storeId
            ];

            $this->addFieldToFilter($fields, $conditions);
        }

        return $this;
    }

    /**
     * Retrieve image count by pages
     *
     * @return array
     */
    public function getImageCountByPages()
    {
        $baseSelect = clone $this->getSelect();
        $limit = $baseSelect->getPart(Select::LIMIT_COUNT);
        $baseSelect
            ->reset(Select::LIMIT_COUNT)
            ->reset(Select::LIMIT_OFFSET);
        $baseSelect->joinLeft(
            ['attach' => $this->getTable(ReviewResource::REVIEW_ATTACHMENT_TABLE_NAME)],
            'main_table.id = attach.' . ReviewAttachmentInterface::REVIEW_ID,
            ['count' => new \Zend_Db_Expr('count(attach.' . ReviewAttachmentInterface::REVIEW_ID . ')')]
        );

        $twrnSelect = $this->getConnection()->select()
            ->from(
                new \Zend_Db_Expr(sprintf('(select @rownum := 0) r, (%s)', $baseSelect)),
                [
                    'row_number' => new \Zend_Db_Expr('@rownum := @rownum + 1'),
                    'count'
                ]
            );

        $numExpr = empty($limit)
            ? 'floor((row_number-0.1)) + 1'
            : sprintf('floor((row_number-0.1) / %s) + 1', $limit);
        $main = $this->getConnection()->select()
            ->from(
                ['main' => new \Zend_Db_Expr(sprintf('(%s)', $twrnSelect))],
                [
                    'num' => new \Zend_Db_Expr($numExpr),
                    'count' => new \Zend_Db_Expr('SUM(count)'),
                ]
            )->group('num');

        $imageCountByPages = $this->getConnection()->fetchAssoc($main);

        return $imageCountByPages ?: [];
    }
}

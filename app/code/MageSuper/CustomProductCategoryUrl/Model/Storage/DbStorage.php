<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace MageSuper\CustomProductCategoryUrl\Model\Storage;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Select;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory;
use Magento\Framework\Api\DataObjectHelper;

class DbStorage extends \Magento\UrlRewrite\Model\Storage\AbstractStorage
{
    /**
     * DB Storage table name
     */
    const TABLE_NAME = 'url_rewrite';

    /**
     * Code of "Integrity constraint violation: 1062 Duplicate entry" error
     */
    const ERROR_CODE_DUPLICATE_ENTRY = 23000;

    const CATEGORY_URL_SUFFIX = 'catalog/seo/category_url_suffix';

    const PRODUCT_URL_SUFFIX = 'catalog/seo/product_url_suffix';

    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var Resource
     */
    protected $resource;

    /**
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewriteFactory $urlRewriteFactory
     * @param DataObjectHelper $dataObjectHelper
     * @param \Magento\Framework\App\ResourceConnection $resource
     */
    public function __construct(
        UrlRewriteFactory $urlRewriteFactory,
        DataObjectHelper $dataObjectHelper,
        ResourceConnection $resource,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        $this->scopeConfig = $scopeConfig;

        parent::__construct($urlRewriteFactory, $dataObjectHelper);
    }

    /**
     * Prepare select statement for specific filter
     *
     * @param array $data
     * @return \Magento\Framework\DB\Select
     */
    protected function prepareSelect($data)
    {
        $select = $this->connection->select();
        $select->from($this->resource->getTableName(self::TABLE_NAME));

        foreach ($data as $column => $value) {
            $select->where($this->connection->quoteIdentifier($column) . ' IN (?)', $value);
        }
        return $select;
    }

    /**
     * {@inheritdoc}
     */
    protected function doFindAllByData(array $data)
    {
        return $this->connection->fetchAll($this->prepareSelect($data));
    }

    /**
     * {@inheritdoc}
     */
    protected function doFindOneByData(array $data)
    {
        return $this->connection->fetchRow($this->prepareSelect($data));
    }

    /**
     * {@inheritdoc}
     */
    protected function doReplace(array $urls)
    {
        foreach ($this->createFilterDataBasedOnUrls($urls) as $type => $urlData) {
            $urlData[UrlRewrite::ENTITY_TYPE] = $type;
            $this->deleteByData($urlData);
        }

        $data = [];
        foreach ($urls as $url) {
            if ($url->getRequestPath() == $this->getSuffix($url->getEntityType())) {
                continue;
            }
            $data[] = $url->toArray();
        }

        /* FIXME: Get rid of rewrite for root Magento category to unduplicate things
          * @see: https://github.com/magento/magento2/issues/6671 */
        foreach ($data as $key => $info) {
            if (isset($info['target_path']) && stristr($info['target_path'], '/category') && $info['entity_type'] == 'product') {
                $this->notify();
                unset($data[$key]);
            }
        }

        $this->insertMultiple($data);

        return $urls;
    }

    protected function notify()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $cache = $om->get('Magento\Framework\App\CacheInterface');
        $tmp = $cache->load('int_trigger_url_regenerate_incorrect');
        if (!$tmp) {
            $cache->save('1', 'int_trigger_url_regenerate_incorrect');
            $e = new \Exception();
            $str = $e->getTraceAsString();
        }
    }

    /**
     * Insert multiple
     *
     * @param array $data
     * @return void
     * @throws \Magento\Framework\Exception\AlreadyExistsException
     * @throws \Exception
     */
    protected function insertMultiple(array $data)
    {
        try {
            $this->connection->insertMultiple($this->resource->getTableName(self::TABLE_NAME), $data);
        } catch (\Exception $e) {
            if ($e->getCode() === self::ERROR_CODE_DUPLICATE_ENTRY
                && preg_match('#SQLSTATE\[23000\]: [^:]+: 1062[^\d]#', $e->getMessage())
            ) {
                throw new \Magento\Framework\Exception\AlreadyExistsException(
                    __('URL key for specified store already exists.')
                );
            }
            throw $e;
        }
    }

    /**
     * Get filter for url rows deletion due to provided urls
     *
     * @param UrlRewrite[] $urls
     * @return array
     */
    protected function createFilterDataBasedOnUrls($urls)
    {
        $data = [];
        foreach ($urls as $url) {
            $entityType = $url->getEntityType();
            foreach ([UrlRewrite::ENTITY_ID, UrlRewrite::STORE_ID] as $key) {
                $fieldValue = $url->getByKey($key);
                if (!isset($data[$entityType][$key]) || !in_array($fieldValue, $data[$entityType][$key])) {
                    $data[$entityType][$key][] = $fieldValue;
                }
            }
        }
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteByData(array $data)
    {
        $this->connection->query(
            $this->prepareSelect($data)->deleteFromSelect($this->resource->getTableName(self::TABLE_NAME))
        );
    }

    protected function getSuffix($entityType)
    {
        if ($entityType == 'category') {
            return $this->scopeConfig->getValue(self::CATEGORY_URL_SUFFIX);
        }

        if ($entityType == 'product') {
            return $this->scopeConfig->getValue(self::PRODUCT_URL_SUFFIX);
        }
    }
}

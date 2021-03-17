<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\DataProvider;

class AttributesRequest
{
    /**
     * @var array|null
     */
    private $attributeFields;

    /**
     * @var array|null
     */
    private $edgeFields;

    /**
     * @var array|null
     */
    private $pageInfoFields;

    /**
     * @var bool
     */
    private $isCalculateTotalCount = false;

    /**
     * @var int|null
     */
    private $limit;

    /**
     * @var string|null
     */
    private $cursor;

    /**
     * @var array
     */
    private $filters = [];

    public function __construct(array $fields = [])
    {
        $this->setFields($fields);
    }

    public function setFields(array $fields)
    {
        $this->pageInfoFields = $fields['pageInfo'] ?? null;
        $this->edgeFields = $fields['edges'] ?? null;
        $this->attributeFields = $fields['edges']['node'] ?? null;
        $this->isCalculateTotalCount = isset($fields['totalCount']) || isset($fields['pageInfo']['hasNextPage']);
    }

    /**
     * @return array
     */
    public function getAttributeFields(): array
    {
        return $this->attributeFields;
    }

    /**
     * @return array|null
     */
    public function getEdgeFields(): ?array
    {
        return $this->edgeFields;
    }

    /**
     * @return array|null
     */
    public function getPageInfoFields(): ?array
    {
        return $this->pageInfoFields;
    }

    /**
     * @return bool
     */
    public function isCalculateTotalCount(): bool
    {
        return $this->isCalculateTotalCount;
    }

    /**
     * @param bool $isCalculateTotalCount
     */
    public function setIsCalculateTotalCount(bool $isCalculateTotalCount): void
    {
        $this->isCalculateTotalCount = $isCalculateTotalCount;
    }

    /**
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * @param int|null $limit
     */
    public function setLimit(?int $limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @return string|null
     */
    public function getCursor(): ?string
    {
        return $this->cursor;
    }

    /**
     * @param string|null $cursor
     */
    public function setCursor(?string $cursor): void
    {
        $this->cursor = $cursor;
    }

    /**
     * @return array
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    /**
     * @param array $filters
     */
    public function setFilters(array $filters): void
    {
        $this->filters = $filters;
    }
}

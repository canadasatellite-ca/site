<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Container;

class StoreOrdersContainer
{
    private $idsContainer;
    private $orderFieldsContainer;
    private $edgeFieldsContainer;
    private $pageFieldsContainer;
    private $cursorsContainer;
    private $sortsContainer;
    private $isCalculateTotalCountsContainer;
    /**
     * @var KeyValue
     */
    private $limitsContainer;

    public function limits(): KeyValue
    {
        if (null === $this->limitsContainer) {
            $this->limitsContainer = new KeyValue();
        }

        return $this->limitsContainer;
    }

    public function ids(): IdsContainer
    {
        if (null === $this->idsContainer) {
            $this->idsContainer = new IdsContainer();
        }

        return $this->idsContainer;
    }

    public function orderFields(): FieldsContainer
    {
        if (null === $this->orderFieldsContainer) {
            $this->orderFieldsContainer = new FieldsContainer();
        }

        return $this->orderFieldsContainer;
    }

    public function edgeFields(): FieldsContainer
    {
        if (null === $this->edgeFieldsContainer) {
            $this->edgeFieldsContainer = new FieldsContainer();
        }

        return $this->edgeFieldsContainer;
    }

    public function pageFields(): FieldsContainer
    {
        if (null === $this->pageFieldsContainer) {
            $this->pageFieldsContainer = new FieldsContainer();
        }

        return $this->pageFieldsContainer;
    }

    public function cursors(): KeyValue
    {
        if (null === $this->cursorsContainer) {
            $this->cursorsContainer = new KeyValue();
        }

        return $this->cursorsContainer;
    }

    public function sorts(): KeyValue
    {
        if (null === $this->sortsContainer) {
            $this->sortsContainer = new KeyValue();
        }

        return $this->sortsContainer;
    }

    public function isCalculateTotalCounts(): KeyValue
    {
        if (null === $this->isCalculateTotalCountsContainer) {
            $this->isCalculateTotalCountsContainer = new KeyValue();
        }

        return $this->isCalculateTotalCountsContainer;
    }

    public function add($id, int $limit, $cursor, $sort, $hashKey, \GraphQL\Type\Definition\ResolveInfo $info): void
    {
        $fields = $info->getFieldSelection(4);
        $this->ids()->add($id);
        $this->orderFields()->addAll($fields['edges']['node'] ?? []);
        $this->edgeFields()->addAll($fields['edges'] ?? []);
        $this->pageFields()->addAll($fields['pageInfo'] ?? []);
        $this->limits()->add($hashKey, max((int)$this->limits()->get($hashKey), $limit));
        $this->cursors()->get($hashKey) ? $this->cursors()->add($hashKey, min($this->cursors()->get($hashKey), $cursor)) : $this->cursors()->add($hashKey, $cursor);
        $this->isCalculateTotalCounts()->add($hashKey, (isset($fields['totalCount']) || isset($fields['pageInfo']['hasNextPage'])));
        $this->sorts()->add($hashKey, $sort);
    }
}

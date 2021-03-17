<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl;

class FilterToCollectionFilterConverter
{
    public function convert(array $filters, array $fieldsMap): array
    {
        $result = [];
        foreach ($filters as $filter) {
            $field = $filter['field'];
            $field = $fieldsMap[$field] ?? $field;
            $operator = $filter['operator'];
            unset($filter['field']);
            unset($filter['operator']);
            foreach ($filter as $condition => $value) {
                $mappedFilter = $this->mapFilter($condition, $value);
                if ($mappedFilter) {
                    if ($operator === 'AND') {
                        $result[] = [$field => $mappedFilter];
                    } else {
                        $result[$field][] = $mappedFilter;
                    }
                }
            }
        }
        return $result;
    }

    private function mapFilter(string $condition, $value): ?array
    {
        switch ($condition) {
            case 'eq':
            case 'neq':
            case 'gt':
            case 'gteq':
            case 'lt':
            case 'lteq':
            case 'in':
            case 'nin':
                return [$condition => $value];
            case 'beginsWith':
                return ['like' => "$value%"];
            case 'endsWith':
                return ['like' => "%$value"];
            case 'like':
                return ['like' => (string)$value];
            case 'beforeDate':
                return ['lt' => (new \DateTimeImmutable($value))->format('Y-m-d H:i:s')];
            case 'afterDate':
                return ['gt' => (new \DateTimeImmutable($value))->format('Y-m-d H:i:s')];
            case 'isnull':
                return $value
                    ? ['null' => true]
                    : ['notnull' => true];
            default:
                return null;
        }
    }
}

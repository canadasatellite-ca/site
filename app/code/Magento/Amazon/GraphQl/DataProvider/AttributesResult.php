<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\DataProvider;

class AttributesResult
{
    /**
     * @var array
     */
    private $attributesData;
    /**
     * @var int|null
     */
    private $totalCount;

    /**
     * @var string|null
     */
    private $lastCursor;
    /**
     * @var bool
     */
    private $hasNextPage;

    public function __construct(array $attributesData, ?int $totalCount, bool $hasNextPage)
    {
        $this->attributesData = $attributesData;
        $this->totalCount = $totalCount;
        $this->hasNextPage = $hasNextPage;
    }

    /**
     * @return array
     */
    public function getAttributesData(): array
    {
        return $this->attributesData;
    }

    /**
     * @return int|null
     */
    public function getTotalCount(): ?int
    {
        return $this->totalCount;
    }

    public function getEndCursor(): ?string
    {
        if (!empty($this->attributesData)) {
            $lastAttribute = end($this->attributesData);
            if ($lastAttribute && isset($lastAttribute['cursor'])) {
                $this->lastCursor = $lastAttribute['cursor'];
            }
        }
        return $this->lastCursor;
    }

    /**
     * @return bool
     */
    public function isHasNextPage(): bool
    {
        return $this->hasNextPage;
    }
}

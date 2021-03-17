<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Service\Account;

class ReimportAttributeValues
{
    /**
     * @var \Magento\Amazon\Model\ResourceModel\Amazon\Attribute\Value
     */
    private $resourceModel;

    public function __construct(\Magento\Amazon\Model\ResourceModel\Amazon\Attribute\Value $resourceModel)
    {
        $this->resourceModel = $resourceModel;
    }

    public function reimport(array $attributeIds): void
    {
        if (!$attributeIds) {
            return;
        }
        $this->resourceModel->clearAttributeValuesByAttributeIds($attributeIds);
    }
}

<?php

declare(strict_types=1);

namespace Magento\Amazon\Comm\Amazon\UpdateHandler;

use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Attribute\Value as AttributeValueResourceModel;

class AttributeValue implements HandlerInterface
{
    /**
     * @var AttributeValueResourceModel
     */
    private $attributeValueResource;
    /**
     * @var ChunkedHandler
     */
    private $chunkedHandler;

    public function __construct(AttributeValueResourceModel $attributeValueResource, ChunkedHandler $chunkedHandler)
    {
        $this->attributeValueResource = $attributeValueResource;
        $this->chunkedHandler = $chunkedHandler;
    }

    public function handle(array $updates, AccountInterface $account): array
    {
        $merchantId = (int)$account->getMerchantId();
        return $this->chunkedHandler->handleUpdatesWithChunks(
            function (array $chunk) use ($merchantId): void {
                $this->attributeValueResource->insert($chunk, $merchantId);
            },
            $updates,
            $account,
            'Cannot process logs with attributes values. Please report an error.'
        );
    }
}

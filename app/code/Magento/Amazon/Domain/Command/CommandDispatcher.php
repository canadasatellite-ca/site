<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\Domain\Command;

use Magento\Amazon\Model\ResourceModel\Amazon\Action as ActionResourceModel;
use Magento\Framework\Serialize\SerializerInterface;

/**
 * Class CommandDispatcher
 */
class CommandDispatcher
{
    /**
     * @var ActionResourceModel
     */
    private $actionResourceModel;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @param ActionResourceModel $actionResourceModel
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ActionResourceModel $actionResourceModel,
        SerializerInterface $serializer
    ) {
        $this->actionResourceModel = $actionResourceModel;
        $this->serializer = $serializer;
    }

    /**
     * @param int $merchantId
     * @param \Magento\Amazon\Domain\Command\AmazonCommandInterface $command
     * @return void
     */
    public function dispatch(int $merchantId, \Magento\Amazon\Domain\Command\AmazonCommandInterface $command)
    {
        $commandData['merchant_id'] = $merchantId;
        $commandData['command'] = $command->getName();
        $commandData['command_body'] = $this->serializer->serialize($command->getBody());
        $commandData['identifier'] = $command->getIdentifier();

        $this->actionResourceModel->insert($commandData);
    }
}

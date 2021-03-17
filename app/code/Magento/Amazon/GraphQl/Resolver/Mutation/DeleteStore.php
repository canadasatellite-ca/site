<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Mutation;

use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\Model\ApiClient\ApiException;
use Magento\Amazon\Model\ApiClient\ResponseValidationException;
use Magento\Amazon\Service\Account\DeleteAccount;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class DeleteStore implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var DeleteAccount
     */
    private $deleteAccount;

    public function __construct(DeleteAccount $deleteAccount)
    {
        $this->deleteAccount = $deleteAccount;
    }

    /**
     * @param $parent
     * @param array $args
     * @param Context $context
     * @param ResolveInfo $info
     * @return bool
     * @throws ApiException
     * @throws ResponseValidationException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        $uuid = $args['uuid'];
        $this->deleteAccount->deleteAccount($uuid);
        return true;
    }
}

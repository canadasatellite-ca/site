<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Mutation;

use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\Api\Data\AccountInterface;
use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\Model\ApiClient;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

class VerifyAuthentication implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var \Magento\Amazon\GraphQl\DataProvider\Stores
     */
    private $stores;
    /**
     * @var \Magento\Amazon\Service\Account\VerifyAuthentication
     */
    private $verifyAuthentication;

    public function __construct(
        \Magento\Amazon\GraphQl\DataProvider\Stores $stores,
        \Magento\Amazon\Service\Account\VerifyAuthentication $verifyAuthentication
    ) {
        $this->stores = $stores;
        $this->verifyAuthentication = $verifyAuthentication;
    }

    /**
     * @param  $parent
     * @param array $args
     * @param Context $context
     * @param ResolveInfo $info
     * @return AccountInterface|null
     * @throws ApiClient\ApiException
     * @throws ApiClient\ResponseFormatValidationException
     * @throws ApiClient\ResponseValidationException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function resolve($parent, array $args, Context $context, ResolveInfo $info)
    {
        $uuid = $args['uuid'];
        $this->verifyAuthentication->verifyByUuid($uuid);
        $context->stores()->addSingle($uuid, $info);
        return $this->stores->getSingleStore($uuid, $context);
    }
}

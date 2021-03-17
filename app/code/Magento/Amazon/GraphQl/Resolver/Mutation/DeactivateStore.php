<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Mutation;

use Magento\Amazon\GraphQl\ValidationException;
use Magento\Framework\Exception\LocalizedException;

class DeactivateStore implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var \Magento\Amazon\Service\Account\ChangeAccountStatus
     */
    private $changeAccountStatus;
    /**
     * @var \Magento\Amazon\GraphQl\DataProvider\Stores
     */
    private $stores;

    public function __construct(
        \Magento\Amazon\Service\Account\ChangeAccountStatus $changeAccountStatus,
        \Magento\Amazon\GraphQl\DataProvider\Stores $stores
    ) {
        $this->changeAccountStatus = $changeAccountStatus;
        $this->stores = $stores;
    }

    /**
     * @param $parent
     * @param array $args
     * @param \Magento\Amazon\GraphQl\Context $context
     * @param \GraphQL\Type\Definition\ResolveInfo $info
     * @return \GraphQL\Deferred
     * @throws ValidationException
     */
    public function resolve(
        $parent,
        array $args,
        \Magento\Amazon\GraphQl\Context $context,
        \GraphQL\Type\Definition\ResolveInfo $info
    ) {
        $uuid = $args['uuid'];
        try {
            $this->changeAccountStatus->deactivateByUuid($uuid);
        } catch (LocalizedException $exception) {
            throw new ValidationException($exception->getMessage());
        }
        $context->stores()->addSingle($uuid, $info);
        return new \GraphQL\Deferred(function () use ($uuid, $context) {
            return $this->stores->getSingleStore($uuid, $context);
        });
    }
}

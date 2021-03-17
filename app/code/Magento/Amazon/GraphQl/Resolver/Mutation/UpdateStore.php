<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

declare(strict_types=1);

namespace Magento\Amazon\GraphQl\Resolver\Mutation;

use Assert\AssertionFailedException;
use GraphQL\Type\Definition\ResolveInfo;
use Magento\Amazon\GraphQl\Context;
use Magento\Amazon\GraphQl\ValidationException;
use Magento\Amazon\Model\ApiClient\ApiException;
use Magento\Amazon\Service\Account\UpdateAccount;
use Magento\Amazon\Service\Account\UpdateAccountData;

class UpdateStore implements \Magento\Amazon\GraphQl\Resolver\ResolverInterface
{
    /**
     * @var UpdateAccount
     */
    private $updateAccount;
    /**
     * @var \Magento\Amazon\GraphQl\DataProvider\Stores
     */
    private $stores;

    public function __construct(
        UpdateAccount $updateAccount,
        \Magento\Amazon\GraphQl\DataProvider\Stores $stores
    ) {
        $this->updateAccount = $updateAccount;
        $this->stores = $stores;
    }

    public function resolve(
        $parent,
        array $args,
        Context $context,
        ResolveInfo $info
    ) {
        $uuid = $args['uuid'];
        $store = $args['store'];

        try {
            $data = new UpdateAccountData(
                $uuid,
                $store['name'] ?? null,
                $store['email'] ?? null
            );
            $this->updateAccount->updateAccount($data);
            $context->stores()->addSingle($uuid, $info);
            return $this->stores->getSingleStore($uuid, $context);
        } catch (AssertionFailedException $e) {
            throw new ValidationException($e->getMessage(), $e);
        } catch (ApiException $e) {
            throw new ValidationException($e->getMessage(), $e);
        }
    }
}

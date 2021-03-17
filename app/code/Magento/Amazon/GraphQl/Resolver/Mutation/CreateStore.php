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
use Magento\Amazon\GraphQl\DataProvider\Stores;
use Magento\Amazon\GraphQl\Resolver\ResolverInterface;
use Magento\Amazon\GraphQl\ValidationException;
use Magento\Amazon\Model\ApiClient\ApiException;
use Magento\Amazon\Model\MagentoAttributes;
use Magento\Amazon\Service\Account\CreateAccount;
use Magento\Amazon\Service\Account\CreateAccountData;
use Magento\Amazon\Service\Account\SearchMappingData;

class CreateStore implements ResolverInterface
{
    /**
     * @var CreateAccount
     */
    private $createAccount;
    /**
     * @var Stores
     */
    private $stores;
    /**
     * @var MagentoAttributes
     */
    private $magentoAttributes;

    /**
     * @param CreateAccount $createAccount
     * @param Stores $stores
     * @param MagentoAttributes $magentoAttributes
     */
    public function __construct(
        CreateAccount $createAccount,
        Stores $stores,
        MagentoAttributes $magentoAttributes
    ) {
        $this->createAccount = $createAccount;
        $this->stores = $stores;
        $this->magentoAttributes = $magentoAttributes;
    }

    /**
     * @param $parent
     * @param array $args
     * @param Context $context
     * @param ResolveInfo $info
     * @return \Magento\Amazon\Api\Data\AccountInterface|null
     * @throws ValidationException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function resolve($parent, array $args, Context $context, ResolveInfo $info)
    {
        $store = $args['store'];
        $mappingData = $args['searchMapping'];

        try {
            $data = new CreateAccountData(
                $store['name'],
                $store['email'],
                $store['countryCode'],
                $store['websiteCode']
            );
            $magentoAttributeCodes = $this->getMagentoAttributeCodes();
            $searchMapping = new SearchMappingData(
                $mappingData['amazonIdType'],
                $mappingData['magentoAttributeCode'],
                $magentoAttributeCodes
            );

            $createdAccount = $this->createAccount->createAccount($data, $searchMapping);

            $uuid = $createdAccount->getUuid();
            $context->stores()->addSingle($uuid, $info);
            return $this->stores->getSingleStore($uuid, $context);
        } catch (AssertionFailedException $e) {
            throw new ValidationException($e->getMessage(), $e);
        } catch (ApiException $e) {
            throw new ValidationException($e->getMessage(), $e);
        }
    }

    private function getMagentoAttributeCodes(): array
    {
        $productAttributeArray = $this->magentoAttributes->getAttributes();

        return array_keys($productAttributeArray);
    }
}

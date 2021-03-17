<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Model\Amazon\Pricing;

use Magento\Amazon\Api\Data\PricingRuleInterface;
use Magento\Amazon\Api\PricingRuleRepositoryInterface;
use Magento\Amazon\Model\ResourceModel\Amazon\Pricing\Rule as ResourceModel;
use Magento\Amazon\Model\ResourceModel\Amazon\Pricing\Rule\CollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class PricingRuleRepository
 */
class PricingRuleRepository implements PricingRuleRepositoryInterface
{
    /** @var RuleFactory $ruleFactory */
    protected $ruleFactory;
    /** @var ResourceModel $resourceModel */
    protected $resourceModel;
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;

    /**
     * @param RuleFactory $ruleFactory
     * @param ResourceModel $resourceModel
     * @param CollectionFactory $collectionFactory
     */
    public function __construct(
        RuleFactory $ruleFactory,
        ResourceModel $resourceModel,
        CollectionFactory $collectionFactory
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->resourceModel = $resourceModel;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function save(PricingRuleInterface $account)
    {
        try {
            $this->resourceModel->save($account);
        } catch (\Exception $e) {
            $phrase = __('Unable to pricing rule. Please try again.');
            throw new CouldNotSaveException($phrase);
        }

        return $account;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($id)
    {
        /** @var RuleFactory */
        $rule = $this->ruleFactory->create()->load($id);

        if (!$rule->getId()) {
            return;
        }

        // delete rule
        try {
            $this->resourceModel->delete($rule);
        } catch (\Exception $e) {
            $phrase = __('An error occured while attempting to delete the pricing rule. Please try again.');
            throw new CouldNotDeleteException($phrase);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        /** @var CollectionFactory */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('id', $id);

        return $collection->getFirstItem();
    }
}

<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing\Rules\View\Tab;

use Magento\Amazon\Api\ListingRuleRepositoryInterface;
use Magento\Amazon\Block\Adminhtml\Amazon\Account\AbstractRule;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Framework\Data\Form;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Registry;
use Magento\Rule\Block\Conditions as RuleConditions;

/**
 * Class Conditions
 */
class Conditions extends AbstractRule
{
    /** @var ListingRuleRepositoryInterface $listingRuleRepository */
    protected $listingRuleRepository;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param RuleConditions $conditions
     * @param Fieldset $rendererFieldset
     * @param RuleFactory $ruleFactory
     * @param ListingRuleRepositoryInterface $listingRuleRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        RuleConditions $conditions,
        Fieldset $rendererFieldset,
        RuleFactory $ruleFactory,
        ListingRuleRepositoryInterface $listingRuleRepository,
        array $data = []
    ) {
        $this->listingRuleRepository = $listingRuleRepository;
        parent::__construct(
            $context,
            $registry,
            $formFactory,
            $conditions,
            $rendererFieldset,
            $ruleFactory,
            $data
        );
    }

    /**
     * Prepare form before rendering HTML
     *
     * @return AbstractRule
     */
    protected function _prepareForm()
    {
        /** @var int */
        $merchantId = $this->getRequest()->getParam('merchant_id');
        /** @var ListingRuleRepositoryInterface */
        $marketplaceRule = $this->listingRuleRepository->getByMerchantId($merchantId);
        /** @var RuleFactory */
        $rule = $this->ruleFactory->create();

        $rule->setData($marketplaceRule->getData());

        /** @var Form */
        $form = $this->addTabToForm($rule, 'conditions_fieldset', 'amazon_account_listing_rules_form');
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

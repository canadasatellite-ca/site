<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Pricing\Rules\Create\View\Tab;

use Magento\Amazon\Api\Data\PricingRuleInterfaceFactory;
use Magento\Amazon\Api\PricingRuleRepositoryInterface;
use Magento\Amazon\Block\Adminhtml\Amazon\Account\AbstractRule;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Form\Renderer\Fieldset;
use Magento\CatalogRule\Model\RuleFactory;
use Magento\Framework\Data\FormFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Rule\Block\Conditions as RuleConditions;

/**
 * Class Conditions
 */
class Conditions extends AbstractRule
{
    /** @var PricingRuleRepositoryInterface $pricingRuleRepository */
    protected $pricingRuleRepository;
    /** @var PricingRuleInterfaceFactory $pricingRuleFactory */
    protected $pricingRuleFactory;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param FormFactory $formFactory
     * @param RuleConditions $conditions
     * @param Fieldset $rendererFieldset
     * @param RuleFactory $ruleFactory
     * @param PricingRuleRepositoryInterface $pricingRuleRepository
     * @param PricingRuleInterfaceFactory $pricingRuleFactory
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        FormFactory $formFactory,
        RuleConditions $conditions,
        Fieldset $rendererFieldset,
        RuleFactory $ruleFactory,
        PricingRuleRepositoryInterface $pricingRuleRepository,
        PricingRuleInterfaceFactory $pricingRuleFactory,
        array $data = []
    ) {
        $this->pricingRuleRepository = $pricingRuleRepository;
        $this->pricingRuleFactory = $pricingRuleFactory;
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
        $id = $this->getRequest()->getParam('id');

        /** @var PricingRuleInterfaceFactory */
        try {
            $pricingRule = $this->pricingRuleRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            $pricingRule = $this->pricingRuleFactory->create();
        }

        /** @var RuleFactory */
        $rule = $this->ruleFactory->create();

        $rule->setData($pricingRule->getData());

        /** @var FormFactory */
        $form = $this->addTabToForm($rule);
        $this->setForm($form);

        return parent::_prepareForm();
    }
}

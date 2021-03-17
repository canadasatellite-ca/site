<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\DataProvider\Form\Amazon\Account\Pricing\Rules\Create;

use Magento\Amazon\Api\Data\PricingRuleInterface;
use Magento\Amazon\Api\ListingManagementInterface;
use Magento\Amazon\Api\PricingRuleRepositoryInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class Modifier
 */
class Modifier implements ModifierInterface
{
    /** @var Http $request */
    protected $request;
    /** @var ListingManagementInterface $listingManagement */
    protected $listingManagement;
    /** @var PricingRuleRepositoryInterface $pricingRuleRepository */
    protected $pricingRuleRepository;

    /**
     * @param Http $request
     * @param ListingManagementInterface $listingManagement
     * @param PricingRuleRepositoryInterface $pricingRuleRepository
     * @internal param LocatorInterface $locator
     */
    public function __construct(
        Http $request,
        ListingManagementInterface $listingManagement,
        PricingRuleRepositoryInterface $pricingRuleRepository
    ) {
        $this->request = $request;
        $this->listingManagement = $listingManagement;
        $this->pricingRuleRepository = $pricingRuleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        /** @var int */
        $merchantId = $this->request->getParam('merchant_id');
        /** @var int */
        $ruleId = $this->request->getParam('id');
        /** @var array */
        $newMeta = [];

        try {
            if ($this->listingManagement->isSubNewConditionListing($merchantId)) {
                $newMeta = $this->showFieldset($newMeta);
            } else {
                $newMeta = $this->hideFieldset($newMeta);
            }

            /** @var PricingRuleInterface */
            $rule = $this->pricingRuleRepository->getById($ruleId);

            // intelligent rule
            if ($rule->getAuto()) {
                $newMeta = $this->showAutoRule($rule, $newMeta);
            } else { // standard rule
                $newMeta = $this->hideAutoRule($newMeta);
            }
        } catch (NoSuchEntityException $e) {
            $newMeta = $this->hideAutoRule($newMeta);
        }

        $meta['rule_actions']['children'] = $newMeta;

        $meta = $this->prepareTooltipLinks($meta);

        return $meta;
    }

    /**
     * Prepare tooltip links
     *
     * @param array $meta
     * @return array
     */
    private function prepareTooltipLinks(array $meta)
    {
        /** @var int */
        $merchantId = $this->request->getParam('merchant_id');

        $stopRulesProcessingTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_PRICING_RULE_STOP_RULES_PROCESSING
        ];

        $priorityTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_PRICING_RULE_PRIORITY
        ];

        $autoTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_PRICING_RULE_AUTO
        ];

        $autoSourceTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_PRICING_RULE_AUTO_SOURCE
        ];

        $autoMinimumFeedbackTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_PRICING_RULE_AUTO_MINIMUM_FEEDBACK
        ];

        $autoFeedbackCountTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_PRICING_RULE_AUTO_FEEDBACK_COUNT
        ];

        $autoConditionTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_PRICING_RULE_AUTO_CONDITION
        ];

        $priceMovementTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_PRICING_RULE_PRICE_MOVEMENT
        ];

        $priceMovementTwoTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_PRICING_RULE_PRICE_MOVEMENT_TWO
        ];

        $floorTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_PRICING_RULE_FLOOR
        ];

        $floorPriceMovementTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_PRICING_RULE_FLOOR_PRICE_MOVEMENT
        ];

        $ceilingTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_PRICING_RULE_CEILING
        ];

        $ceilingPriceMovementTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_PRICING_RULE_CEILING_PRICE_MOVEMENT
        ];

        // @codingStandardsIgnoreStart Magento2.Files.LineLength.MaxExceeded
        $meta = array_replace_recursive(
            $meta,
            [
                'rule_settings' => [
                    'children' => [
                        'sort_order' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/pricing-rules-priority',
                                        'tooltip' => $priorityTooltip
                                    ]
                                ]
                            ]
                        ],
                        'stop_rules_processing' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/pricing-rules-stop-rules-processing',
                                        'tooltip' => $stopRulesProcessingTooltip
                                    ]
                                ]
                            ]
                        ],
                        'merchant_id' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'value' => $merchantId
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'rule_actions' => [
                    'children' => [
                        'price_rule_type' => [
                            'children' => [
                                'auto' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/pricing-rules-auto',
                                                'tooltip' => $autoTooltip
                                            ]
                                        ]
                                    ]
                                ],
                                'auto_source' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/pricing-rules-auto-source',
                                                'tooltip' => $autoSourceTooltip
                                            ]
                                        ]
                                    ]
                                ],
                                'auto_minimum_feedback' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/pricing-rules-auto-minimum-feedback',
                                                'tooltip' => $autoMinimumFeedbackTooltip
                                            ]
                                        ]
                                    ]
                                ],
                                'auto_feedback_count' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/pricing-rules-auto-feedback-count',
                                                'tooltip' => $autoFeedbackCountTooltip
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'condition_outer_fieldset' => [
                            'children' => [
                                'condition_inner_fieldset' => [
                                    'children' => [
                                        'auto_condition' => [
                                            'arguments' => [
                                                'data' => [
                                                    'config' => [
                                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/pricing-rules-auto-condition',
                                                        'tooltip' => $autoConditionTooltip
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'price_rule_action' => [
                            'children' => [
                                'price_action_one_fieldset' => [
                                    'children' => [
                                        'price_movement_one' => [
                                            'arguments' => [
                                                'data' => [
                                                    'config' => [
                                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/pricing-rules-price-movement',
                                                        'tooltip' => $priceMovementTooltip
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ],
                                'price_action_two_fieldset' => [
                                    'children' => [
                                        'price_movement_two' => [
                                            'arguments' => [
                                                'data' => [
                                                    'config' => [
                                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/pricing-rules-price-movement-two',
                                                        'tooltip' => $priceMovementTwoTooltip
                                                    ]
                                                ]
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'floor_fieldset' => [
                            'children' => [
                                'floor' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/pricing-rules-floor',
                                                'tooltip' => $floorTooltip
                                            ]
                                        ]
                                    ]
                                ],
                                'floor_price_movement' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/pricing-rules-floor-price-movement',
                                                'tooltip' => $floorPriceMovementTooltip
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'ceiling_fieldset' => [
                            'children' => [
                                'ceiling' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/pricing-rules-ceiling',
                                                'tooltip' => $ceilingTooltip
                                            ]
                                        ]
                                    ]
                                ],
                                'ceiling_price_movement' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/pricing-rules-ceiling-price-movement',
                                                'tooltip' => $ceilingPriceMovementTooltip
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );
        // @codingStandardsIgnoreEnd Magento2.Files.LineLength.MaxExceeded

        return $meta;
    }

    /**
     * Edits field dependencies on auto / intelligent rule enabled
     *
     * @param PricingRuleInterface $rule
     * @param array $meta
     * @return array
     */
    private function showAutoRule($rule, $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                'price_rule_type' => [
                    'children' => [
                        'auto_source' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => false
                                    ]
                                ]
                            ]
                        ],
                        'auto_minimum_feedback' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => ($rule->getAutoSource()) ? false : true
                                    ]
                                ]
                            ]
                        ],
                        'auto_feedback_count' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => ($rule->getAutoSource()) ? false : true
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'price_rule_action' => [
                    'children' => [
                        'price_action_two_fieldset' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => true
                                    ]
                                ]
                            ],
                            'children' => [
                                'simple_action_two' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => ($rule->getPriceMovement()) ? true : false
                                            ]
                                        ]
                                    ]
                                ],
                                'discount_amount_two' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => ($rule->getPriceMovement()) ? true : false
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ],
                        'price_action_one_fieldset' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => false
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'floor_fieldset' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'visible' => true
                            ]
                        ]
                    ],
                    'children' => [
                        'floor_price_movement' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => ($rule->getFloor()) ? false : true
                                    ]
                                ]
                            ]
                        ],
                        'floor_simple_action' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => ($rule->getFloorPriceMovement()) ? false : true
                                    ]
                                ]
                            ]
                        ],
                        'floor_discount_amount' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => ($rule->getFloorPriceMovement()) ? false : true
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'ceiling_fieldset' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'visible' => true
                            ]
                        ]
                    ],
                    'children' => [
                        'ceiling_price_movement' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => ($rule->getCeiling()) ? false : true
                                    ]
                                ]
                            ]
                        ],
                        'ceiling_simple_action' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => ($rule->getCeilingPriceMovement()) ? false : true
                                    ]
                                ]
                            ]
                        ],
                        'ceiling_discount_amount' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => ($rule->getCeilingPriceMovement()) ? false : true
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        $meta = array_replace_recursive(
            $meta,
            [
                'condition_outer_fieldset' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'visible' => true
                            ]
                        ]
                    ],
                    'children' => [
                        'condition_inner_fieldset' => [
                            'children' => [
                                'new_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => ($rule->getAutoCondition() == 2) ? true : false
                                            ]
                                        ]
                                    ]
                                ],
                                'refurbished_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => ($rule->getAutoCondition() == 2) ? true : false
                                            ]
                                        ]
                                    ]
                                ],
                                'usedlikenew_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => ($rule->getAutoCondition() == 2) ? true : false
                                            ]
                                        ]
                                    ]
                                ],
                                'usedverygood_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => ($rule->getAutoCondition() == 2) ? true : false
                                            ]
                                        ]
                                    ]
                                ],
                                'usedgood_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => ($rule->getAutoCondition() == 2) ? true : false
                                            ]
                                        ]
                                    ]
                                ],
                                'usedacceptable_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => ($rule->getAutoCondition() == 2) ? true : false
                                            ]
                                        ]
                                    ]
                                ],
                                'collectiblelikenew_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => ($rule->getAutoCondition() == 2) ? true : false
                                            ]
                                        ]
                                    ]
                                ],
                                'collectibleverygood_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => ($rule->getAutoCondition() == 2) ? true : false
                                            ]
                                        ]
                                    ]
                                ],
                                'collectiblegood_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => ($rule->getAutoCondition() == 2) ? true : false
                                            ]
                                        ]
                                    ]
                                ],
                                'collectibleacceptable_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => ($rule->getAutoCondition() == 2) ? true : false
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $meta;
    }

    /**
     * Edits field dependencies on auto / intelligent rule disabled
     *
     * @param array $meta
     * @return array
     */
    private function hideAutoRule($meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                'price_rule_type' => [
                    'children' => [
                        'auto_minimum_feedback' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => true
                                    ]
                                ]
                            ]
                        ],
                        'auto_feedback_count' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => true
                                    ]
                                ]
                            ]
                        ],
                        'auto_source' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'disabled' => true
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'price_rule_action' => [
                    'children' => [
                        'price_action_two_fieldset' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => false
                                    ]
                                ]
                            ]
                        ],
                        'price_action_one_fieldset' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => true
                                    ]
                                ]
                            ]
                        ],
                    ]
                ],
                'floor_fieldset' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'visible' => false
                            ]
                        ]
                    ]
                ],
                'ceiling_fieldset' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'visible' => false
                            ]
                        ]
                    ]
                ]
            ]
        );

        $meta = array_replace_recursive(
            $meta,
            [
                'condition_outer_fieldset' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'visible' => false
                            ]
                        ]
                    ],
                    'children' => [
                        'condition_inner_fieldset' => [
                            'children' => [
                                'new_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'refurbished_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'usedlikenew_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'usedverygood_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'usedgood_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'usedacceptable_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'collectiblelikenew_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'collectibleverygood_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'collectiblegood_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ],
                                'collectibleacceptable_variance' => [
                                    'arguments' => [
                                        'data' => [
                                            'config' => [
                                                'visible' => false
                                            ]
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $meta;
    }

    /**
     * Hide fieldset
     *
     * @param array $meta
     * @return array
     */
    private function hideFieldset($meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                'condition_outer_fieldset' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'visible' => false
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $meta;
    }

    /**
     * Show fieldset
     *
     * @param array $meta
     * @return array
     */
    private function showFieldset($meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                'condition_outer_fieldset' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'visible' => true
                            ]
                        ]
                    ]
                ]
            ]
        );

        return $meta;
    }
}

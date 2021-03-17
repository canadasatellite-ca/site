<?php

/** Magedelight
 * Copyright (C) 2017 Magedelight <info@magedelight.com>
 *
 * @category Magedelight
 * @package Magedelight_Subscribenow
 * @copyright Copyright (c) 2017 Mage Delight (http://www.magedelight.com/)
 * @license http://opensource.org/licenses/gpl-3.0.html GNU General Public License,version 3 (GPL-3.0)
 * @author Magedelight <info@magedelight.com>
 */

namespace Magedelight\Subscribenow\Ui\DataProvider\Product\Modifier;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\Form;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;

/**
 * Class GiftMessageDataProvider.
 */
class Subscribenow extends AbstractModifier
{

    const FIELD_MESSAGE_AVAILABLE = 'is_subscription';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * @var LocatorInterface
     */
    protected $locator;

    /**
     * @var ArrayManager
     */
    protected $arrayManager;

    /**
     * @var array
     */
    protected $meta = [];

    /**
     * @param LocatorInterface     $locator
     * @param ArrayManager         $arrayManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        LocatorInterface $locator,
        ArrayManager $arrayManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->locator = $locator;
        $this->arrayManager = $arrayManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyData(array $data)
    {
        $modelId = $this->locator->getProduct()->getId();

        if (!isset($data[$modelId][static::DATA_SOURCE_DEFAULT]['is_subscription'])) {
            $data[$modelId][static::DATA_SOURCE_DEFAULT]['is_subscription'] = '0';
        }

        return $data;
    }

    /**
     * {@inheritdoc}
     */
    public function modifyMeta(array $meta)
    {
        $this->meta = $meta;
        $this->customizeStatusField();
        $this->customizeDiscountAmountField();
        $this->customizeInitialAmountField();
        $this->customizeBillingMaxCyclesField();
        $this->customizeDayOfMonthField();
        $this->customizeTrialAmountField();
        $this->customizeTrialMaxCyclesField();

        return $this->meta;
    }

    /**
     * Customize Status field.
     *
     * @param array $meta
     *
     * @return array
     */
    protected function customizeStatusField()
    {
        $switcherConfig = [
            'dataType' => Select::NAME,
            'formElement' => Select::NAME,
            'componentType' => Form\Field::NAME,
            'template' => 'Magento_Catalog/form/element/field',
            'validation' => [
                'subscribenowoption' => true,
            ],
        ];

        $path = $this->arrayManager->findPath('is_subscription', $this->meta, null, 'children');
        $this->meta = $this->arrayManager->merge($path . '/arguments/data/config', $this->meta, $switcherConfig);

        return $this->meta;
    }

    /**
     * Customize Discount amount field.
     *
     * @param array $meta
     *
     * @return array
     */
    protected function customizeDiscountAmountField()
    {
        $switcherConfig = [
            'dataType' => Input::NAME,
            'formElement' => Input::NAME,
            'componentType' => Form\Field::NAME,
            'validation' => [
                'validate-greater-than-zero' => true,
                'validate-number' => true,
            ],
            'notice' => __(
                'Discount will applied on product price.'
            ),
        ];

        $path = $this->arrayManager->findPath('discount_amount', $this->meta, null, 'children');
        $this->meta = $this->arrayManager->merge($path . '/arguments/data/config', $this->meta, $switcherConfig);

        return $this->meta;
    }

    /**
     * Customize Initial amount field.
     *
     * @param array $meta
     *
     * @return array
     */
    protected function customizeInitialAmountField()
    {
        $switcherConfig = [
            'dataType' => Input::NAME,
            'formElement' => Input::NAME,
            'componentType' => Form\Field::NAME,
            'validation' => [
                'validate-number' => true,
                'validate-greater-than-zero' => true,
            ],
        ];

        $path = $this->arrayManager->findPath('initial_amount', $this->meta, null, 'children');
        $this->meta = $this->arrayManager->merge($path . '/arguments/data/config', $this->meta, $switcherConfig);

        return $this->meta;
    }

    /**
     * Customize Billing max cycles field.
     *
     * @param array $meta
     *
     * @return array
     */
    protected function customizeBillingMaxCyclesField()
    {
        $switcherConfig = [
            'dataType' => Input::NAME,
            'formElement' => Input::NAME,
            'componentType' => Form\Field::NAME,
            'validation' => [
                'validate-digits' => true,
                'validate-number' => true,
                'validate-greater-than-zero' => true,
            ],
        ];

        $path = $this->arrayManager->findPath('billing_max_cycles', $this->meta, null, 'children');
        $this->meta = $this->arrayManager->merge($path . '/arguments/data/config', $this->meta, $switcherConfig);

        return $this->meta;
    }

    /**
     * Customize Day of month field.
     *
     * @param array $meta
     *
     * @return array
     */
    protected function customizeDayOfMonthField()
    {
        $switcherConfig = [
            'dataType' => Input::NAME,
            'formElement' => Input::NAME,
            'componentType' => Form\Field::NAME,
            'notice' => __(
                'Apply only if subscription start from is selected as Exact Day Of Month'
            ),
            'validation' => [
                'validate-digits' => true,
                'validate-number' => true,
                'validate-greater-than-zero' => true,
                'validate-digits-range' => true,
                'digits-range-1-31' => true,
            ],
        ];

        $path = $this->arrayManager->findPath('day_of_month', $this->meta, null, 'children');
        $this->meta = $this->arrayManager->merge($path . '/arguments/data/config', $this->meta, $switcherConfig);

        return $this->meta;
    }

    /**
     * Customize Trial Amount field.
     *
     * @param array $meta
     *
     * @return array
     */
    protected function customizeTrialAmountField()
    {
        $switcherConfig = [
            'dataType' => Input::NAME,
            'formElement' => Input::NAME,
            'componentType' => Form\Field::NAME,
            'prefer' => 'toggle',
            'validation' => [
                'validate-number' => true,
                'validate-zero-or-greater' => true,
            ],
        ];

        $path = $this->arrayManager->findPath('trial_amount', $this->meta, null, 'children');
        $this->meta = $this->arrayManager->merge($path . '/arguments/data/config', $this->meta, $switcherConfig);

        return $this->meta;
    }

    /**
     * Customize Trial max cycles field.
     *
     * @param array $meta
     *
     * @return array
     */
    protected function customizeTrialMaxCyclesField()
    {
        $switcherConfig = [
            'dataType' => Input::NAME,
            'formElement' => Input::NAME,
            'componentType' => Form\Field::NAME,
            'prefer' => 'toggle',
            'validation' => [
                'validate-number' => true,
                'validate-digits' => true,
                'validate-greater-than-zero' => true,
            ],
        ];

        $path = $this->arrayManager->findPath('trial_maxcycle', $this->meta, null, 'children');
        $this->meta = $this->arrayManager->merge($path . '/arguments/data/config', $this->meta, $switcherConfig);

        return $this->meta;
    }
}

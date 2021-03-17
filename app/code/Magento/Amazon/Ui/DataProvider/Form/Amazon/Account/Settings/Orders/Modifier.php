<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\DataProvider\Form\Amazon\Account\Settings\Orders;

use Magento\Amazon\Api\AccountOrderRepositoryInterface;
use Magento\Amazon\Api\Data\AccountOrderInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Msi\MsiChecker;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class Modifier
 */
class Modifier implements ModifierInterface
{
    /** Container fieldset prefix */
    const CONTAINER_PREFIX = 'container_';
    const META_CONFIG_PATH = '/arguments/data/config';

    /** @var ArrayManager $arrayManager */
    protected $arrayManager;
    /** @var Http $request */
    protected $request;
    /** @var AccountOrderRepositoryInterface $accountOrderRepository */
    protected $accountOrderRepository;
    /** @var MsiChecker */
    private $msiChecker;

    /**
     * @param ArrayManager $arrayManager
     * @param Http $request
     * @param AccountOrderRepositoryInterface $accountOrderRepository
     * @param MsiChecker $msiChecker
     */
    public function __construct(
        ArrayManager $arrayManager,
        Http $request,
        AccountOrderRepositoryInterface $accountOrderRepository,
        MsiChecker $msiChecker
    ) {
        $this->arrayManager = $arrayManager;
        $this->request = $request;
        $this->accountOrderRepository = $accountOrderRepository;
        $this->msiChecker = $msiChecker;
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
        $meta = $this->prepareMerchantData($meta);
        $meta = $this->prepareOrderFields($meta);
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
        $orderIsActiveTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_ORDER_IS_ACTIVE
        ];

        $defaultStoreTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_DEFAULT_STORE
        ];

        $customerIsActiveTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_CUSTOMER_IS_ACTIVE
        ];

        $isExternalOrderIdTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_IS_EXTERNAL_ORDER_ID
        ];

        $reserveTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_RESERVE
        ];

        $customStatusIsActiveTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_CUSTOM_STATUS_IS_ACTIVE
        ];

        $meta = array_replace_recursive(
            $meta,
            [
                'orders' => [
                    'children' => [
                        'order_is_active' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/order-is-active',
                                        'tooltip' => $orderIsActiveTooltip
                                    ]
                                ]
                            ]
                        ],
                        'default_store' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/default-store',
                                        'tooltip' => $defaultStoreTooltip
                                    ]
                                ]
                            ]
                        ],
                        'customer_is_active' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/customer-is-active',
                                        'tooltip' => $customerIsActiveTooltip
                                    ]
                                ]
                            ]
                        ],
                        'is_external_order_id' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/is-external-order-id',
                                        'tooltip' => $isExternalOrderIdTooltip
                                    ]
                                ]
                            ]
                        ],
                        'reserve' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/reserve',
                                        'tooltip' => $reserveTooltip
                                    ]
                                ]
                            ]
                        ],
                        'custom_status_is_active' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/custom-status-is-active',
                                        'tooltip' => $customStatusIsActiveTooltip
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
     * Prepare merchant data
     *
     * @param array $meta
     * @return array $meta
     */
    private function prepareMerchantData(array $meta)
    {
        /** @var int */
        $merchantId = $this->request->getParam('merchant_id');

        $meta = array_replace_recursive(
            $meta,
            [
                'orders' => [
                    'children' => [
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
                ]
            ]
        );

        return $meta;
    }

    /**
     * Customize order fields
     *
     * @param array $meta
     * @return array
     */
    private function prepareOrderFields(array $meta)
    {
        /** @var AccountOrderInterface */
        $account = $this->getAccount();
        $allowReserve = !$this->msiChecker->isMsiEnabled();

        if (!$account->getOrderIsActive()) {
            $meta = array_replace_recursive(
                $meta,
                [
                    'orders' => [
                        'children' => [
                            'default_store' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'disabled' => true
                                        ]
                                    ]
                                ]
                            ],
                            'customer_is_active' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'disabled' => true
                                        ]
                                    ]
                                ]
                            ],
                            'reserve' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'disabled' => true,
                                            'visible' => $allowReserve
                                        ]
                                    ]
                                ]
                            ],
                            'custom_status_is_active' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'disabled' => true
                                        ]
                                    ]
                                ]
                            ],
                            'custom_status' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'disabled' => true
                                        ]
                                    ]
                                ]
                            ],
                            'is_external_order_id' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'disabled' => true
                                        ]
                                    ]
                                ]
                            ],
                        ]
                    ]
                ]
            );

            return $meta;
        }

        if (!$account->getCustomStatusIsActive()) {
            $meta = array_replace_recursive(
                $meta,
                [
                    'orders' => [
                        'children' => [
                            'custom_status' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'disabled' => true
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );
        }

        if (!$allowReserve) {
            $meta = array_replace_recursive(
                $meta,
                [
                    'orders' => [
                        'children' => [
                            'reserve' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'disabled' => true,
                                            'visible' => false,
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );
        }

        return $meta;
    }

    /**
     * Get account
     *
     * @return AccountOrderInterface | bool
     */
    private function getAccount()
    {
        /** @var int */
        $merchantId = $this->request->getParam('merchant_id');

        try {
            /** @var AccountOrderInterface $accountOrder */
            $accountOrder = $this->accountOrderRepository->getByMerchantId($merchantId);
            return $accountOrder;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}

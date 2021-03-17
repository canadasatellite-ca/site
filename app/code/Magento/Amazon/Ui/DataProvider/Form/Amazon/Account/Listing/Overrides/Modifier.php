<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\DataProvider\Form\Amazon\Account\Listing\Overrides;

use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\Collection;
use Magento\Amazon\Model\ResourceModel\Amazon\Listing\CollectionFactory;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Stdlib\ArrayManager;
use Magento\Ui\Component\MassAction\Filter;
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
    /** @var ListingRepositoryInterface $listingRepository */
    protected $listingRepository;
    /** @var Filter $filter */
    protected $filter;
    /** @var CollectionFactory $collectionFactory */
    protected $collectionFactory;

    /**
     * @param ArrayManager $arrayManager
     * @param Http $request
     * @param ListingRepositoryInterface $listingRepository
     * @param Filter $filter
     * @param CollectionFactory $collectionFactory
     * @internal param LocatorInterface $locator
     */
    public function __construct(
        ArrayManager $arrayManager,
        Http $request,
        ListingRepositoryInterface $listingRepository,
        Filter $filter,
        CollectionFactory $collectionFactory
    ) {
        $this->arrayManager = $arrayManager;
        $this->request = $request;
        $this->listingRepository = $listingRepository;
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
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
        $handlingOverride = null;
        /** @var float */
        $priceOverride = null;
        /** @var int */
        $conditionOverride = null;
        /** @var string */
        $conditionNotesOverride = null;
        /** @var array */
        $ids = [];

        $tab = $this->request->getParam('tab');

        $meta = $this->prepareTooltipLinks($meta);

        $meta = array_replace_recursive(
            $meta,
            [
                'handling' => [
                    'children' => [
                        'merchant_id' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'componentType' => 'field',
                                        'filter' => 'text',
                                        'formElement' => 'hidden',
                                        'value' => $merchantId
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
                'detail' => [
                    'children' => [
                        'tab' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'value' => $tab
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        );

        if ($id = $this->request->getParam('id')) {
            /** @var ListingInterface */
            $listing = $this->listingRepository->getById($id, true);

            $handlingOverride = $listing->getHandlingOverride();
            $priceOverride = $listing->getAmazonListingPriceOverride();
            $conditionOverride = $listing->getConditionOverride();
            $conditionNotesOverride = $listing->getConditionNotesOverride();
            $fulfilledBy = $listing->getFulfilledBy();

            $meta = array_replace_recursive(
                $meta,
                [
                    'handling' => [
                        'children' => [
                            'id' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'componentType' => 'field',
                                            'filter' => 'text',
                                            'formElement' => 'hidden',
                                            'value' => $id
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            );

            // if fulfilled by Amazon
            if (strpos($fulfilledBy, 'AMAZON_') === 0) {
                $meta = array_replace_recursive(
                    $meta,
                    [
                        'handling' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'visible' => false
                                    ]
                                ]
                            ]
                        ],
                        'condition' => [
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
            }
        } else {
            /** @var CollectionFactory */
            if ($collection = $this->getFilteredCollection($merchantId)) {
                foreach ($collection as $listing) {
                    $ids[] = $listing->getId();
                }
            }

            $meta = array_replace_recursive(
                $meta,
                [
                    'handling' => [
                        'children' => [
                            'selected_ids' => [
                                'arguments' => [
                                    'data' => [
                                        'config' => [
                                            'componentType' => 'field',
                                            'filter' => 'text',
                                            'formElement' => 'hidden',
                                            'value' => json_encode($ids)
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ],
                    'detail' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'visible' => false
                                ]
                            ]
                        ]
                    ],
                    'pricing' => [
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
        }

        $meta = array_replace_recursive(
            $meta,
            [
                'pricing' => [
                    'children' => [
                        'list_price_override' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'value' => $priceOverride
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'handling' => [
                    'children' => [
                        'handling_override' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'value' => $handlingOverride
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'condition' => [
                    'children' => [
                        'condition_override' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'value' => $conditionOverride
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'condition_notes' => [
                    'children' => [
                        'condition_notes_override' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'value' => $conditionNotesOverride
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
     * Prepare tooltip links
     *
     * @param array $meta
     * @return array
     */
    private function prepareTooltipLinks(array $meta)
    {
        $listPriceOverrideTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_LIST_PRICE_OVERRIDE
        ];

        $handlingTimeOverrideTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_HANDLING_TIME_OVERRIDE
        ];

        $conditionOverrideTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_CONDITION_OVERRIDE
        ];

        $conditionNotesOverrideTooltip = [
            'link' => Definitions::UG_URL . Definitions::UG_CONDITION_NOTES_OVERRIDE
        ];

        $meta = array_replace_recursive(
            $meta,
            [
                'pricing' => [
                    'children' => [
                        'list_price_override' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/list-price-override',
                                        'tooltip' => $listPriceOverrideTooltip
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'handling' => [
                    'children' => [
                        'handling_override' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/handling-time-override',
                                        'tooltip' => $handlingTimeOverrideTooltip
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'condition' => [
                    'children' => [
                        'condition_override' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/condition-override',
                                        'tooltip' => $conditionOverrideTooltip
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                'condition_notes' => [
                    'children' => [
                        'condition_notes_override' => [
                            'arguments' => [
                                'data' => [
                                    'config' => [
                                        'tooltipTpl' => 'Magento_Amazon/form/element/tooltip/condition-notes-override',
                                        'tooltip' => $conditionNotesOverrideTooltip
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
     * Filters mass action collection based on user selections
     *
     * @return bool|Collection
     * @throws \Magento\Framework\Exception\LocalizedException
     * @var int $merchantId
     *
     */
    public function getFilteredCollection($merchantId)
    {
        $component = $this->filter->getComponent();
        $this->filter->prepareComponent($component);
        $dataProvider = $component->getContext()->getDataProvider();
        $dataProvider->setLimit(0, false);

        /** @var array */
        $ids = [];

        foreach ($dataProvider->getSearchResult()->getItems() as $document) {
            $ids[] = $document->getId();
        }

        /** @var Collection */
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('id', ['in' => $ids]);
        $selected = $this->request->getParam('selected');
        $excluded = $this->request->getParam('excluded');

        $collection->addFieldToFilter('merchant_id', $merchantId);

        if ('false' !== $excluded) {
            if (is_array($excluded) && !empty($excluded)) {
                $collection->addFieldToFilter('id', ['nin' => $excluded]);
            } elseif (is_array($selected) && !empty($selected)) {
                $collection->addFieldToFilter('id', ['in' => $selected]);
            } else {
                return false;
            }
        }

        if (count($collection)) {
            return $collection;
        }

        return false;
    }
}

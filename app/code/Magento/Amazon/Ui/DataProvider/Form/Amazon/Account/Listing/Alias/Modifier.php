<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\DataProvider\Form\Amazon\Account\Listing\Alias;

use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Model\Amazon\Definitions;
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

    /** @var ArrayManager $arrayManager */
    protected $arrayManager;
    /** @var Http $request */
    protected $request;
    /** @var ListingRepositoryInterface $listingRepository */
    protected $listingRepository;

    /**
     * @param ArrayManager $arrayManager
     * @param Http $request
     * @param ListingRepositoryInterface $listingRepository
     */
    public function __construct(
        ArrayManager $arrayManager,
        Http $request,
        ListingRepositoryInterface $listingRepository
    ) {
        $this->arrayManager = $arrayManager;
        $this->request = $request;
        $this->listingRepository = $listingRepository;
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
        /** @var ListingInterface */
        if ($listing = $this->getListing()) {
            $meta = $this->prepareRemoveFlag($meta, $listing);
        }

        return $meta;
    }

    /**
     * Get listing
     *
     * @return ListingInterface | bool
     */
    private function getListing()
    {
        /** @var int $id */
        $id = $this->request->getParam('id');

        try {
            /** @var ListingInterface $listing */
            $listing = $this->listingRepository->getById($id);
            return $listing;
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }

    /**
     * Hide remove flag (if listing is already ended)
     *
     * @param array $meta
     * @param ListingInterface $listing
     * @return array
     */
    private function prepareRemoveFlag(array $meta, ListingInterface $listing)
    {
        /** @var int */
        $listStatus = $listing->getListStatus();
        /** @var array */
        $statuses = [
            Definitions::ENDED_LIST_STATUS,
            Definitions::TOBEENDED_LIST_STATUS
        ];

        if (!in_array($listStatus, $statuses)) {
            return $meta;
        }

        $meta = array_replace_recursive(
            $meta,
            [
                'listing_action_alias' => [
                    'children' => [
                        'remove_flag' => [
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
        );

        return $meta;
    }
}

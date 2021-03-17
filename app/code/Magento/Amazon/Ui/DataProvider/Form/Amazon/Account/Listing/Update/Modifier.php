<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\DataProvider\Form\Amazon\Account\Listing\Update;

use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Amazon\Model\Amazon\Definitions;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

/**
 * Class Modifier
 */
class Modifier implements ModifierInterface
{
    /** @var string */
    const CONDITION_FIELDSET_NAME = 'list_condition_fieldset';
    /** @var string */
    const NEEDS_ASIN_FIELDSET_NAME = 'listing_needsasin';
    /** @var string */
    const MULTIPLES_FIELDSET_NAME = 'listing_multiples';
    /** @var string */
    const VARIANTS_FIELDSET_NAME = 'listing_variants';

    /** @var Http $request */
    protected $request;
    /** @var ListingRepositoryInterface $listingRepository */
    protected $listingRepository;

    /**
     * @param Http $request
     * @param ListingRepositoryInterface $listingRepository
     * @internal param LocatorInterface $locator
     */
    public function __construct(
        Http $request,
        ListingRepositoryInterface $listingRepository
    ) {
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
        /** @var int */
        $id = $this->request->getParam('id');

        // hide all fieldsets (initial state)
        $meta = $this->hideFieldset(self::CONDITION_FIELDSET_NAME, $meta);
        $meta = $this->hideFieldset(self::MULTIPLES_FIELDSET_NAME, $meta);
        $meta = $this->hideFieldset(self::NEEDS_ASIN_FIELDSET_NAME, $meta);
        $meta = $this->hideFieldset(self::VARIANTS_FIELDSET_NAME, $meta);

        try {

            /** @var ListingInterface */
            $listing = $this->listingRepository->getById($id);
            /** @var int */
            $listStatus = $listing->getListStatus();

            switch ($listStatus) {
                case Definitions::MISSING_CONDITION_LIST_STATUS:
                    $meta = $this->showFieldset(self::CONDITION_FIELDSET_NAME, $meta);
                    break;
                case Definitions::MULTIPLE_LIST_STATUS:
                    $meta = $this->showFieldset(self::MULTIPLES_FIELDSET_NAME, $meta);
                    break;
                case Definitions::VARIANTS_LIST_STATUS:
                    $meta = $this->showFieldset(self::VARIANTS_FIELDSET_NAME, $meta);
                    break;
                default:
                    $meta = $this->showFieldset(self::NEEDS_ASIN_FIELDSET_NAME, $meta);
            }
        } catch (NoSuchEntityException $e) {
            $meta = $this->showFieldset(self::NEEDS_ASIN_FIELDSET_NAME, $meta);
        }

        return $meta;
    }

    /**
     * Hide fieldset
     *
     * @param string $name
     * @param array $meta
     * @return array
     */
    private function hideFieldset($name, $meta)
    {
        $meta[$name]['arguments']['data']['config']['visible'] = false;
        return $meta;
    }

    /**
     * Show fieldset
     *
     * @param string $name
     * @param array $meta
     * @return array
     */
    private function showFieldset($name, $meta)
    {
        $meta[$name]['arguments']['data']['config']['visible'] = true;
        return $meta;
    }
}

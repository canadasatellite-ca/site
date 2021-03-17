<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing;

use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class Detail
 */
class Details extends Template
{
    protected $_template = 'Magento_Amazon::amazon/account/listing/details.phtml';

    protected $listingRepository;

    /**
     * @param Context $context
     * @param ListingRepositoryInterface $listingRepository
     * @param array $data
     */
    public function __construct(
        Context $context,
        ListingRepositoryInterface $listingRepository,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->listingRepository = $listingRepository;
        $this->setData('use_container', true);
    }

    /**
     * Returns the listing object
     *
     * @return bool|ListingInterface
     */
    public function getListing()
    {
        if (!$id = $this->getRequest()->getParam('id')) {
            return false;
        }

        try {
            return $this->listingRepository->getById($id);
        } catch (NoSuchEntityException $e) {
            return false;
        }
    }
}

<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing\Alias\View\Help;

use Magento\Amazon\Api\Data\ListingInterface;
use Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing\Update\View\Help;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class AliasSku
 */
class AliasSku extends Help
{
    protected $_template = 'Magento_Amazon::amazon/account/listing/alias/aliassku.phtml';

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

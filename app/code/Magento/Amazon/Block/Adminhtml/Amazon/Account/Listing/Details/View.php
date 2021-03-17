<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Block\Adminhtml\Amazon\Account\Listing\Details;

use Magento\Amazon\Api\ListingRepositoryInterface;
use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;

/**
 * Class View
 */
class View extends Container
{
    /** @var string */
    protected $_template = 'amazon/account/listing/details/view.phtml';

    /** @var ListingRepositoryInterface $listingRepository */
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
        $this->listingRepository = $listingRepository;
        parent::__construct($context, $data);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->_objectId = 'id';
        $this->_controller = 'adminhtml_amazon_account_listing_details';
        $this->_mode = 'view';
        $this->_blockGroup = 'Magento_Amazon';

        $this->buttonList->remove('delete');
        $this->buttonList->remove('save');
        $this->buttonList->remove('reset');
        $this->buttonList->remove('back');
        $this->setData('id', 'channel_amazon_account_listing_details_index');
    }

    /**
     * @return string
     */
    public function getSwitchUrl()
    {
        if ($url = $this->getData('switch_url')) {
            return $url;
        }
        return $this->getUrl('adminhtml/*/*', ['_current' => true, 'period' => null]);
    }
}

<?php

/**
 * Copyright © Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Amazon\Ui\Component\MassAction\Amazon\Listing;

use Magento\Ui\Component\Control\Action;

/**
 * Class ThirdParty
 */
class ThirdParty extends Action
{
    /**
     * Prepare
     *
     * @return void
     */
    public function prepare()
    {
        $config = $this->getConfiguration();
        $context = $this->getContext();
        $config['url'] = $context->getUrl(
            $config['listingAction'],
            ['merchant_id' => $context->getRequestParam('merchant_id'), "tab" => "listing_view_thirdparty"]
        );
        $this->setData('config', (array)$config);
        parent::prepare();
    }
}

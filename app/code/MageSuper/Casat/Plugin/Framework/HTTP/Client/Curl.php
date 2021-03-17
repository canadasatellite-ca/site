<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\Casat\Plugin\Framework\HTTP\Client;

/**
 * Class to work with HTTP protocol using curl library
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Curl extends \Magento\Framework\HTTP\Client\Curl implements \Magento\Framework\HTTP\ClientInterface
{

    /**
     * Make request
     * @param string $method
     * @param string $uri
     * @param array $params
     * @return void
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    protected function makeRequest($method, $uri, $params = [])
    {
        if($uri=='https://dashboard2.cart2quote.com/api/v1/licenses/domain.json' && strpos($_SERVER['REQUEST_URI'],'/admin_1st8gu/')===false){
            return;
        }
        parent::makeRequest($method, $uri, $params);
    }
}

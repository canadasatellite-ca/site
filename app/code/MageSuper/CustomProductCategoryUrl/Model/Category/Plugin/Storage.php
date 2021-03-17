<?php
/**
 * Copyright © 2013-2017 Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace MageSuper\CustomProductCategoryUrl\Model\Category\Plugin;

use Magento\CatalogUrlRewrite\Model\Category\ProductFactory;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\UrlRewrite\Model\StorageInterface;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;

class Storage
{
    protected $unique = [];
    public function beforeReplace(StorageInterface $object, array $urls)
    {
        foreach ($urls as $key=> $url) {
            $path = $this->getUnique($url);
            $url->setRequestPath($path);
        }
        return array($urls);
    }

    public function getUnique($url, $i = 0){
        $path = $url->getRequestPath();
        if ($url->getEntityType() == 'category'){
            return $path;
        }
        if(strlen($path)>255){
            $path = md5($path);
        }
        $path_hash = md5($path).$url->getStoreId();
        if (isset($this->unique[$path_hash])){
            $path = explode('.',$path);
            $path[0] = $path[0].'-'.$url->getEntityId();
            $path = implode('.',$path);
            $url->setRequestPath($path);
            $path = $this->getUnique($url,++$i);
        }
        $path_hash = md5($path).$url->getStoreId();
        $this->unique[$path_hash] = $path;
        return $path;
    }
}

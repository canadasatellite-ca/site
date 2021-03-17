<?php
/**
 * @project: CartImport
 * @author : LitExtension
 * @url    : http://litextension.com
 * @email  : litextension@gmail.com
 */

namespace LitExtension\CartImport\Model;

class Process// extends \Magento\Framework\Model\AbstractModel
{

    protected $_reservedAttr = array();
    protected $_objectManager;
    protected $_configManager;
    protected $_scopeConfig;
    protected $_cacheTypeList;
    protected $_resource;
    protected $_indexersConfig;

    public function __construct(
        //\Magento\Framework\Model\Context $nhtec2727b3b71f07635f726026bef44352ec89e452,
        //\Magento\Framework\Registry $nhtf687604ae801bc390ff2b07bd9ad7ace07f30862,
        \Magento\Framework\App\Config\ScopeConfigInterface $nht7e30e5879651ff951a7471e5c4d8996bac0a0c21,
        \Magento\Framework\ObjectManagerInterface $nhtb4d2ed732317b214b436bccb235e3e68b2da49d1,
        \Magento\Framework\App\Config\ConfigResource\ConfigInterface $nhtdfba7aade0868074c2861c98e2a9a92f3178a51b,
        \Magento\Framework\App\Cache\TypeListInterface $nht61c81d674a40acdf63b6f2739ab9810528e2929c,
        \Magento\Framework\App\ResourceConnection $nht7a104738973573b63f13bdc7a1d816e09b6016ad,
        \Magento\Framework\Indexer\ConfigInterface $nht0071731889b961f56da85b4e81e86c3d434e8514
    ) {
        $this->_objectManager = $nhtb4d2ed732317b214b436bccb235e3e68b2da49d1;
        $this->_configManager = $nhtdfba7aade0868074c2861c98e2a9a92f3178a51b;
        $this->_scopeConfig = $nht7e30e5879651ff951a7471e5c4d8996bac0a0c21;
        $this->_cacheTypeList = $nht61c81d674a40acdf63b6f2739ab9810528e2929c;
        $this->_resource = $nht7a104738973573b63f13bdc7a1d816e09b6016ad;
        $this->_indexersConfig = $nht0071731889b961f56da85b4e81e86c3d434e8514;
        /*parent::__construct(
            $nhtec2727b3b71f07635f726026bef44352ec89e452,
            $nhtf687604ae801bc390ff2b07bd9ad7ace07f30862
        );*/
    }

    protected function _construct()
    {
        //$this->_init('LitExtension\CartImport\Model\ResourceModel\Process');
    }

    public function getConnection() {
        return $this->_resource->getConnection();
    }
    /**
     * Change index mode to scheduled and sindex status to suspend
     */
    public function stopIndexes()
    {
        foreach (array_keys($this->_indexersConfig->getIndexers()) as $nht03642482ad54154d077c866f27f059cb8a2cdd39) {
            $nht1d06a0d76f000e6edd18de492383983feefced4e = $this->_objectManager->get('Magento\Framework\Indexer\IndexerRegistry')->get($nht03642482ad54154d077c866f27f059cb8a2cdd39);
            if(!$nht1d06a0d76f000e6edd18de492383983feefced4e->isScheduled()) {
                $nht1d06a0d76f000e6edd18de492383983feefced4e->setScheduled(true);
            }
            /*if (!$nhtc2e2d6621334dc890bbd8a69430012c45a83bf65->getView()->isSuspended()) {
                $nhtc2e2d6621334dc890bbd8a69430012c45a83bf65->getView()->suspend();
            }*/
        }
    }

    /**
     * Reindex all type
     */
    public function reIndexes()
    {
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = true;
        $nht7821bc7269c25a0f70481bf8957a2d63f7956389 = $this->_objectManager->create('Magento\Indexer\Model\Indexer\CollectionFactory');
        $nhtfe6b3e0f0a0a6f436538209cf78e8cef50a16456 = $nht7821bc7269c25a0f70481bf8957a2d63f7956389->create()->getItems();
        foreach ($nhtfe6b3e0f0a0a6f436538209cf78e8cef50a16456 as $nhtc2e2d6621334dc890bbd8a69430012c45a83bf65) {
            try {
                $nhtc2e2d6621334dc890bbd8a69430012c45a83bf65->reindexAll();
            } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = false;
            }
        }
        if ($nht37a5301a88da334dc5afc5b63979daa0f3f45e68) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
        } else {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = 'An issue occurred while reindexing, please manually reindex in Index Management.';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Clear magento cache
     */
    public function clearCache()
    {
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        try {
            $nht9767c6ada628421161b177c5ef52d23bf333e829 = array_keys($this->_cacheTypeList->getTypes());
            foreach ($nht9767c6ada628421161b177c5ef52d23bf333e829 as $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9) {
                $this->_cacheTypeList->cleanType($nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9);
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
        } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = 'An issue occurred while refreshing cache, please manually flush cache in Cache Management.';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Process clear store with entity select
     */
    public function clearStore($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed)
    {
        $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61 = $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed->getNotice();
        $nhtc218e39efa2e1aae69f39d2054528369ce1e1f46 = $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['clear_info']['function'];
        return $this->$nhtc218e39efa2e1aae69f39d2054528369ce1e1f46($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61);
    }

    /**
     * Clear product with limit per batch
     */
    protected function _clearProducts($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61)
    {
        if (!$nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['import']['products']) {
            return array(
                'result' => 'process',
                'function' => '_clearCategories'
            );
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array(
            'result' => 'process'
        );
        $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $this->_objectManager->create('Magento\Catalog\Model\Product')
            ->getCollection()
            ->addWebsiteFilter($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['website_id'])
            ->setPageSize($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['clear_info']['limit'])
            ->setCurPage(1);
        if (!count($nht2037de437c80264ccbce8a8b61d0bf9f593d2322)) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearSeoProducts';
        } else {
            foreach ($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nht38a007151abe87cc01a5b6e9cc418e85286e2087) {
                try {
                    $nht38a007151abe87cc01a5b6e9cc418e85286e2087->delete();
                } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = "Product Id = {$nht38a007151abe87cc01a5b6e9cc418e85286e2087->getId()} delete failed. Error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
                    break;
                }
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearProducts';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Clear product seo by previous import
     */
    protected function _clearSeoProducts($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61)
    {
        try {
            $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed->deleteTable('url_rewrite', array(
                'entity_type' => 'product',
                //'description' => 'cm_product'
            ));
        } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
        }
        return array(
            'result' => "process",
            'msg' => "",
            'function' => "_clearCategories"
        );
    }

    /**
     * Clear category with limit per batch
     */
    protected function _clearCategories($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61)
    {
        if (!$nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['import']['categories']) {
            return array(
                'result' => 'process',
                'function' => '_clearCustomers'
            );
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array(
            'result' => 'process'
        );
        if (!$nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['root_category_id']) {
            return array(
                'result' => 'success',
                'function' => '_clearCustomers',
                'msg' => ''
            );
        }
        $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $this->_objectManager->create('Magento\Catalog\Model\Category')
            ->getCollection()
            ->addFieldToFilter('parent_id', $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['root_category_id'])
            ->setPageSize($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['clear_info']['limit'])
            ->setCurPage(1);
        if (!count($nht2037de437c80264ccbce8a8b61d0bf9f593d2322)) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearSeoCategories';
        } else {
            foreach ($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165) {
                try {
                    $nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165->delete();
                } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = "Category Id = {$nht5ccbf9c9c5fc1bc34df8238a97094968f38f5165->getId()} delete failed. Error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
                    break;
                }
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearCategories';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Clear product seo by previous import
     */
    protected function _clearSeoCategories($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61)
    {
        try {
            $nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed->deleteTable('url_rewrite', array(
                'entity_type' => 'category',
                //'description' => 'cm_category'
            ));
        } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
        }
        return array(
            'result' => "process",
            'msg' => "",
            'function' => "_clearCustomers"
        );
    }

    /**
     * Clear customer with limit per batch
     */
    protected function _clearCustomers($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61)
    {
        if (!$nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['import']['customers']) {
            return array(
                'result' => 'process',
                'function' => '_clearOrders'
            );
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array(
            'result' => 'process'
        );
        $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $this->_objectManager->create('Magento\Customer\Model\Customer')
            ->getCollection()
            ->addFieldToFilter('website_id', $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['website_id'])
            ->setPageSize($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['clear_info']['limit'])
            ->setCurPage(1);
        if (!count($nht2037de437c80264ccbce8a8b61d0bf9f593d2322)) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearOrders';
        } else {
            foreach ($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nhtb39f008e318efd2bb988d724a161b61c6909677f) {
                try {
                    $nhtb39f008e318efd2bb988d724a161b61c6909677f->delete();
                } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = "Customer Id = {$nhtb39f008e318efd2bb988d724a161b61c6909677f->getId()} delete failed. Error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
                    break;
                }
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearCustomers';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Clear order with limit per batch
     */
    protected function _clearOrders($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61)
    {
        if (!$nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['import']['orders']) {
            return array(
                'result' => 'process',
                'function' => '_clearReviews'
            );
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array(
            'result' => 'process'
        );
        $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $this->_objectManager->create('Magento\Sales\Model\Order')
            ->getCollection()
            ->addFieldToFilter('store_id', array_values($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['languages']))
            ->setPageSize($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['clear_info']['limit'])
            ->setCurPage(1);
        if (!count($nht2037de437c80264ccbce8a8b61d0bf9f593d2322)) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearReviews';
        } else {
            foreach ($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nhtcce55e4309a753985bdd21919395fdc17daa11e4) {
                try {
                    $nhtcce55e4309a753985bdd21919395fdc17daa11e4->delete();
                } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = "Order Id = {$nhtcce55e4309a753985bdd21919395fdc17daa11e4->getId()} delete failed. Error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
                    break;
                }
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearOrders';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Clear review with limit per batch
     */
    protected function _clearReviews($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61)
    {
        if (!$nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['import']['reviews']) {
            return array(
                'result' => 'process',
                'function' => '_clearTaxRules'
            );
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array(
            'result' => 'process'
        );
        $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $this->_objectManager->create('Magento\Review\Model\Review')
            ->getCollection()
            ->addFieldToFilter('store_id', array_values($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['languages']))
            ->setPageSize($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['clear_info']['limit'])
            ->setCurPage(1);
        if (!count($nht2037de437c80264ccbce8a8b61d0bf9f593d2322)) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearTaxRules';
        } else {
            foreach ($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nht61e62b213a1a56f7695845df4fc372a10cb0a73e) {
                try {
                    $nht61e62b213a1a56f7695845df4fc372a10cb0a73e->delete();
                } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = "Review Id = {$nht61e62b213a1a56f7695845df4fc372a10cb0a73e->getId()} delete failed. Error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
                    break;
                }
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearReviews';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Clear tax rule with limit per batch
     */
    protected function _clearTaxRules($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61)
    {
        if (!$nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['import']['taxes']) {
            return array(
                'result' => 'process',
                'function' => '_clearPages'
            );
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array(
            'result' => 'process'
        );
        $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $this->_objectManager->create('Magento\Tax\Model\Calculation\Rule')
            ->getCollection()
            ->setPageSize($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['clear_info']['limit'])
            ->setCurPage(1);
        if (!count($nht2037de437c80264ccbce8a8b61d0bf9f593d2322)) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearTaxCustomers';
        } else {
            foreach ($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nht88436b5a5e887c17a911cc3e794c6a15ad4c0322) {
                try {
                    $nht88436b5a5e887c17a911cc3e794c6a15ad4c0322->getCalculationModel()->deleteByRuleId($nht88436b5a5e887c17a911cc3e794c6a15ad4c0322->getId());
                    $nht88436b5a5e887c17a911cc3e794c6a15ad4c0322->delete();
                } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = "Tax rule Id = {$nht88436b5a5e887c17a911cc3e794c6a15ad4c0322->getId()} delete failed. Error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
                    break;
                }
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearTaxRules';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Clear tax customer with limit per batch
     */
    protected function _clearTaxCustomers($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61)
    {
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array(
            'result' => 'process'
        );
        $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $this->_objectManager->create('Magento\Tax\Model\ClassModel')
            ->getCollection()
            ->setClassTypeFilter('class_type', \Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER)
            ->setPageSize($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['clear_info']['limit'])
            ->setCurPage(1);
        if (!count($nht2037de437c80264ccbce8a8b61d0bf9f593d2322)) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearTaxProducts';
        } else {
            foreach ($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nht1a59384b9b3917d9ec392b99e0721c4a122eb739) {
                try {
                    $nht1a59384b9b3917d9ec392b99e0721c4a122eb739->delete();
                } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = "Tax customer Id = {$nht1a59384b9b3917d9ec392b99e0721c4a122eb739->getId()} delete failed. Error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
                    break;
                }
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearTaxCustomers';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Clear tax product with limit per batch
     */
    protected function _clearTaxProducts($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61)
    {
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array(
            'result' => 'process'
        );
        $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $this->_objectManager->create('Magento\Tax\Model\ClassModel')
            ->getCollection()
            ->setClassTypeFilter(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT)
            ->setPageSize($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['clear_info']['limit'])
            ->setCurPage(1);
        if (!count($nht2037de437c80264ccbce8a8b61d0bf9f593d2322)) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearTaxRates';
        } else {
            foreach ($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nht3b7b0728849024fcc319c65fa8f67ac21dd6ff28) {
                try {
                    $nht3b7b0728849024fcc319c65fa8f67ac21dd6ff28->delete();
                } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = "Tax product Id = {$nht3b7b0728849024fcc319c65fa8f67ac21dd6ff28->getId()} delete failed. Error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
                    break;
                }
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearTaxProducts';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Clear tax rate with limit per batch
     */
    protected function _clearTaxRates($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61)
    {
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array(
            'result' => 'process'
        );
        $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $this->_objectManager->create('Magento\Tax\Model\Calculation\Rate')
            ->getCollection()
            ->setPageSize($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['clear_info']['limit'])
            ->setCurPage(1);
        if (!count($nht2037de437c80264ccbce8a8b61d0bf9f593d2322)) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'process';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearPages';
        } else {
            foreach ($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nht5558649b199a99a6b3400f7d5ccd1271acb2da58) {
                try {
                    $nht5558649b199a99a6b3400f7d5ccd1271acb2da58->delete();
                } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = "Tax rate Id = {$nht5558649b199a99a6b3400f7d5ccd1271acb2da58->getId()} delete failed. Error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
                    break;
                }
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearTaxRates';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    protected function _clearPages($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61)
    {
        if (!$nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['import']['pages']) {
            return array(
                'result' => 'process',
                'function' => '_clearBlocks'
            );
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array(
            'result' => 'process'
        );
        $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $this->_objectManager->create('Magento\Cms\Model\Page')
            ->getCollection()
            ->setPageSize($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['clear_info']['limit'])
            ->setCurPage(1);
        if (!count($nht2037de437c80264ccbce8a8b61d0bf9f593d2322)) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'process';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearBlocks';
        } else {
            foreach ($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nhtaabd435cfb7a39d097a531176f5da4c574409e7a) {
                try {
                    $nhtaabd435cfb7a39d097a531176f5da4c574409e7a->delete();
                } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = "CMS Page Id = {$nhtaabd435cfb7a39d097a531176f5da4c574409e7a->getId()} delete failed. Error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
                    break;
                }
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearPages';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    protected function _clearBlocks($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61)
    {
        if (!$nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['import']['blocks']) {
            return array(
                'result' => 'process',
                'function' => '_clearTransactions'
            );
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array(
            'result' => 'process'
        );
        $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $this->_objectManager->create('Magento\Cms\Model\Page')
            ->getCollection()
            ->setPageSize($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['clear_info']['limit'])
            ->setCurPage(1);
        if (!count($nht2037de437c80264ccbce8a8b61d0bf9f593d2322)) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'process';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearTransactions';
        } else {
            foreach ($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nht63fede50c6cfb196a43a891e15ec53a181239df6) {
                try {
                    $nht63fede50c6cfb196a43a891e15ec53a181239df6->delete();
                } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = "CMS Block Id = {$nht63fede50c6cfb196a43a891e15ec53a181239df6->getId()} delete failed. Error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
                    break;
                }
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearBlocks';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    protected function _clearTransactions($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61) {
        if (!$nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['import']['transactions']) {
            return array(
                'result' => 'process',
                'function' => '_clearRules'
            );
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array(
            'result' => 'process'
        );
        $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $this->_objectManager->create('Magento\Email\Model\Template')
            ->getCollection()
            ->setPageSize($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['clear_info']['limit'])
            ->setCurPage(1);
        if (!count($nht2037de437c80264ccbce8a8b61d0bf9f593d2322)) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'process';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearRules';
        } else {
            foreach ($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nht41c48b55fa9164e123cc73b1157459e840be5d24) {
                try {
                    $nht41c48b55fa9164e123cc73b1157459e840be5d24->delete();
                } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = "Transaction Id = {$nht41c48b55fa9164e123cc73b1157459e840be5d24->getId()} delete failed. Error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
                    break;
                }
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearTransactions';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    protected function _clearRules($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61) {
        if (!$nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['import']['rules']) {
            return array(
                'result' => 'process',
                'function' => '_clearCartrules'
            );
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array(
            'result' => 'process'
        );
        $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $this->_objectManager->create('Magento\SalesRule\Model\Rule')
            ->getCollection()
            ->setPageSize($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['clear_info']['limit'])
            ->setCurPage(1);
        if (!count($nht2037de437c80264ccbce8a8b61d0bf9f593d2322)) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'process';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearCartrules';
        } else {
            foreach ($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nht0f400122e37da3a462df347caac2be31d74be730) {
                try {
                    $nht0f400122e37da3a462df347caac2be31d74be730->delete();
                } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = "Rule Id = {$nht0f400122e37da3a462df347caac2be31d74be730->getId()} delete failed. Error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
                    break;
                }
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearRules';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    protected function _clearCartrules($nht8bfb4e1aa590eab8f08f837b97acf5803a5737ed, $nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61) {
        if (!$nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['config']['import']['cartrules']) {
            return array(
                'result' => 'success',
                'function' => ''
            );
        }
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array(
            'result' => 'process'
        );
        $nht2037de437c80264ccbce8a8b61d0bf9f593d2322 = $this->_objectManager->create('Magento\CatalogRule\Model\Rule')
            ->getCollection()
            ->setPageSize($nhta81b09d9521597ebcaf0cc8b49ad7a8be8e4cc61['clear_info']['limit'])
            ->setCurPage(1);
        if (!count($nht2037de437c80264ccbce8a8b61d0bf9f593d2322)) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '';
        } else {
            foreach ($nht2037de437c80264ccbce8a8b61d0bf9f593d2322 as $nht0f400122e37da3a462df347caac2be31d74be730) {
                try {
                    $nht0f400122e37da3a462df347caac2be31d74be730->delete();
                } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = "CartRule Id = {$nht0f400122e37da3a462df347caac2be31d74be730->getId()} delete failed. Error: " . $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
                    break;
                }
            }
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['function'] = '_clearCartrules';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Import tax rule
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of class Mage_Tax_Model_Calculation_Rule
     * @return array
     */
    public function taxRule($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd)
    {
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        $nht8b05752770a262f75f5349f5ad48d4af432b1670 = $this->_objectManager->create('Magento\Tax\Model\Calculation\Rule');
        $nht8b05752770a262f75f5349f5ad48d4af432b1670->addData($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        $nht8b05752770a262f75f5349f5ad48d4af432b1670->setTaxRateIds($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['tax_rate_ids']);
        try {
            $nht8b05752770a262f75f5349f5ad48d4af432b1670->save();
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nht8b05752770a262f75f5349f5ad48d4af432b1670->getId();
        } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Import tax customer
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of class Mage_Tax_Model_Class
     * @return array
     */
    public function taxCustomer($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd)
    {
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        $nht36fc816cd2f44cb03610648f0ab461456a7c0ea6 = $this->_objectManager->create('Magento\Tax\Model\ClassModel')
            ->getCollection()
            ->setClassTypeFilter(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER)
            ->addFieldToFilter('class_name', $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['class_name'])
            ->getFirstItem();
        if ($nht36fc816cd2f44cb03610648f0ab461456a7c0ea6->getId()) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nht36fc816cd2f44cb03610648f0ab461456a7c0ea6->getId();
        } else {
            $nhtb834c45519151ed240b91539e7d8506b0cbf7a2d = $this->_objectManager->create('Magento\Tax\Model\ClassModel');
            $nhtb834c45519151ed240b91539e7d8506b0cbf7a2d->addData($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
            $nhtb834c45519151ed240b91539e7d8506b0cbf7a2d->setClassType(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_CUSTOMER);
            try {
                $nhtb834c45519151ed240b91539e7d8506b0cbf7a2d->save();
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nhtb834c45519151ed240b91539e7d8506b0cbf7a2d->getId();
            } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
            }
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Import tax product
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of class Mage_Tax_Model_Class
     * @return array
     */
    public function taxProduct($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd)
    {
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        $nhtab659b3f97b1df9c7a78d8ab47e9215d03b7295c = $this->_objectManager->create('Magento\Tax\Model\ClassModel')
            ->getCollection()
            ->setClassTypeFilter(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT)
            ->addFieldToFilter('class_name', $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['class_name'])
            ->getFirstItem();
        if ($nhtab659b3f97b1df9c7a78d8ab47e9215d03b7295c->getId()) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nhtab659b3f97b1df9c7a78d8ab47e9215d03b7295c->getId();
        } else {
            $nhtcb0592fffabd6fbca74710c4ff88d141d12429bb = $this->_objectManager->create('Magento\Tax\Model\ClassModel');
            $nhtcb0592fffabd6fbca74710c4ff88d141d12429bb->addData($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
            $nhtcb0592fffabd6fbca74710c4ff88d141d12429bb->setClassType(\Magento\Tax\Model\ClassModel::TAX_CLASS_TYPE_PRODUCT);
            try {
                $nhtcb0592fffabd6fbca74710c4ff88d141d12429bb->save();
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nhtcb0592fffabd6fbca74710c4ff88d141d12429bb->getId();
            } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
            }
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Import tax rate
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of class Mage_Tax_Model_Calculation_Rate
     * @return array
     */
    public function taxRate($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd)
    {
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        $nhtf368106e32f619b0f32b15c2141366210085f64a = $this->_objectManager->create('Magento\Tax\Model\Calculation\Rate')->getCollection()
            ->addFieldToFilter('code', $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['code'])
            ->getFirstItem();
        if ($nhtf368106e32f619b0f32b15c2141366210085f64a->getId()) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nhtf368106e32f619b0f32b15c2141366210085f64a->getId();
        } else {
            $nht4237e989ae2a27e5082a19095d45a13671f20b25 = $this->_objectManager->create('Magento\Tax\Model\Calculation\Rate');
            $nht4237e989ae2a27e5082a19095d45a13671f20b25->addData($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
            try {
                $nht4237e989ae2a27e5082a19095d45a13671f20b25->save();
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nht4237e989ae2a27e5082a19095d45a13671f20b25->getId();
            } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
            }
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Import manufacturer option
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of class Mage_Eav_Model_Entity_Attribute_Option
     * @param int $nhtf3172007d4de5ae8e7692759d79f67f5558242ed
     * @return array
     */
    public function manufacturer($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed = 0)
    {
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        $nht7841101cde678795310a7fbcc47cbb64ce9e49b7 = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['value']['option'][$nhtf3172007d4de5ae8e7692759d79f67f5558242ed];
        $nht80437a44a661d141174209119d54125a59a64b2a = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute')->loadByCode('catalog_product', \LitExtension\CartImport\Model\Cart::MANUFACTURER_CODE);
        $nhtee98105465d1d046d440358b445b8dec3d3dc1ed = $nht80437a44a661d141174209119d54125a59a64b2a->getId();
        $nht2c1a872e529b6e9f4d5ab824686e08a336ff578c = $this->_checkAndGetOptionValue($nhtee98105465d1d046d440358b445b8dec3d3dc1ed, $nht7841101cde678795310a7fbcc47cbb64ce9e49b7, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
        if ($nht2c1a872e529b6e9f4d5ab824686e08a336ff578c['result'] == 'success') {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nht2c1a872e529b6e9f4d5ab824686e08a336ff578c['mage_id'];
        } else {
            try {
                $nht80437a44a661d141174209119d54125a59a64b2a->setOption($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
                $nht80437a44a661d141174209119d54125a59a64b2a->save();
                $nht14eb14ece52df99c284b819d9f8092e50aa1613e = $this->_checkAndGetOptionValue($nhtee98105465d1d046d440358b445b8dec3d3dc1ed, $nht7841101cde678795310a7fbcc47cbb64ce9e49b7, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
                if ($nht14eb14ece52df99c284b819d9f8092e50aa1613e['result'] == 'success') {
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nht14eb14ece52df99c284b819d9f8092e50aa1613e['mage_id'];
                } else {
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                    $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = 'Cannot import option value!';
                }
            } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
            }
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    public function cms($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd)
    {
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        $nht8278799d7ce2526fe88953043a6c2055cfd220ae = $this->_objectManager->create('Magento\Cms\Model\Page');
        $nht8278799d7ce2526fe88953043a6c2055cfd220ae->addData($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        try {
            $nht8278799d7ce2526fe88953043a6c2055cfd220ae->save();
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nht8278799d7ce2526fe88953043a6c2055cfd220ae->getId();
        } catch (LitExtension_CartImport_Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
        } catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f) {
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Check manufacturer option exists
     */
    protected function _checkAndGetOptionValue($nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b, $nht7841101cde678795310a7fbcc47cbb64ce9e49b7, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed){
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        $nhtd56d985300d4b52eb6e189be006f44f8d23c5ec9 = false;
        $nht513f8de9259fe7658fe14d1352c54ccf070e911f = $this->getAllAttributeOptions($nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
        foreach($nht513f8de9259fe7658fe14d1352c54ccf070e911f as $nht14eb14ece52df99c284b819d9f8092e50aa1613e){
            if($nht14eb14ece52df99c284b819d9f8092e50aa1613e['value'] == $nht7841101cde678795310a7fbcc47cbb64ce9e49b7){
                $nhtd56d985300d4b52eb6e189be006f44f8d23c5ec9 = true;
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
                $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nht14eb14ece52df99c284b819d9f8092e50aa1613e['option_id'];
                break;
            }
        }
        if($nhtd56d985300d4b52eb6e189be006f44f8d23c5ec9 == false){
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Import Category
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of class Mage_Catalog_Model_Category
     * @return array
     */
    public function category($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd){
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = $nhtd38ad05b0d82286437cf817ce5d33d538209cfcb = $nht86cc8834fe7939c6a9010b2255b021b44b314fa1 = array();
        if(isset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['multi_store'])){
            $nhtd38ad05b0d82286437cf817ce5d33d538209cfcb = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['multi_store'];
            unset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['multi_store']);
        }
        if(isset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['seo_url'])){
            $nht86cc8834fe7939c6a9010b2255b021b44b314fa1 = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['seo_url'];
            unset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['seo_url']);
        }
        $_categories = $this->_objectManager->create('Magento\Catalog\Model\Category');
        $_categories->addData($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        $_categories->setStoreId(0);
        try{
            $_categories->save();
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
            $nht9c4e4a89e3674fc9f382d733f03d24746bdc9d9a = $_categories->getId();
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nht9c4e4a89e3674fc9f382d733f03d24746bdc9d9a;
            if($nht86cc8834fe7939c6a9010b2255b021b44b314fa1){
                foreach($nht86cc8834fe7939c6a9010b2255b021b44b314fa1 as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de => $nht81736358b1645103ae83247b10c5f82af641ddfc){
                    $nht180567f97f5c6745c6e3f1382eda73b8efacfb46 = $this->_objectManager->create('Magento\UrlRewrite\Model\UrlRewrite');
                    $nht180567f97f5c6745c6e3f1382eda73b8efacfb46->addData($nht81736358b1645103ae83247b10c5f82af641ddfc);
                    $nht180567f97f5c6745c6e3f1382eda73b8efacfb46
                        ->setEntityId($nht9c4e4a89e3674fc9f382d733f03d24746bdc9d9a)
                        ->setEntityType('category')
                        ->setRedirectType(0)
                        ->setDescription('cm_category')
                        ->setTargetPath('catalog/category/view/id/'.$nht9c4e4a89e3674fc9f382d733f03d24746bdc9d9a);
                    try{
                        $nht180567f97f5c6745c6e3f1382eda73b8efacfb46->save();
                    } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
                        // do nothing
                    }
                }
            }
        } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
        }
        if($nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] == 'success' && $nhtd38ad05b0d82286437cf817ce5d33d538209cfcb && !empty($nhtd38ad05b0d82286437cf817ce5d33d538209cfcb)){
            foreach($nhtd38ad05b0d82286437cf817ce5d33d538209cfcb as $nht37fb9a74112551f93901f7e9fe68754acca39bbf){
                $_category = $this->_objectManager->create('Magento\Catalog\Model\Category')->setStoreId(0)->load($nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id']);
                try{
                    $_category->addData($nht37fb9a74112551f93901f7e9fe68754acca39bbf);
                    $_category->setStoreId($nht37fb9a74112551f93901f7e9fe68754acca39bbf['store_id']);
                    $_category->save();
                } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
                    // do nothing
                }

            }
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Import product
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd
     * @return array
     */
    public function product($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd){
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = $nht262b79d431ba9bd1def0b0033a23f3afb8d5b746 = $nhtd4db9b758636e66f50753c9a99f83bc21256232f = $nhtde6012586f899a5acad90793c850f72cb5061391 = $nht86cc8834fe7939c6a9010b2255b021b44b314fa1 = $nhtbfcd35fa0b6f07ee29a78a2f00837b3a1ccc146d = array();
        if(isset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['multi_store'])){
            $nht262b79d431ba9bd1def0b0033a23f3afb8d5b746 = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['multi_store'];
            unset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['multi_store']);
        }
        if(isset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['image_import_path'])){
            $nhtd4db9b758636e66f50753c9a99f83bc21256232f = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['image_import_path'];
            unset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['image_import_path']);
        }
        if(isset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['image_gallery'])){
            $nhtde6012586f899a5acad90793c850f72cb5061391 = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['image_gallery'];
            unset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['image_gallery']);
        }
        if(isset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['seo_url'])){
            $nht86cc8834fe7939c6a9010b2255b021b44b314fa1 = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['seo_url'];
            unset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['seo_url']);
        }
        if(isset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['add_data'])) {
            $nhtbfcd35fa0b6f07ee29a78a2f00837b3a1ccc146d = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['add_data'];
            unset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['add_data']);
        }
        try{
            $_product = $this->_objectManager->create('Magento\Catalog\Model\Product');
            $_product->addData($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
            if($nhtd4db9b758636e66f50753c9a99f83bc21256232f && isset($nhtd4db9b758636e66f50753c9a99f83bc21256232f['path']) && file_exists($nhtd4db9b758636e66f50753c9a99f83bc21256232f['path'])){
                $_product->addImageToMediaGallery(substr($nhtd4db9b758636e66f50753c9a99f83bc21256232f['path'], strpos($nhtd4db9b758636e66f50753c9a99f83bc21256232f['path'], 'catalog')) ,array('thumbnail', 'small_image', 'image'), true, false, $nhtd4db9b758636e66f50753c9a99f83bc21256232f['label']);
            }
            if($nhtde6012586f899a5acad90793c850f72cb5061391){
                foreach($nhtde6012586f899a5acad90793c850f72cb5061391 as $nht5b47b90353dce961408d7319555f7cb9ca62fd7f){
                    if(file_exists($nht5b47b90353dce961408d7319555f7cb9ca62fd7f['path'])){
                        $_product->addImageToMediaGallery(substr($nht5b47b90353dce961408d7319555f7cb9ca62fd7f['path'], strpos($nht5b47b90353dce961408d7319555f7cb9ca62fd7f['path'], 'catalog')), array(), true, false, $nht5b47b90353dce961408d7319555f7cb9ca62fd7f['label']);
                    }
                }
            }
            $_product->setStoreId(0);
            $_product->save();
            $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5 = $_product->getId();
            if ($nhtbfcd35fa0b6f07ee29a78a2f00837b3a1ccc146d) {
                $nht814605c64a5c1c3c2d3c2c332153f0e425e92653 = $this->getConnection();
                foreach ($nhtbfcd35fa0b6f07ee29a78a2f00837b3a1ccc146d as $nht4b4faf6d20325fb224ce7559f5d7601e0437bca3 => $nht89e507103fe09d455ce563a311231231ea1f13ab) {
                    $nhtc3ee137d4f22eb06ed1351d644f3674592c90836 = $this->_resource->getTableName('catalog_product_entity_' . $nht89e507103fe09d455ce563a311231231ea1f13ab['backend_type']);
                    $nht11148d990cf78361c4ca35640c4de225d02f18f8 = [
                        'attribute_id' => $nht4b4faf6d20325fb224ce7559f5d7601e0437bca3,
                        'store_id' => 0,
                        'entity_id' => $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5,
                        'value' => $nht89e507103fe09d455ce563a311231231ea1f13ab['value']
                    ];
                    $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd = ['entity_id =?' => $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5, 'attribute_id =?' => $nht4b4faf6d20325fb224ce7559f5d7601e0437bca3];
                    $nht814605c64a5c1c3c2d3c2c332153f0e425e92653->delete($nhtc3ee137d4f22eb06ed1351d644f3674592c90836, $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd);
                    $nht814605c64a5c1c3c2d3c2c332153f0e425e92653->insert($nhtc3ee137d4f22eb06ed1351d644f3674592c90836, $nht11148d990cf78361c4ca35640c4de225d02f18f8);
                }
            }
            if($nht86cc8834fe7939c6a9010b2255b021b44b314fa1){
                foreach($nht86cc8834fe7939c6a9010b2255b021b44b314fa1 as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de => $nht81736358b1645103ae83247b10c5f82af641ddfc){
                    $nht180567f97f5c6745c6e3f1382eda73b8efacfb46 = $this->_objectManager->create('Magento\UrlRewrite\Model\UrlRewrite');
                    $nht180567f97f5c6745c6e3f1382eda73b8efacfb46->addData($nht81736358b1645103ae83247b10c5f82af641ddfc);
                    $nht180567f97f5c6745c6e3f1382eda73b8efacfb46
                        ->setEntityId($nhtbebc9158e480b949565b4dc7a82d05cfd99935d5)
                        ->setEntityType('product')
                        ->setRedirectType(0)
                        ->setDescription('cm_product')
                        ->setTargetPath('catalog/product/view/id/'.$nhtbebc9158e480b949565b4dc7a82d05cfd99935d5);
                    try{
                        $nht180567f97f5c6745c6e3f1382eda73b8efacfb46->save();
                    } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
                        // do nothing
                    }
                }
            }
            if ($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['type_id'] == 'bundle') {

            }

            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5;
        } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
        }
        if($nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] == 'success' && $nht262b79d431ba9bd1def0b0033a23f3afb8d5b746 && !empty($nht262b79d431ba9bd1def0b0033a23f3afb8d5b746)){
            foreach($nht262b79d431ba9bd1def0b0033a23f3afb8d5b746 as $nht37fb9a74112551f93901f7e9fe68754acca39bbf){
                try{
                    $nht38a007151abe87cc01a5b6e9cc418e85286e2087 = $this->_objectManager->create('Magento\Catalog\Model\Product')->setStoreId(0)->load($nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id']);
                    $nht38a007151abe87cc01a5b6e9cc418e85286e2087->addData($nht37fb9a74112551f93901f7e9fe68754acca39bbf);
                    $nht38a007151abe87cc01a5b6e9cc418e85286e2087->setStoreId($nht37fb9a74112551f93901f7e9fe68754acca39bbf['store_id']);
                    $nht38a007151abe87cc01a5b6e9cc418e85286e2087->save();
                } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
                    //do nothing
                }
            }
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Import customer
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of class Mage_Customer_Model_Customer
     * @return array
     */
    public function customer($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd){
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        try{
            $nhtbaf5c05864d34dca9be5e1f320d01a6b96c27793 = false;
            if(isset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['id'])){
                $nhtbaf5c05864d34dca9be5e1f320d01a6b96c27793 = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['id'];
                unset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['id']);
            }
            $nhtb39f008e318efd2bb988d724a161b61c6909677f = $this->_objectManager->create('Magento\Customer\Model\Customer');
            if($nhtbaf5c05864d34dca9be5e1f320d01a6b96c27793){
                $nhtb39f008e318efd2bb988d724a161b61c6909677f->setId($nhtbaf5c05864d34dca9be5e1f320d01a6b96c27793);
            }
            $nhtb39f008e318efd2bb988d724a161b61c6909677f->addData($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
            $nhtb39f008e318efd2bb988d724a161b61c6909677f->setPasswordHash(null);
            $nhtb39f008e318efd2bb988d724a161b61c6909677f->setConfirmation(null);
            $nhtb39f008e318efd2bb988d724a161b61c6909677f->save();
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nhtb39f008e318efd2bb988d724a161b61c6909677f->getId();
        } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Import customer
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd
     * @param boolean $nht384ebdfbaeaf93c799d4b3cd49a9e7e2288096f6
     * @return array
     */
    public function order($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht384ebdfbaeaf93c799d4b3cd49a9e7e2288096f6){
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        $nht5ae5e326f5697bbc776ecd24a81074b5646d7723 = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['address_billing'];
        $nht6be600a29d57d37cac4d5fb17b67b7668677171a = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['address_shipping'];
        $nht17994471a9a7a6fdf0818b65dae3008512bde344 = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['carts'];
        $nhtb54e3b580ece7c8a8180d41aeadbf4b3ac713dfe = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['order'];
        if(isset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['payment'])){
            $nht8186262c3712aa1ef4ad9ab511e6948ac3a01fa1 = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['payment'];
        }
        $nht27aba40d1cf929b99907af8f69e276a46eda7893 = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['order_src_id'];
        $nhtf3172007d4de5ae8e7692759d79f67f5558242ed = $nhtb54e3b580ece7c8a8180d41aeadbf4b3ac713dfe['store_id'];
        try{
            $order = $this->_objectManager->create('Magento\Sales\Model\Order')
                ->setStoreId($nhtf3172007d4de5ae8e7692759d79f67f5558242ed)
                ->setQuoteId(0)
                ->addData($nhtb54e3b580ece7c8a8180d41aeadbf4b3ac713dfe);
            if($nht384ebdfbaeaf93c799d4b3cd49a9e7e2288096f6){
                $nht4815e7e0e84194823b9519b0730cf301d21987de = $nht27aba40d1cf929b99907af8f69e276a46eda7893;
                $order->setIncrementId($nht4815e7e0e84194823b9519b0730cf301d21987de);
            } else {
                //$nht4815e7e0e84194823b9519b0730cf301d21987de = $this->_objectManager->get('Magento\Eav\Model\Config')->getEntityType('order')->fetchNewIncrementId($nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
            }
            $order->setOldId($nht27aba40d1cf929b99907af8f69e276a46eda7893);
            $nhtc411c0eb684da54c19cb3463972cdc5b5fe15629 = $this->_objectManager->create('Magento\Sales\Model\Order\Address')->addData($nht5ae5e326f5697bbc776ecd24a81074b5646d7723);
            $nht77c2ea483e3b9ef7134bf9e9e4105c1e78688fd8 = $this->_objectManager->create('Magento\Sales\Model\Order\Address')->addData($nht6be600a29d57d37cac4d5fb17b67b7668677171a);
            $order->setBillingAddress($nhtc411c0eb684da54c19cb3463972cdc5b5fe15629);
            $order->setShippingAddress($nht77c2ea483e3b9ef7134bf9e9e4105c1e78688fd8)
                ->setShippingMethod('flatrate_flatrate');
            if (!isset($nht8186262c3712aa1ef4ad9ab511e6948ac3a01fa1['method']) || !$nht8186262c3712aa1ef4ad9ab511e6948ac3a01fa1['method']) {
                $nht56cdacd198300ea1e7deb9ddce95379e03cd674d = $this->_objectManager->create('Magento\Sales\Model\Order\Payment')
                    ->setMethod('checkmo');
                $order->setPayment($nht56cdacd198300ea1e7deb9ddce95379e03cd674d);
            } else {
                $nhtb481ca91cec8d3a38f9f76b636905d08d110723c = $this->_scopeConfig->getValue('payment');
                $nht56cdacd198300ea1e7deb9ddce95379e03cd674d = $this->_objectManager->create('Magento\Sales\Model\Order\Payment');
                if (isset($nhtb481ca91cec8d3a38f9f76b636905d08d110723c[$nht8186262c3712aa1ef4ad9ab511e6948ac3a01fa1['method']])) {
                    $nht3dd360ff8933dff1709504ee8e44e9ce15d264e0 = $nht56cdacd198300ea1e7deb9ddce95379e03cd674d->setData($nht8186262c3712aa1ef4ad9ab511e6948ac3a01fa1);
                } else {
                    $nht3dd360ff8933dff1709504ee8e44e9ce15d264e0 = $nht56cdacd198300ea1e7deb9ddce95379e03cd674d->setMethod('checkmo');
                }
                $order->setPayment($nht3dd360ff8933dff1709504ee8e44e9ce15d264e0);
            }
            foreach ($nht17994471a9a7a6fdf0818b65dae3008512bde344 as $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af) {
                /*if(isset($nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af['product_id'])){
                    if($this->_scopeConfig->getValue('lecamg/general/report')){
                        $this->_addProductIsView($nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af['product_id'], $nhtb54e3b580ece7c8a8180d41aeadbf4b3ac713dfe['customer_id']);
                    }
                    if($this->_scopeConfig->getValue('lecamg/general/bestseller')){
                        $this->_addProductIsBestseller($nhtb54e3b580ece7c8a8180d41aeadbf4b3ac713dfe['created_at'], $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af);
                    }
                }*/
                $nht4c623b7ae420208a9443ff9eb9184984633800e8 = $this->_objectManager->create('Magento\Sales\Model\Order\Item')
                    ->setStoreId($nhtf3172007d4de5ae8e7692759d79f67f5558242ed)
                    ->setQuoteItemId(0)
                    ->setQuoteParentItemId(NULL);
                $nht4c623b7ae420208a9443ff9eb9184984633800e8->addData($nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af);
                if (isset($nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af['children_item'])) {
                    $nht4fb426105a5b177e63a7382d43276a2d95bb15e6 = array();
                    foreach ($nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af['children_item'] as $nht523894b9c29d6adeca1f71abc4ad48415b44b4c0) {
                        $nht96669f699f3ef98fdd375f96eafd3849e65c3607 = $this->_objectManager->create('Magento\Sales\Model\Order\Item')
                            ->setStoreId($nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
                        $nht96669f699f3ef98fdd375f96eafd3849e65c3607->addData($nht523894b9c29d6adeca1f71abc4ad48415b44b4c0);
                        $nht96669f699f3ef98fdd375f96eafd3849e65c3607->setParentItem($nht4c623b7ae420208a9443ff9eb9184984633800e8);
                        $nht4fb426105a5b177e63a7382d43276a2d95bb15e6[] = $nht96669f699f3ef98fdd375f96eafd3849e65c3607;
                    }
                }
                $order->addItem($nht4c623b7ae420208a9443ff9eb9184984633800e8);
                if (isset($nht4fb426105a5b177e63a7382d43276a2d95bb15e6)) {
                    foreach ($nht4fb426105a5b177e63a7382d43276a2d95bb15e6 as $nht0e93069c40111cd62dac2cd02cd71daffdb01cc0) {
                        $order->addItem($nht0e93069c40111cd62dac2cd02cd71daffdb01cc0);
                    }
                }
            }
            //$order->addData($nhtb54e3b580ece7c8a8180d41aeadbf4b3ac713dfe);
            $order->save();
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $order->getId();
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    /**
     * Import review
     *
     * @param array $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd : Data of class Mage_Review_Model_Review
     * @param array $nhtc69ea0b91488ac352e8a8975a17d4f2d58c6d08c
     * @return array
     */
    public function review($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhtc69ea0b91488ac352e8a8975a17d4f2d58c6d08c){
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = $nht3c2d687ef032e625aa4a2b1cfca9751d2080322c = array();
        if(isset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['rating'])){
            $nht3c2d687ef032e625aa4a2b1cfca9751d2080322c = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['rating'];
            unset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['rating']);
        }
        $nhtfbc018679316956d734c87c070172fc19bdc63f8 = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['created_at'];
        $_review = $this->_objectManager->create('Magento\Review\Model\Review');
        $_review->addData($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        try{
            $_review->save();
            $_review->setCreatedAt($nhtfbc018679316956d734c87c070172fc19bdc63f8);
            $_review->save();
            $nht2e4299a343a81574217255c00cac917e19d295b8 = $_review->getId();
            if($nht3c2d687ef032e625aa4a2b1cfca9751d2080322c && !is_array($nht3c2d687ef032e625aa4a2b1cfca9751d2080322c)){
                foreach($nhtc69ea0b91488ac352e8a8975a17d4f2d58c6d08c as $nhtecda8ad32645327e4765b43649eb6b9720c8eab8 => $nht012f589f25cf6f788e77b0eb44a5c80330c1901e){
                    $_rating = $this->_objectManager->create('Magento\Review\Model\Rating')
                        ->setRatingId($nhtecda8ad32645327e4765b43649eb6b9720c8eab8)
                        ->setReviewId($nht2e4299a343a81574217255c00cac917e19d295b8)
                        ->addOptionVote($nht012f589f25cf6f788e77b0eb44a5c80330c1901e[$nht3c2d687ef032e625aa4a2b1cfca9751d2080322c -1],$nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['entity_pk_value']);
                }
            } else {
                foreach ($nhtc69ea0b91488ac352e8a8975a17d4f2d58c6d08c as $nhtecda8ad32645327e4765b43649eb6b9720c8eab8 => $nht012f589f25cf6f788e77b0eb44a5c80330c1901e) {
                    if (!isset($nht3c2d687ef032e625aa4a2b1cfca9751d2080322c[$nhtecda8ad32645327e4765b43649eb6b9720c8eab8])) {
                        continue;
                    }
                    $_rating = $this->_objectManager->create('Magento\Review\Model\Rating')
                        ->setRatingId($nhtecda8ad32645327e4765b43649eb6b9720c8eab8)
                        ->setReviewId($nht2e4299a343a81574217255c00cac917e19d295b8)
                        ->addOptionVote($nht3c2d687ef032e625aa4a2b1cfca9751d2080322c[$nhtecda8ad32645327e4765b43649eb6b9720c8eab8], $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['entity_pk_value']);
                }
            }
            $_review->aggregate();
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nht2e4299a343a81574217255c00cac917e19d295b8;
        } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    // extend function

    /**
     * Import attribute
     */
    public function attribute($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nht988edb198250bc72ad4bc9dd330c87ef4ac96e81 = array(), $nht55bab791a2b3ade48e6d4d6f7b3936537156117e = true, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed = 0){
        $nht0d000c0954b5da49bf9b3f424b31de49e11dfa64 = array();
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = $this->_prepareAttributeData($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        if(!$nht55bab791a2b3ade48e6d4d6f7b3936537156117e){
            $nhte78fe7049341b36116d8054f5a3e00d01f245fcc = $this->_checkAttributeSync($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        } else {
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = $this->_validAttrCodeReserve($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = $this->_createAttributeCodeIfInvalid($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
            $nhte78fe7049341b36116d8054f5a3e00d01f245fcc = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['check'];
            unset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['check']);
        }

        $nht82955cac791c06ffad52527556612268c17baa1f = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute');
        if($nhte78fe7049341b36116d8054f5a3e00d01f245fcc == 'valid'){
            $nhta334ea0f893c9121b29f2f0360c09b26b829b6a6 = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd;
            unset($nhta334ea0f893c9121b29f2f0360c09b26b829b6a6['option']);
            $nht14eb14ece52df99c284b819d9f8092e50aa1613e = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['option'];
            $nht82955cac791c06ffad52527556612268c17baa1f->addData($nhta334ea0f893c9121b29f2f0360c09b26b829b6a6);
            if ($nht14eb14ece52df99c284b819d9f8092e50aa1613e['value']) {
                $nht14eb14ece52df99c284b819d9f8092e50aa1613e = $this->_uniqueOptionValue($nht14eb14ece52df99c284b819d9f8092e50aa1613e, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
                $nht82955cac791c06ffad52527556612268c17baa1f->setOption($nht14eb14ece52df99c284b819d9f8092e50aa1613e);
            }
        } else if($nhte78fe7049341b36116d8054f5a3e00d01f245fcc == 'sync'){
            $nht82955cac791c06ffad52527556612268c17baa1f->loadByCode($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['entity_type_id'], $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_code']);
            if($nht988edb198250bc72ad4bc9dd330c87ef4ac96e81 && !empty($nht988edb198250bc72ad4bc9dd330c87ef4ac96e81)){
                $nht82955cac791c06ffad52527556612268c17baa1f->addData($nht988edb198250bc72ad4bc9dd330c87ef4ac96e81);
            }
            if($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['option']){
                $nht4b4faf6d20325fb224ce7559f5d7601e0437bca3 = $nht82955cac791c06ffad52527556612268c17baa1f->getId();
                $nht14eb14ece52df99c284b819d9f8092e50aa1613e = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['option'];
                if ($nht14eb14ece52df99c284b819d9f8092e50aa1613e['value']) {
                    $nht14eb14ece52df99c284b819d9f8092e50aa1613e = $this->_uniqueOptionValue($nht14eb14ece52df99c284b819d9f8092e50aa1613e, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
                    $nht14eb14ece52df99c284b819d9f8092e50aa1613e = $this->_duplicateAttributeOptionExists($nht4b4faf6d20325fb224ce7559f5d7601e0437bca3, $nht14eb14ece52df99c284b819d9f8092e50aa1613e, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
                    $nht82955cac791c06ffad52527556612268c17baa1f->setOption($nht14eb14ece52df99c284b819d9f8092e50aa1613e);
                }
            }
        } else {
            return false;
        }
        try{
            $nht82955cac791c06ffad52527556612268c17baa1f->save();
            $nht4b4faf6d20325fb224ce7559f5d7601e0437bca3 = $nht82955cac791c06ffad52527556612268c17baa1f->getId();
            $nhtd6703e4ff57a5eb55f2538645d80eeef6765b770 = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')->getResource()->getSetInfo(array($nht4b4faf6d20325fb224ce7559f5d7601e0437bca3));
            $nht0d000c0954b5da49bf9b3f424b31de49e11dfa64 = array_keys($nhtd6703e4ff57a5eb55f2538645d80eeef6765b770[$nht4b4faf6d20325fb224ce7559f5d7601e0437bca3]);
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            return false;
        }
        if($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_set_id'] && !is_array($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_set_id'])){
            if (!in_array($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_set_id'], $nht0d000c0954b5da49bf9b3f424b31de49e11dfa64)) {
                try{
                    $nht75f9bdcc9115d70b3b0e6e0cc17de52ec085cc87 = $this->_objectManager->create('Magento\Eav\Model\Entity')->setType(\Magento\Catalog\Model\Product::ENTITY)->getTypeId();
                    $nhtf33be25f11a4a054e8b0f193b1907e18b9ade483 = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')
                        ->setEntityTypeId($nht75f9bdcc9115d70b3b0e6e0cc17de52ec085cc87)->load($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_set_id'])->getDefaultGroupId();
                    /*$nht80437a44a661d141174209119d54125a59a64b2a = $this->_objectManager->create('Magento\Catalog\Model\Product\Attribute\Management');
                    $nht80437a44a661d141174209119d54125a59a64b2a->assign($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_set_id'], $nhtf33be25f11a4a054e8b0f193b1907e18b9ade483, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_code'], 1000);*/
                    $nht814605c64a5c1c3c2d3c2c332153f0e425e92653 = $this->getConnection();
                    $nhtc3ee137d4f22eb06ed1351d644f3674592c90836 = $this->_resource->getTableName('eav_entity_attribute');
                    $nht76f747de912e8682e29a23cb506dd5bf0de080d2 = [
                        'entity_type_id' => $nht75f9bdcc9115d70b3b0e6e0cc17de52ec085cc87,
                        'attribute_set_id' => $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_set_id'],
                        'attribute_group_id' => $nhtf33be25f11a4a054e8b0f193b1907e18b9ade483,
                        'attribute_id' => $nht4b4faf6d20325fb224ce7559f5d7601e0437bca3,
                        'sort_order' => 1000,
                    ];
                    $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd = ['attribute_id =?' => $nht4b4faf6d20325fb224ce7559f5d7601e0437bca3, 'attribute_set_id =?' => $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_set_id']];
                    $nht814605c64a5c1c3c2d3c2c332153f0e425e92653->delete($nhtc3ee137d4f22eb06ed1351d644f3674592c90836, $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd);
                    $nht814605c64a5c1c3c2d3c2c332153f0e425e92653->insert($nhtc3ee137d4f22eb06ed1351d644f3674592c90836, $nht76f747de912e8682e29a23cb506dd5bf0de080d2);
                }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){}
            }
        }
        if($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_set_id'] && is_array($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_set_id'])){
            //$nht80437a44a661d141174209119d54125a59a64b2a = $this->_objectManager->create('Magento\Catalog\Model\Product\Attribute\Management');
            foreach ($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_set_id'] as $nhtdb5de993943fd8c01a124b3118fea1d9cd2f254a) {
                if (!in_array($nhtdb5de993943fd8c01a124b3118fea1d9cd2f254a, $nht0d000c0954b5da49bf9b3f424b31de49e11dfa64)) {
                    try{
                        $nht75f9bdcc9115d70b3b0e6e0cc17de52ec085cc87 = $this->_objectManager->create('Magento\Eav\Model\Entity')->setType(\Magento\Catalog\Model\Product::ENTITY)->getTypeId();
                        $nhtf33be25f11a4a054e8b0f193b1907e18b9ade483 = $this->_objectManager->create('Magento\Eav\Model\Entity\Attribute\Set')
                            ->setEntityTypeId($nht75f9bdcc9115d70b3b0e6e0cc17de52ec085cc87)->load($nhtdb5de993943fd8c01a124b3118fea1d9cd2f254a)->getDefaultGroupId();
                        //$nht80437a44a661d141174209119d54125a59a64b2a->assign($nhtdb5de993943fd8c01a124b3118fea1d9cd2f254a, $nhtf33be25f11a4a054e8b0f193b1907e18b9ade483, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_code'], 1000);
                        $nht814605c64a5c1c3c2d3c2c332153f0e425e92653 = $this->getConnection();
                        $nhtc3ee137d4f22eb06ed1351d644f3674592c90836 = $this->_resource->getTableName('eav_entity_attribute');
                        $nht76f747de912e8682e29a23cb506dd5bf0de080d2 = [
                            'entity_type_id' => $nht75f9bdcc9115d70b3b0e6e0cc17de52ec085cc87,
                            'attribute_set_id' => $nhtdb5de993943fd8c01a124b3118fea1d9cd2f254a,
                            'attribute_group_id' => $nhtf33be25f11a4a054e8b0f193b1907e18b9ade483,
                            'attribute_id' => $nht4b4faf6d20325fb224ce7559f5d7601e0437bca3,
                            'sort_order' => 1000,
                        ];
                        $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd = ['attribute_id =?' => $nht4b4faf6d20325fb224ce7559f5d7601e0437bca3, 'attribute_set_id =?' => $nhtdb5de993943fd8c01a124b3118fea1d9cd2f254a];
                        $nht814605c64a5c1c3c2d3c2c332153f0e425e92653->delete($nhtc3ee137d4f22eb06ed1351d644f3674592c90836, $nht46148cc3b4d2b3ac8073f14b0cba7f25ffff54bd);
                        $nht814605c64a5c1c3c2d3c2c332153f0e425e92653->insert($nhtc3ee137d4f22eb06ed1351d644f3674592c90836, $nht76f747de912e8682e29a23cb506dd5bf0de080d2);
                    }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){}
                }
            }
        }
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['attribute_id'] = $nht4b4faf6d20325fb224ce7559f5d7601e0437bca3;
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['attribute_code'] = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_code'];
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['backend_type'] = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['backend_type'];
        $nht4aee1ec23193d9f61398e437845d337125141e6b = $this->getAttributeOptionValueByListOption($nht4b4faf6d20325fb224ce7559f5d7601e0437bca3, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['option'], $nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68['option_ids'] = $nht4aee1ec23193d9f61398e437845d337125141e6b;
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    protected function _prepareAttributeData($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd){
        $nhte89031171790164da2bf3d0f29db6fe592824365 = array(
            'attribute_set_id'              => null,
            'attribute_group_id'            => null,
            'is_global'                     => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
            'default_value_text'            => '',
            'default_value_yesno'           => false,
            'default_value_date'            => '',
            'default_value_textarea'        => '',
            'is_unique'                     => false,
            'is_required'                   => false,
            'frontend_class'                => '',
            'is_searchable'                 => false,
            'is_visible_in_advanced_search' => false,
            'is_comparable'                 => false,
            'is_filterable'                 => false,
            'is_filterable_in_search'       => false,
            'is_used_for_promo_rules'       => false,
            'is_html_allowed_on_front'      => true,
            'is_visible_on_front'           => false,
            'used_in_product_listing'       => false,
            'used_for_sort_by'              => false,
            'apply_to'                      => null,
            'is_user_defined'               => true,
        );
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array_merge($nhte89031171790164da2bf3d0f29db6fe592824365, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        $nht4e3749c4b2f1417a50a25f415a5e455379df17f2 = $this->_objectManager->get('Magento\Catalog\Helper\Product');
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['source_model'] = $nht4e3749c4b2f1417a50a25f415a5e455379df17f2->getAttributeSourceModelByInputType($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['frontend_input']);
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['backend_model'] = $nht4e3749c4b2f1417a50a25f415a5e455379df17f2->getAttributeBackendModelByInputType($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['frontend_input']);
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['backend_type'] = $this->_getBackendTypeByInput($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['frontend_input']);
        $nht49501ecf40eae6f13a603cb56027bef6e71ae658 = $this->_getDefaultValueByInput($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['frontend_input']);
        if ($nht49501ecf40eae6f13a603cb56027bef6e71ae658) {
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['default_value'] = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd[$nht49501ecf40eae6f13a603cb56027bef6e71ae658];
        }
        return $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd;
    }

    protected function _getBackendTypeByInput($nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9)
    {
        $nht2da0b68df8841752bb747a76780679bcd87c6215 = null;
        switch ($nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9) {
            case 'text':
            case 'gallery':
            case 'media_image':
            case 'multiselect':
                $nht2da0b68df8841752bb747a76780679bcd87c6215 = 'varchar';
                break;

            case 'image':
            case 'textarea':
                $nht2da0b68df8841752bb747a76780679bcd87c6215 = 'text';
                break;

            case 'date':
                $nht2da0b68df8841752bb747a76780679bcd87c6215 = 'datetime';
                break;

            case 'select':
            case 'boolean':
                $nht2da0b68df8841752bb747a76780679bcd87c6215 = 'int';
                break;

            case 'price':
            case 'weight':
                $nht2da0b68df8841752bb747a76780679bcd87c6215 = 'decimal';
                break;

            default:
                break;
        }

        return $nht2da0b68df8841752bb747a76780679bcd87c6215;
    }

    /**
     * Detect default value using frontend input type
     *
     * @param string $nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9 frontend_input field name
     * @return string default_value field value
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    protected function _getDefaultValueByInput($nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9)
    {
        $nht2da0b68df8841752bb747a76780679bcd87c6215 = '';
        switch ($nhtd0a3e7f81a9885e99049d1cae0336d269d5e47a9) {
            case 'select':
            case 'gallery':
            case 'media_image':
                break;
            case 'multiselect':
                $nht2da0b68df8841752bb747a76780679bcd87c6215 = null;
                break;

            case 'text':
            case 'price':
            case 'image':
            case 'weight':
                $nht2da0b68df8841752bb747a76780679bcd87c6215 = 'default_value_text';
                break;

            case 'textarea':
                $nht2da0b68df8841752bb747a76780679bcd87c6215 = 'default_value_textarea';
                break;

            case 'date':
                $nht2da0b68df8841752bb747a76780679bcd87c6215 = 'default_value_date';
                break;

            case 'boolean':
                $nht2da0b68df8841752bb747a76780679bcd87c6215 = 'default_value_yesno';
                break;

            default:
                break;
        }

        return $nht2da0b68df8841752bb747a76780679bcd87c6215;
    }

    protected function _checkAttributeSync($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = 'valid';
        $nht02b49685d9c8ac89842de1a101759192c2f8368f = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['frontend_input'];
        $nht9cae639b7814ed5a609d98e70d12e2914b4ea3be = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['backend_type'];
        $nhtfc88c6b3cc380853de27e44642207df9743ff63d = $this->_objectManager->create('Magento\Eav\Model\Config')
            ->getAttribute(\Magento\Catalog\Model\Product::ENTITY, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_code']);
        if($nhtfc88c6b3cc380853de27e44642207df9743ff63d && $nhtfc88c6b3cc380853de27e44642207df9743ff63d->getId()){
            $nht966c469165bb68d4aae5330eee62ba3c15582630 = $nhtfc88c6b3cc380853de27e44642207df9743ff63d->getFrontendInput();
            $nht3627e42cb0e8571646a80a8aba98ae7fedc09edd = $nhtfc88c6b3cc380853de27e44642207df9743ff63d->getBackendType();
            if($nht966c469165bb68d4aae5330eee62ba3c15582630 == $nht02b49685d9c8ac89842de1a101759192c2f8368f && $nht3627e42cb0e8571646a80a8aba98ae7fedc09edd == $nht9cae639b7814ed5a609d98e70d12e2914b4ea3be){
                $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = 'sync';
            } else{
                $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = 'invalid';
            }
        }
        return $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
    }

    protected function _validAttrCodeReserve($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd){
        $nhte91f32cd87c8e5054ce3950561664c8c48a9afde = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_code'];
        $nhtec87faca4cbad909219bbcea9dbbe370a9f8c690 = 'a';
        $nht29f6bfd89e6d034bc6a20724a0891bc49aa11e5a = $this->_objectManager->create('Magento\Catalog\Model\Product\ReservedAttributeList');
        while($nht29f6bfd89e6d034bc6a20724a0891bc49aa11e5a->isReservedAttribute($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_code'])){
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_code'] = $nhte91f32cd87c8e5054ce3950561664c8c48a9afde . "_" . $nhtec87faca4cbad909219bbcea9dbbe370a9f8c690;
            $nhtec87faca4cbad909219bbcea9dbbe370a9f8c690++;
        }
        return $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd;
    }

    protected function _uniqueOptionValue($nht4aee1ec23193d9f61398e437845d337125141e6b, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed = 0){
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = array();
        $nht58037c0078d5f54e15e638cc0dd882a570b13c50 = array();
        foreach($nht4aee1ec23193d9f61398e437845d337125141e6b['value'] as $nhta62f2225bf70bfaccbc7f1ef2a397836717377de => $nhtf32b67c7e26342af42efabc674d441dca0a281c5){
            if(!in_array($nhtf32b67c7e26342af42efabc674d441dca0a281c5[$nhtf3172007d4de5ae8e7692759d79f67f5558242ed], $nht58037c0078d5f54e15e638cc0dd882a570b13c50)){
                $nht37a5301a88da334dc5afc5b63979daa0f3f45e68[$nhta62f2225bf70bfaccbc7f1ef2a397836717377de] = $nhtf32b67c7e26342af42efabc674d441dca0a281c5;
                $nht58037c0078d5f54e15e638cc0dd882a570b13c50[] = $nhtf32b67c7e26342af42efabc674d441dca0a281c5[$nhtf3172007d4de5ae8e7692759d79f67f5558242ed];
            }
        }
        return array('value' => $nht37a5301a88da334dc5afc5b63979daa0f3f45e68);
    }

    protected function _createAttributeCodeIfInvalid($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd){
        $nhte91f32cd87c8e5054ce3950561664c8c48a9afde = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_code'];
        $nhtec87faca4cbad909219bbcea9dbbe370a9f8c690 = 'a';
        $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->_checkAttributeSync($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
        while($nht37a5301a88da334dc5afc5b63979daa0f3f45e68 == 'invalid'){
            $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['attribute_code'] = $nhte91f32cd87c8e5054ce3950561664c8c48a9afde.'_'.$nhtec87faca4cbad909219bbcea9dbbe370a9f8c690;
            $nht37a5301a88da334dc5afc5b63979daa0f3f45e68 = $this->_checkAttributeSync($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
            $nhtec87faca4cbad909219bbcea9dbbe370a9f8c690++;
        }
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['check'] = $nht37a5301a88da334dc5afc5b63979daa0f3f45e68;
        return $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd;
    }

    protected function _duplicateAttributeOptionExists($nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b, $nht225aefa4f6e9df4f6565a7caf17b87c2ea6e8866, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed = 0){
        $nht513f8de9259fe7658fe14d1352c54ccf070e911f = $this->getAllAttributeOptions($nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
        if($nht225aefa4f6e9df4f6565a7caf17b87c2ea6e8866 && $nhtdf674d7e55c664c0df30c27a8a08c96836faf408 = $nht225aefa4f6e9df4f6565a7caf17b87c2ea6e8866['value']){
            foreach($nhtdf674d7e55c664c0df30c27a8a08c96836faf408 as $nhte19ad52a146d46abb337f4346f9d1aa6bf37c805 => $nhtbf8e69937ad3256d484316c80ded2aed7b79595b){
                foreach($nht513f8de9259fe7658fe14d1352c54ccf070e911f as $nht14eb14ece52df99c284b819d9f8092e50aa1613e){
                    if($nhtbf8e69937ad3256d484316c80ded2aed7b79595b[$nhtf3172007d4de5ae8e7692759d79f67f5558242ed] == $nht14eb14ece52df99c284b819d9f8092e50aa1613e['value']){
                        unset($nhtdf674d7e55c664c0df30c27a8a08c96836faf408[$nhte19ad52a146d46abb337f4346f9d1aa6bf37c805]);
                        break ;
                    }
                }
            }
        }
        $nht225aefa4f6e9df4f6565a7caf17b87c2ea6e8866['value'] = $nhtdf674d7e55c664c0df30c27a8a08c96836faf408;
        return $nht225aefa4f6e9df4f6565a7caf17b87c2ea6e8866;
    }

    public function getAttributeOptionValueByListOption($nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b, $nht225aefa4f6e9df4f6565a7caf17b87c2ea6e8866 = array(), $nhtf3172007d4de5ae8e7692759d79f67f5558242ed = 0){
        $nht513f8de9259fe7658fe14d1352c54ccf070e911f = $this->getAllAttributeOptions($nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
        $nht56a78ba90ef18e3f76ab83284e73db436d14e463 = array();
        if($nht225aefa4f6e9df4f6565a7caf17b87c2ea6e8866 && $nhtdf674d7e55c664c0df30c27a8a08c96836faf408 = $nht225aefa4f6e9df4f6565a7caf17b87c2ea6e8866['value']){
            foreach($nhtdf674d7e55c664c0df30c27a8a08c96836faf408 as $nhte19ad52a146d46abb337f4346f9d1aa6bf37c805 => $nhtbf8e69937ad3256d484316c80ded2aed7b79595b){
                foreach($nht513f8de9259fe7658fe14d1352c54ccf070e911f as $nht14eb14ece52df99c284b819d9f8092e50aa1613e){
                    if($nhtbf8e69937ad3256d484316c80ded2aed7b79595b[$nhtf3172007d4de5ae8e7692759d79f67f5558242ed] == $nht14eb14ece52df99c284b819d9f8092e50aa1613e['value']){
                        $nht56a78ba90ef18e3f76ab83284e73db436d14e463[$nhte19ad52a146d46abb337f4346f9d1aa6bf37c805] = $nht14eb14ece52df99c284b819d9f8092e50aa1613e['option_id'];
                        break ;
                    }
                }
            }
        }
        return $nht56a78ba90ef18e3f76ab83284e73db436d14e463;
    }

    public function getAllAttributeOptions($nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b, $nhtf3172007d4de5ae8e7692759d79f67f5558242ed) {
        $nht343fef1a3759f46a6a118e66cd16650b26d8e9fa = $this->_resource->getTableName('eav_attribute_option');
        $nht5671f45aaa2aca0626f85bf9125090ef6dd803dd = $this->_resource->getTableName('eav_attribute_option_value');
        $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "SELECT a.option_id, b.value FROM " . $nht343fef1a3759f46a6a118e66cd16650b26d8e9fa . " AS a LEFT JOIN " . $nht5671f45aaa2aca0626f85bf9125090ef6dd803dd . " AS b ON a.option_id = b.option_id WHERE b.store_id = " . $nhtf3172007d4de5ae8e7692759d79f67f5558242ed . " AND a.attribute_id = " . $nhtb13fb7c5c9e9dff62b60e0de72929155d3b6167b;
        $nht814605c64a5c1c3c2d3c2c332153f0e425e92653 = $this->getConnection();
        $nht513f8de9259fe7658fe14d1352c54ccf070e911f = $nht814605c64a5c1c3c2d3c2c332153f0e425e92653->fetchAll($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72);
        return $nht513f8de9259fe7658fe14d1352c54ccf070e911f;
    }

    /**
     * Import address
     */
    public function address($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd, $nhta7a13f4cacb744524e44dfdad329d540144d209d){
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        try{
            $nhtc662180230cad14787d4ab7e77aa08681ce783fa = $this->_objectManager->create('Magento\Customer\Model\Address');
            $nhtc662180230cad14787d4ab7e77aa08681ce783fa->setCustomerId($nhta7a13f4cacb744524e44dfdad329d540144d209d);
            $nhtc662180230cad14787d4ab7e77aa08681ce783fa->addData($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
            $nhtc662180230cad14787d4ab7e77aa08681ce783fa->save();
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['mage_id'] = $nhtc662180230cad14787d4ab7e77aa08681ce783fa->getId();
        } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    protected function _addProductIsView($nhtbebc9158e480b949565b4dc7a82d05cfd99935d5, $nhta7a13f4cacb744524e44dfdad329d540144d209d = false){
        if(!$nhta7a13f4cacb744524e44dfdad329d540144d209d) $nhta7a13f4cacb744524e44dfdad329d540144d209d = 0;
        $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd = array(
            'event_type_id' => 1,
            'object_id' => $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5,
            'subject_id' => $nhta7a13f4cacb744524e44dfdad329d540144d209d,
            'subtype' => 1,
            'store_id' => 0,
        );
        try{
            $nhte5c98e3a9a205030fcbadbcfaa29bc9cb8423c73 = $this->_objectManager->create('Magento\Reports\Model\Event');
            $nhte5c98e3a9a205030fcbadbcfaa29bc9cb8423c73->addData($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
            $nhte5c98e3a9a205030fcbadbcfaa29bc9cb8423c73->save();
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){}
    }

    protected function _addProductIsBestseller($nhteae6177aa7fd233407ab78db49aced8942dc3fce, $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af){
        try{
            $nht7a104738973573b63f13bdc7a1d816e09b6016ad = $this->_objectManager->create('Magento\Framework\App\ResourceConnection');
            $nhte1d0c6c1c29e6ad5164072a5b21340dca7fcb052 = $nht7a104738973573b63f13bdc7a1d816e09b6016ad->getConnection('core_write');
            $nht3915396b5fe58dce8505b3e62c31b4f79b3ccc2c = array(
                'sales/bestsellers_aggregated_daily',
                'sales/bestsellers_aggregated_monthly',
                'sales/bestsellers_aggregated_yearly'
            );
            $nht54235b8b0e980ebb3355126954c130a816a21afb = $this->_getDateFromDateTime($nhteae6177aa7fd233407ab78db49aced8942dc3fce);
            foreach($nht3915396b5fe58dce8505b3e62c31b4f79b3ccc2c as $nhtc3ee137d4f22eb06ed1351d644f3674592c90836){
                $nht2cb0b78846e0a65d35a4cd02d3cb9710abda4912 = $nht7a104738973573b63f13bdc7a1d816e09b6016ad->getTableName($nhtc3ee137d4f22eb06ed1351d644f3674592c90836);
                $nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72 = "INSERT INTO `{$nht2cb0b78846e0a65d35a4cd02d3cb9710abda4912}` (`id`, `period`, `store_id`, `product_id`, `product_name`, `product_price`, `qty_ordered`, `rating_pos`)
                                VALUES (null, '{$nht54235b8b0e980ebb3355126954c130a816a21afb}', 0, '{$nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af['product_id']}', '{$nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af['name']}', '{$nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af['original_price']}', '{$nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af['qty_ordered']}', 1)";
                $nhte1d0c6c1c29e6ad5164072a5b21340dca7fcb052->query($nht7cd9148ec5a552dbf68de5a6debcf8e4d974db72);
            }
        }catch (\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){}
        return ;
    }

    protected function _getDateFromDateTime($nht89ffad089c042f31dcc81269da38bef3ca44ab1f){
        $nhtf7e663289261bf5663f14748dd0817050df83f40 = new DateTime($nht89ffad089c042f31dcc81269da38bef3ca44ab1f);
        $nhte927d0677c77241b707442314346326278051dd6 = $nhtf7e663289261bf5663f14748dd0817050df83f40->format('Y-m-d');
        return $nhte927d0677c77241b707442314346326278051dd6;
    }

    public function ordersComment($nht1031171c13130102495201e3e20faf818f08474a, $nht118a9989815489c24b81b160782015890ed2085e){
        try{
            $nhtcce55e4309a753985bdd21919395fdc17daa11e4 = $this->_objectManager->create('Magento\Sales\Model\Order')->load($nht1031171c13130102495201e3e20faf818f08474a);
            $nht66f79d8a6327c82c9033e6d65ff03322a3766c87 = $this->_objectManager->create('Magento\Sales\Model\Order\Status\History')
                ->setStatus($nht118a9989815489c24b81b160782015890ed2085e['status'])
                ->setComment($nht118a9989815489c24b81b160782015890ed2085e['comment'])
                ->setEntityName('order')
                ->setIsCustomerNotified($nht118a9989815489c24b81b160782015890ed2085e['is_customer_notified'])
                ->setCreatedAt($nht118a9989815489c24b81b160782015890ed2085e['created_at']);
            $nhtcce55e4309a753985bdd21919395fdc17daa11e4->addStatusHistory($nht66f79d8a6327c82c9033e6d65ff03322a3766c87);
            /*if($nht118a9989815489c24b81b160782015890ed2085e['updated_at']){
                $nhtcce55e4309a753985bdd21919395fdc17daa11e4->setUpdatedAt($nht118a9989815489c24b81b160782015890ed2085e['updated_at'])
                    ->setStatus($nht118a9989815489c24b81b160782015890ed2085e['status']);
                if(isset($nht118a9989815489c24b81b160782015890ed2085e['state']) && $nht118a9989815489c24b81b160782015890ed2085e['state']){
                    $nhtcce55e4309a753985bdd21919395fdc17daa11e4->setData('state', $nht118a9989815489c24b81b160782015890ed2085e['state']);
                }
            }*/
            $nhtcce55e4309a753985bdd21919395fdc17daa11e4->save();
        } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            // do nothing
        }
        return ;
    }

    public function ordersInvoice($nht1031171c13130102495201e3e20faf818f08474a, $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd) {
        $nht5ad0ec5b41de36a1dab5fed9172cefd666ba2e4d = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['invoice'];
        $nht8aab8ca59d058abbd9a6daf5f3f469ad8f5d78fe = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['item'];
        $nht7b9b669983d54907fd264c2d670d2ab1218c273e = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['comment'];
        $nhtf3172007d4de5ae8e7692759d79f67f5558242ed = $nht5ad0ec5b41de36a1dab5fed9172cefd666ba2e4d['store_id'];
        try {
            $nht4815e7e0e84194823b9519b0730cf301d21987de = $this->_objectManager->get('Magento\Eav\Model\Config')->getEntityType('invoice')->fetchNewIncrementId($nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
            $nhtbaeba7e7b9ec453e23b5bac313219285d6292358 = $this->_objectManager->create('Magento\Sales\Model\Order\Invoice')
                ->setIncrementId($nht4815e7e0e84194823b9519b0730cf301d21987de)
                ->setStoreId($nhtf3172007d4de5ae8e7692759d79f67f5558242ed)
                ->setOrderId($nht1031171c13130102495201e3e20faf818f08474a);
            $nhtcce55e4309a753985bdd21919395fdc17daa11e4 = $this->_objectManager->create('Magento\Sales\Model\Order')->load($nht1031171c13130102495201e3e20faf818f08474a);
            $nht8b8df4acff091662117f8fa20746ad7c4c56d329 = $nhtcce55e4309a753985bdd21919395fdc17daa11e4->getAllItems();
            foreach ($nht8aab8ca59d058abbd9a6daf5f3f469ad8f5d78fe as $nht87ea5dfc8b8e384d848979496e706390b497e547 => $nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af) {
                $nht63ce5fe36d9569a82a022a3599b6d5e3ea58babf = $this->_objectManager->create('Magento\Sales\Model\Order\Invoice\Item')
                    ->setOrderItem($nht8b8df4acff091662117f8fa20746ad7c4c56d329[$nht87ea5dfc8b8e384d848979496e706390b497e547]);
                $nht63ce5fe36d9569a82a022a3599b6d5e3ea58babf->addData($nht3a7d9767b1233601ebf8b67495c6dc2ce8b8c2af);
                $nhtbaeba7e7b9ec453e23b5bac313219285d6292358->addItem($nht63ce5fe36d9569a82a022a3599b6d5e3ea58babf);
            }
            $nhtbaeba7e7b9ec453e23b5bac313219285d6292358->addData($nht5ad0ec5b41de36a1dab5fed9172cefd666ba2e4d);
            $nhtbaeba7e7b9ec453e23b5bac313219285d6292358->save();
            foreach ($nht7b9b669983d54907fd264c2d670d2ab1218c273e as $nht118a9989815489c24b81b160782015890ed2085e) {
                /*$nhtaa969a04be709a099e57afc43d488ed92a73e328 = $this->_objectManager->create('Magento\Sales\Model\Order\Invoice\Comment')
                        ->setStoreId($nhtf3172007d4de5ae8e7692759d79f67f5558242ed);
                $nhtaa969a04be709a099e57afc43d488ed92a73e328->addData($nht118a9989815489c24b81b160782015890ed2085e);
                $nhtbaeba7e7b9ec453e23b5bac313219285d6292358->getCommentsCollection()->addItem($nhtaa969a04be709a099e57afc43d488ed92a73e328);*/
                $nhtbaeba7e7b9ec453e23b5bac313219285d6292358->addComment($nht118a9989815489c24b81b160782015890ed2085e['comment'], (bool)$nht118a9989815489c24b81b160782015890ed2085e['is_customer_notified'], (bool)$nht118a9989815489c24b81b160782015890ed2085e['is_visible_on_front']);
            }
            $nhtbaeba7e7b9ec453e23b5bac313219285d6292358->save();
        } catch (\Exception $nhte066133fdba5e5077ee034d757dc6dfcebd12979) {

        }
    }

    public function productDownloadLink($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd) {
        if (isset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['product_id'])) {
            $nhtbebc9158e480b949565b4dc7a82d05cfd99935d5 = $nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['product_id'];
            unset($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd['product_id']);
        } else {
            return false;
        }
        try {
            $nht5bd48d1c6f05bd01e338dfe5d1b82b472aa73084 = $this->_objectManager->create('Magento\Downloadable\Model\Link')
                ->setProductId($nhtbebc9158e480b949565b4dc7a82d05cfd99935d5);
            $nht5bd48d1c6f05bd01e338dfe5d1b82b472aa73084->addData($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
            $nht5bd48d1c6f05bd01e338dfe5d1b82b472aa73084->save();
        } catch (\Exception $nhte066133fdba5e5077ee034d757dc6dfcebd12979) {

        }
    }

    public function currencyAllow($nht5af241468b9087c5e55af77f5d378a1d67301fc5){
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        try{
            $this->_configManager->saveConfig('currency/options/allow', $nht5af241468b9087c5e55af77f5d378a1d67301fc5, 'default', 0);
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
        } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    public function currencyDefault($nhtc294949a2b878b1515697587cacaff7ead4742fb){
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        try{
            $this->_configManager->saveConfig('currency/options/default', $nhtc294949a2b878b1515697587cacaff7ead4742fb, 'default', 0);
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
        } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

    public function currencyRate($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd){
        $nht0ec6d150549780250a9772c06b619bcc46a0e560 = array();
        try{
            $this->_objectManager->create('Magento\Directory\Model\Currency')->saveRates($nhta17c9aaa61e80a1bf71d0d850af4e5baa9800bbd);
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'success';
        } catch(\Exception $nht58e6b3a414a1e090dfc6029add0f3555ccba127f){
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['result'] = 'error';
            $nht0ec6d150549780250a9772c06b619bcc46a0e560['msg'] = $nht58e6b3a414a1e090dfc6029add0f3555ccba127f->getMessage();
        }
        return $nht0ec6d150549780250a9772c06b619bcc46a0e560;
    }

}
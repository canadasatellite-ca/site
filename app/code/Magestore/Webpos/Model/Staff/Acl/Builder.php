<?php
/**
 *  Copyright © 2016 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Model\Staff\Acl;

class Builder
{
    /**
     * Acl object
     *
     * @var \Magento\Framework\Acl
     */
    protected $_webposAcl;

    /**
     * Acl loader list
     *
     * @var \Magento\Framework\Acl\LoaderInterface[]
     */
    protected $_loaderPool;

    /**
     * ACL cache
     *
     * @var \Magento\Framework\Acl\CacheInterface
     */
    protected $_webposCache;

    /**
     * @var \Magento\Framework\AclFactory
     */
    protected $_webposAclFactory;

    /**
     * @param \Magento\Framework\AclFactory $aclFactory
     * @param \Magento\Framework\Acl\CacheInterface $cache
     * @param \Magento\Framework\Acl\LoaderInterface $roleLoader
     * @param \Magento\Framework\Acl\LoaderInterface $resourceLoader
     * @param \Magento\Framework\Acl\LoaderInterface $ruleLoader
     */
    public function __construct(
        \Magento\Framework\AclFactory $aclFactory,
        \Magento\Framework\Acl\Data\CacheInterface $cache,
        \Magento\Framework\Acl\LoaderInterface $roleLoader,
        \Magento\Framework\Acl\LoaderInterface $resourceLoader,
        \Magento\Framework\Acl\LoaderInterface $ruleLoader
    ) {
        $this->_webposAclFactory = $aclFactory;
        $this->_webposCache = $cache;
        $this->_loaderPool = [$roleLoader, $resourceLoader, $ruleLoader];
    }

    public function getAcl()
    {
        try {
            if ($this->_webposCache->test('magestore_acl_cache')) {
                $this->_webposAcl = $this->_webposCache->load('magestore_acl_cache');
            } else {
                $this->_webposAcl = $this->_webposAclFactory->create();

                foreach ($this->_loaderPool as $loader) {
                    $loader->populateAcl($this->_webposAcl);
                }
                $this->_webposCache->save($this->_webposAcl, 'magestore_acl_cache');
            }
        } catch (\Exception $e) {
            throw new \LogicException('Could not create acl object: ' . $e->getMessage());
        }

        return $this->_webposAcl;
    }
}

<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_Blog
 * @copyright   Copyright (c) 2016 Mageplaza (http://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */
namespace Mageplaza\Blog\Controller\Adminhtml\Post;

class Tags extends \Mageplaza\Blog\Controller\Adminhtml\Post
{
    /**
     * Result layout factory
     *
     * @var \Magento\Framework\View\Result\LayoutFactory
     */
	public $resultLayoutFactory;

    /**
     * constructor
     *
     * @param \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
     * @param \Mageplaza\Blog\Model\PostFactory $tagFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    function __construct(
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory,
        \Mageplaza\Blog\Model\PostFactory $tagFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->resultLayoutFactory = $resultLayoutFactory;
        parent::__construct($tagFactory, $registry, $context);
    }

    /**
     * @return \Magento\Framework\View\Result\Layout
     */
    function execute()
    {
        $this->initPost();
        $resultLayout = $this->resultLayoutFactory->create();
        /** @var \Mageplaza\Blog\Block\Adminhtml\Post\Edit\Tab\Tag $tagsBlock */
        $tagsBlock = $resultLayout->getLayout()->getBlock('post.edit.tab.tag');
        if ($tagsBlock) {
            $tagsBlock->setPostTags($this->getRequest()->getPost('post_tags', null));
        }
        return $resultLayout;
    }
}

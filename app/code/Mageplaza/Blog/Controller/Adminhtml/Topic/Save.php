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
namespace Mageplaza\Blog\Controller\Adminhtml\Topic;

class Save extends \Mageplaza\Blog\Controller\Adminhtml\Topic
{
    /**
     * Backend session
     *
     * @var \Magento\Backend\Model\Session
     */
	public $backendSession;

    /**
     * JS helper
     *
     * @var \Magento\Backend\Helper\Js
     */
	public $jsHelper;

    /**
     * constructor
     *
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Backend\Helper\Js $jsHelper
     * @param \Mageplaza\Blog\Model\TopicFactory $topicFactory
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Backend\Model\View\Result\RedirectFactory $resultRedirectFactory
     * @param \Magento\Backend\App\Action\Context $context
     */
    function __construct(
        \Magento\Backend\Helper\Js $jsHelper,
        \Mageplaza\Blog\Model\TopicFactory $topicFactory,
        \Magento\Framework\Registry $registry,
        \Magento\Backend\App\Action\Context $context
    ) {
    
        $this->backendSession = $context->getSession();
        $this->jsHelper       = $jsHelper;
        parent::__construct($topicFactory, $registry, $context);
    }

    /**
     * run the action
     *
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    function execute()
    {
        $data = $this->getRequest()->getPost('topic');
        $data['store_ids'] = implode(',', $data['store_ids']);
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            $topic = $this->initTopic();
            $topic->setData($data);
            $posts = $this->getRequest()->getPost('posts', -1);
            if ($posts != -1) {
                $topic->setPostsData($this->jsHelper->decodeGridSerializedInput($posts));
            }
            $this->_eventManager->dispatch(
                'mageplaza_blog_topic_prepare_save',
                [
                    'topic' => $topic,
                    'request' => $this->getRequest()
                ]
            );
            try {
                $topic->save();
                $this->messageManager->addSuccess(__('The Topic has been saved.'));
                $this->backendSession->setMageplazaBlogTopicData(false);
                if ($this->getRequest()->getParam('back')) {
                    $resultRedirect->setPath(
                        'mageplaza_blog/*/edit',
                        [
                            'topic_id' => $topic->getId(),
                            '_current' => true
                        ]
                    );
                    return $resultRedirect;
                }
                $resultRedirect->setPath('mageplaza_blog/*/');
                return $resultRedirect;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\RuntimeException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addException($e, __('Something went wrong while saving the Topic.'));
            }
            $this->_getSession()->setMageplazaBlogTopicData($data);
            $resultRedirect->setPath(
                'mageplaza_blog/*/edit',
                [
                    'topic_id' => $topic->getId(),
                    '_current' => true
                ]
            );
            return $resultRedirect;
        }
        $resultRedirect->setPath('mageplaza_blog/*/');
        return $resultRedirect;
    }
}

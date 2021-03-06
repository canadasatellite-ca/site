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
namespace Mageplaza\Blog\Block\Adminhtml\Tag\Edit\Tab;

class Tag extends \Magento\Backend\Block\Widget\Form\Generic implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Wysiwyg config
     *
     * @var \Magento\Cms\Model\Wysiwyg\Config
     */
	public $wysiwygConfig;

    /**
     * Country options
     *
     * @var \Magento\Config\Model\Config\Source\Yesno
     */
	public $booleanOptions;

	public $systemStore;

	public $metaRobots;

    /**
     * constructor
     *
     * @param \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig
     * @param \Magento\Config\Model\Config\Source\Yesno $booleanOptions
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    function __construct(
        \Magento\Cms\Model\Wysiwyg\Config $wysiwygConfig,
        \Magento\Config\Model\Config\Source\Yesno $booleanOptions,
        \Magento\Store\Model\System\Store $systemStore,
		\Mageplaza\Blog\Model\Config\Source\MetaRobots $metaRobotsOptions,
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
    
        $this->wysiwygConfig  = $wysiwygConfig;
        $this->booleanOptions = $booleanOptions;
        $this->systemStore = $systemStore;
        $this->metaRobots = $metaRobotsOptions;
        parent::__construct($context, $registry, $formFactory, $data);
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        /** @var \Mageplaza\Blog\Model\Tag $tag */
        $tag = $this->_coreRegistry->registry('mageplaza_blog_tag');
        $form = $this->_formFactory->create();
        $form->setHtmlIdPrefix('tag_');
        $form->setFieldNameSuffix('tag');
        $fieldset = $form->addFieldset(
            'base_fieldset',
            [
                'legend' => __('Tag Information'),
                'class'  => 'fieldset-wide'
            ]
        );
        if ($tag->getId()) {
            $fieldset->addField(
                'tag_id',
                'hidden',
                ['name' => 'tag_id']
            );
        }
        $fieldset->addField(
            'name',
            'text',
            [
                'name'  => 'name',
                'label' => __('Name'),
                'title' => __('Name'),
                'required' => true,
            ]
        );
        $fieldset->addField(
            'description',
            'editor',
            [
                'name'  => 'description',
                'label' => __('Description'),
                'title' => __('Description'),
                'config'    => $this->wysiwygConfig->getConfig()
            ]
        );
        $fieldset->addField(
            'store_ids',
            'multiselect',
            [
                'name'  => 'store_ids',
                'label' => __('Store Views'),
                'title' => __('Store Views'),
                'note' => __('Select Store Views'),
                'values' => $this->systemStore->getStoreValuesForForm(false, true),
            ]
        );
        $fieldset->addField(
            'enabled',
            'select',
            [
                'name'  => 'enabled',
                'label' => __('Enabled'),
                'title' => __('Enabled'),
                'values' => $this->booleanOptions->toOptionArray(),
            ]
        );
        $fieldset->addField(
            'url_key',
            'text',
            [
                'name'  => 'url_key',
                'label' => __('URL Key'),
                'title' => __('URL Key'),
            ]
        );
		$fieldset->addField(
			'meta_title',
			'text',
			[
				'name'  => 'meta_title',
				'label' => __('Meta Title'),
				'title' => __('Meta Title'),
			]
		);
		$fieldset->addField(
			'meta_description',
			'textarea',
			[
				'name'  => 'meta_description',
				'label' => __('Meta Description'),
				'title' => __('Meta Description'),
			]
		);
		$fieldset->addField(
			'meta_keywords',
			'textarea',
			[
				'name'  => 'meta_keywords',
				'label' => __('Meta Keywords'),
				'title' => __('Meta Keywords'),
			]
		);
		$fieldset->addField(
			'meta_robots',
			'select',
			[
				'name'  => 'meta_robots',
				'label' => __('Meta Robots'),
				'title' => __('Meta Robots'),
				'values' => $this->metaRobots->toOptionArray(),
			]
		);

        $tagData = $this->_session->getData('mageplaza_blog_tag_data', true);
        if ($tagData) {
            $tag->addData($tagData);
        } else {
            if (!$tag->getId()) {
                $tag->addData($tag->getDefaultValues());
            }
        }
        $form->addValues($tag->getData());
        $this->setForm($form);
        return parent::_prepareForm();
    }

    /**
     * Prepare label for tab
     *
     * @return string
     */
    function getTabLabel()
    {
        return __('Tag');
    }

    /**
     * Prepare title for tab
     *
     * @return string
     */
    function getTabTitle()
    {
        return $this->getTabLabel();
    }

    /**
     * Can show tab in tabs
     *
     * @return boolean
     */
    function canShowTab()
    {
        return true;
    }

    /**
     * Tab is hidden
     *
     * @return boolean
     */
    function isHidden()
    {
        return false;
    }
}

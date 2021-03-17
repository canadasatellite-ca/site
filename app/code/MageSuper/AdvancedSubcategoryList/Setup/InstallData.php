<?php
	namespace MageSuper\AdvancedSubcategoryList\Setup;
	use Magento\Framework\Module\Setup\Migration;
	use Magento\Framework\Setup\InstallDataInterface;
	use Magento\Framework\Setup\ModuleContextInterface;
	use Magento\Framework\Setup\ModuleDataSetupInterface;
	use Magento\Catalog\Setup\CategorySetupFactory;
	class InstallData implements InstallDataInterface
	{
		/**
			* Category setup factory
			*
			* @var CategorySetupFactory
		*/
		private $categorySetupFactory;
		/**
			* Init
			*
			* @param CategorySetupFactory $categorySetupFactory
		*/
		public function __construct(CategorySetupFactory $categorySetupFactory)
		{
			$this->categorySetupFactory = $categorySetupFactory;
		}
		public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
		{
			$installer = $setup;
			$installer->startSetup();
			$categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
			$entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
			$attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
			$categorySetup->removeAttribute(
			\Magento\Catalog\Model\Category::ENTITY, 'thumbnail' );
			$categorySetup->addAttribute(
			\Magento\Catalog\Model\Category::ENTITY, 'thumbnail', [
			'group' => 'General Information',
			'type' => 'varchar',
			'label' => 'Thumbnail',
			'input' => 'image',
			'backend_model' => 'Magento\Catalog\Model\Category\Attribute\Backend\Image',
			'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
			'visible' => true,
			'required' => false,
			'user_defined' => true,
			'sort_order' => 10,
			'default' => '',
			]
			);
			$installer->endSetup();
		}
	}					
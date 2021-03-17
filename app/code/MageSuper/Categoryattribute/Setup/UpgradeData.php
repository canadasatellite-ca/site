<?php	
	namespace MageSuper\Categoryattribute\Setup;	
		
	use Magento\Framework\Setup\UpgradeDataInterface;	
	use Magento\Framework\Setup\ModuleContextInterface;	
	use Magento\Framework\Setup\ModuleDataSetupInterface;	
	use Magento\Catalog\Setup\CategorySetupFactory;	
	class UpgradeData implements  UpgradeDataInterface	
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
				
		public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)		
		{			
			$installer = $setup;			
			$installer->startSetup();
			$categorySetup = $this->categorySetupFactory->create(['setup' => $setup]);
			$entityTypeId = $categorySetup->getEntityTypeId(\Magento\Catalog\Model\Category::ENTITY);
			$attributeSetId = $categorySetup->getDefaultAttributeSetId($entityTypeId);
			if (version_compare($context->getVersion(), '0.0.2') < 0) {				
								
								
								
				$categorySetup->removeAttribute(				
				\Magento\Catalog\Model\Category::ENTITY, 'alternate_url' );				
				$categorySetup->addAttribute(				
				\Magento\Catalog\Model\Category::ENTITY, 'alternate_url', [				
				'type' => 'text',				
				'label' => 'Alternate Url',				
				'input' => 'text',				
								
				'required' => false,				
				'sort_order' => 100,				
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,				
				'group' => 'General Information',				
				]				
				);				
								
								
				$categorySetup->removeAttribute(				
				\Magento\Catalog\Model\Category::ENTITY, 'noofsubcategories' );				
				$categorySetup->addAttribute(				
				\Magento\Catalog\Model\Category::ENTITY, 'noofsubcategories', [				
				'type' => 'varchar',				
				'label' => 'No of subcategorie per row',				
				'input' => 'select',				
				'source' => 'MageSuper\Categoryattribute\Model\Subcatoptions',				
				'required' => false,				
				'sort_order' => 100,				
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,				
				'group' => 'General Information',				
				]				
				);				
								
								
				$categorySetup->removeAttribute(				
				\Magento\Catalog\Model\Category::ENTITY, 'category_feedback' );				
				$categorySetup->addAttribute(				
				\Magento\Catalog\Model\Category::ENTITY, 'category_feedback', [				
				'type' => 'int',				
				'label' => 'Feedback',				
				'input' => 'select',				
				'source' => 'Magento\Eav\Model\Entity\Attribute\Source\Boolean',				
				'required' => false,				
				'sort_order' => 100,				
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,				
				'group' => 'General Information',				
				]				
				);				
								
				$categorySetup->removeAttribute(				
				\Magento\Catalog\Model\Category::ENTITY, 'secondary_description' );				
				$categorySetup->addAttribute(				
				\Magento\Catalog\Model\Category::ENTITY, 'secondary_description', [				
				'type' => 'text',				
				'label' => 'Secondary Description(Below Products)',				
				'input' => 'textarea',				
				'wysiwyg_enabled' => true,				
				'visible_on_front' => true,				
				'is_html_allowed_on_front' => true,				
				'required' => false,				
				'sort_order' => 100,				
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,				
				'group' => 'General Information',				
				]				
				);				
			}
			if (version_compare($context->getVersion(), '0.0.3') < 0) {
				
				$categorySetup->removeAttribute(\Magento\Catalog\Model\Category::ENTITY, 'noofsubcategoriesmobile' );
				$categorySetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'noofsubcategoriesmobile', [
				'type' => 'varchar',
				'label' => 'No of subcategorie per row Mobile',
				'input' => 'select',
				'source' => 'MageSuper\Categoryattribute\Model\Subcatoptionsmobile',
				'required' => false,
				'sort_order' => 100,
				'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_GLOBAL,
				'group' => 'General Information',
				]);
			}

            if (version_compare($context->getVersion(), '0.0.4') < 0) {
                $categorySetup->addAttribute(\Magento\Catalog\Model\Category::ENTITY, 'noofsubcategoriestablet', [
                    'type' => 'varchar',
                    'label' => 'No of subcategorie per row Tablet',
                    'input' => 'select',
                    'source' => 'MageSuper\Categoryattribute\Model\Subcatoptionstablet',
                    'required' => false,
                    'sort_order' => 110,
                    'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                    'group' => 'General Information',
                ]);
            }
			
			$installer->endSetup();			
						
		}		
	}																			
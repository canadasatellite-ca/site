<?php
namespace Schogini\Beanstream\Model\Request;
class Factory {
	protected $objectManager;
	protected $instanceName;
	function __construct(\Magento\Framework\ObjectManagerInterface $spaac7e9, $spd97872 = 'Schogini\\Beanstream\\Model\\Request') {
		$this->objectManager = $spaac7e9;
		$this->instanceName = $spd97872;
	}
	function create(array $sp7f1c57 = array()) {return $this->objectManager->create($this->instanceName, $sp7f1c57);}
}
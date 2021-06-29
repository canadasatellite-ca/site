<?php
namespace Schogini\Beanstream\Model\Response;
class Factory {
	private $_c;
	function __construct($c = \Schogini\Beanstream\Model\Response::class) {$this->_c = $c;}
	function create(array $p = []) {return df_new_om($this->_c, $p);}
}
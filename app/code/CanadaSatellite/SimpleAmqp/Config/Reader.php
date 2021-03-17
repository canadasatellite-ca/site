<?php

namespace CanadaSatellite\SimpleAmqp\Config;

class Reader extends \Magento\Framework\Config\Reader\Filesystem
{
	protected $_idAttributes = array(
        '/config/csQueue' => 'name',
    );

	public function __construct(
		\Magento\Framework\Config\FileResolverInterface $fileResolver,
		Converter $converter,
        SchemaLocator $schemaLocator,
        \Magento\Framework\Config\ValidationStateInterface $validationState,
        $fileName = 'simple_amqp.xml',
        $idAttributes = array(),
        $domDocumentClass = 'Magento\Framework\Config\Dom',
        $defaultScope = 'global'
	) {
		parent::__construct(
            $fileResolver,
            $converter,
            $schemaLocator,
            $validationState,
            $fileName,
            $idAttributes,
            $domDocumentClass,
            $defaultScope
        );
	}
}
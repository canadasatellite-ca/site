<?php

namespace CanadaSatellite\SimpleAmqp\Config;

class SchemaLocator implements \Magento\Framework\Config\SchemaLocatorInterface
{
    protected $schema = null;
    protected $perFileSchema = null;

    public function __construct(\Magento\Framework\Config\Dom\UrnResolver $urnResolver)
    {
        $this->schema = $urnResolver->getRealPath('urn:magento:module:CanadaSatellite_SimpleAmqp:etc/simple_amqp.xsd');
        $this->perFileSchema = $urnResolver->getRealPath('urn:magento:module:CanadaSatellite_SimpleAmqp:etc/simple_amqp.xsd');
    }

    public function getSchema()
    {
        return $this->schema;
    }

    public function getPerFileSchema()
    {
        return $this->perFileSchema;
    }
}
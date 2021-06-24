<?php

namespace CanadaSatellite\DynamicsIntegration\Utils;

class EavUtils
{
	/**
	 * @param $entity Magento entity.
	 * @param string $attributeName
	 * @return string|null
	 */
	function getTextAttributeValue($entity, $attributeName)
	{
		$attribute = $entity->getCustomAttribute($attributeName);
		if ($attribute === null) {
			return null;
		}

		return $attribute->getValue();
	}

	/**
	 * @used-by \CanadaSatellite\DynamicsIntegration\Envelope\ProductEnvelopeFactory::create()
	 * @param $entity
	 * @param $attributeName
	 * @return mixed|null
	 */
	function getDropdownAttributeValue($entity, $attributeName) {
		$attribute = $entity->getCustomAttribute($attributeName);
		if ($attribute === null) {
			return null;
		}
		$option = $attribute->getValue();
		$resource = $entity->getResource();
		if ($resource === null) {
			return null;
		}
		$attributeResource = $resource->getAttribute($attributeName);
		if ($attributeResource === null) {
			return null;
		}
		if (!$attributeResource->usesSource()) {
			return null;
		}
		return $attributeResource->getSource()->getOptionText($option);
	}

	function getDecimalAttributeValue($entity, $attributeName)
	{
		$attribute = $entity->getCustomAttribute($attributeName);
		if ($attribute === null) {
			return null;
		}

		return $attribute->getValue();
	}

	function getBooleanAttributeValue($entity, $attributeName)
	{
		$attribute = $entity->getCustomAttribute($attributeName);
		if ($attribute === null) {
			return null;
		}

		$value = $attribute->getValue();
		return $value === '1';
	}

	function getMultiselectAttributeValue($entity, $attributeName) {
		$attribute = $entity->getCustomAttribute($attributeName);
		if ($attribute === null) {
			return null;
		}

		$values = $attribute->getValue();

		$attributeResource = $entity->getResource()->getAttribute($attributeName);
		if ($attributeResource === null) {
			return null;
		}

		if (!$attributeResource->usesSource()) {
			return null;
		}

		$options = explode(",", $values);
		if (count($options) === 0) {
			return null;
		}

		$option = $options[0];
		if ($option === '') {
			return null;
		}

		return $attributeResource->getSource()->getOptionText($option);
	}
}

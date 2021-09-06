<?php

namespace CanadaSatellite\SimpleAmqp\Api;

interface BatchConsumerInterface
{
	public function consume($batch, $client, $astQueue);
}

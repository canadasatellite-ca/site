<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class OrderNoteComposer {

	/**
	* @param $noteText Note text
	* @return array Order note
	*/
	public function compose($noteText) {
		return array(
			'notetext' => $noteText,
			'isdocument' => false,
		);
	}
}
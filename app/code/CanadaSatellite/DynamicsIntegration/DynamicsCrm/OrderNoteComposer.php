<?php

namespace CanadaSatellite\DynamicsIntegration\DynamicsCrm;

class OrderNoteComposer {

    /**
     * @param string $noteText
     * @return array Order note
     */
    function compose($noteText) {
        return [
            'notetext' => $noteText,
            'isdocument' => false
        ];
    }
}
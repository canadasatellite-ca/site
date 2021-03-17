<?php

namespace CanadaSatellite\Theme\Model\Mail;

class EmailMessage extends \Magento\Framework\Mail\EmailMessage
{
    public function setPartsToBody()
    {
        $resultParts = [];
        foreach ($this->parts as $part) {
            if ($part->getType() == Mime::TYPE_OCTETSTREAM) {
                continue;
            }
            $resultParts[] = $part;
        }
        foreach ($this->parts as $part) {
            if ($part->getType() == Mime::TYPE_OCTETSTREAM) {
                $resultParts[] = $part;
            }
        }

        $mimeMessage = new MimeMessage($resultParts);
        $mimeMessage->setParts($resultParts);
        $this->zendMessage->setBody($mimeMessage);

        return $this;
    }
}

<?php

namespace CanadaSatellite\Theme\Model\Mail;

use Zend\Mime\Mime;
use Zend\Mime\Part as MimePart;
use Zend\Mime\Message as MimeMessage;

class Message extends \Magento\Framework\Mail\Message implements \Magento\Framework\Mail\MailMessageInterface
{

    /**
     * @var \Zend\Mail\Message
     */
    protected $zendMessage;

    /**
     * Message type
     *
     * @var string
     */
    private $messageType = self::TYPE_TEXT;

    /**
     * @var \Zend\Mime\Part[]
     */
    protected $parts = [];


    /**
     * Initialize dependencies.
     *
     * @param string $charset
     */
    public function __construct($charset = 'utf-8')
    {
        $this->zendMessage = new \Zend\Mail\Message();
        $this->zendMessage->setEncoding($charset);
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated
     * @see \Magento\Framework\Mail\Message::setBodyText
     * @see \Magento\Framework\Mail\Message::setBodyHtml
     */
    public function setMessageType($type)
    {
        $this->messageType = $type;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setSubject($subject)
    {
        $this->zendMessage->setSubject($subject);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getSubject()
    {
        return $this->zendMessage->getSubject();
    }

    /**
     * @inheritdoc
     */
    public function getBody()
    {
        return $this->zendMessage->getBody();
    }

    /**
     * @inheritdoc
     *
     * @deprecated This function is missing the from name. The
     * setFromAddress() function sets both from address and from name.
     * @see setFromAddress()
     */
    public function setFrom($fromAddress)
    {
        $this->setFromAddress($fromAddress, null);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setFromAddress($fromAddress, $fromName = null)
    {
        $this->zendMessage->setFrom($fromAddress, $fromName);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addTo($toAddress)
    {
        $this->zendMessage->addTo($toAddress);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addCc($ccAddress)
    {
        $this->zendMessage->addCc($ccAddress);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addBcc($bccAddress)
    {
        $this->zendMessage->addBcc($bccAddress);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function setReplyTo($replyToAddress)
    {
        $this->zendMessage->setReplyTo($replyToAddress);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getRawMessage()
    {
        return $this->zendMessage->toString();
    }

    /**.
     *
     * @param string $content
     * @return $this
     */
    public function setBodyText($content)
    {
        $textPart = new MimePart();

        $textPart->setContent($content)
            ->setType(Mime::TYPE_TEXT)
            ->setCharset($this->zendMessage->getEncoding())
            ->setDisposition(Mime::DISPOSITION_INLINE)
            ->setEncoding(Mime::ENCODING_QUOTEDPRINTABLE);

        $this->parts[] = $textPart;

        return $this;
    }

    /**
     * Add the text mime part to the message.
     *
     * @param string $content
     * @return $this
     */
    public function setBodyHtml($content)
    {
        $htmlPart = new MimePart();

        $htmlPart->setContent($content)
            ->setType(Mime::TYPE_HTML)
            ->setCharset($this->zendMessage->getEncoding())
            ->setDisposition(Mime::DISPOSITION_INLINE)
            ->setEncoding(Mime::ENCODING_QUOTEDPRINTABLE);

        $this->parts[] = $htmlPart;

        return $this;
    }

    /**
     * Add the attachment mime part to the message.
     *
     * @param string $content
     * @param string $fileName
     * @param string $fileType
     * @return $this
     */
    public function setBodyAttachment($filePath, $fileName, $fileType)
    {
        $attachmentPart = new MimePart(fopen($filePath, 'r'));

        $attachmentPart
            ->setType(Mime::TYPE_OCTETSTREAM)
            ->setFileName($fileName)
            ->setDisposition(Mime::DISPOSITION_ATTACHMENT)
            ->setEncoding(Mime::ENCODING_BASE64);

        $this->parts[] = $attachmentPart;

        return $this;
    }

    /**
     * Set parts to Zend message body.
     *
     * @return $this
     */
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

    /**
     * @param $body
     * @return $this|\Magento\Framework\Mail\Message
     */
    public function setBody($body)
    {
        $this->setBodyHtml($body);
        return $this;
    }
}

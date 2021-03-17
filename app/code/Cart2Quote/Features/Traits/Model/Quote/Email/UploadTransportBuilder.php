<?php
/**
 * Copyright (c) 2020. Cart2Quote B.V. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Cart2Quote\Features\Traits\Model\Quote\Email;
use Magento\Email\Model\AbstractTemplate;
use Magento\Framework\Exception\MailException;
use Magento\Framework\Mail\AddressConverter;
use Magento\Framework\Mail\EmailMessageInterfaceFactory;
use Magento\Framework\Mail\MimeMessageInterfaceFactory;
use Magento\Framework\Mail\MimePartInterfaceFactory;
/**
 * Trait UploadTransportBuilder
 *
 * @package Cart2Quote\Quotation\Model\Quote\Email
 */
trait UploadTransportBuilder
{
    /**
     * Add cc address
     *
     * @param array|string $address
     * @param string $name
     *
     * @return \Magento\Framework\Mail\Template\TransportBuilder
     * @throws MailException
     */
    private function addCc($address, $name = '')
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (method_exists(\Magento\Framework\Mail\Template\TransportBuilder::class, 'addAddressByType')) {
            $this->addAddressByType('cc', $address, $name);
        } else {
            parent::addCc($address, $name);
        }
        return $this;
		}
	}
    /**
     * Add to address
     *
     * @param array|string $address
     * @param string $name
     *
     * @return $this
     * @throws MailException
     */
    private function addTo($address, $name = '')
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (method_exists(\Magento\Framework\Mail\Template\TransportBuilder::class, 'addAddressByType')) {
            $this->addAddressByType('to', $address, $name);
        } else {
            parent::addTo($address, $name);
        }
        return $this;
		}
	}
    /**
     * Add bcc address
     *
     * @param array|string $address
     *
     * @return $this
     * @throws MailException
     */
    private function addBcc($address)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (method_exists(\Magento\Framework\Mail\Template\TransportBuilder::class, 'addAddressByType')) {
            $this->addAddressByType('bcc', $address);
        } else {
            parent::addBcc($address);
        }
        return $this;
		}
	}
    /**
     * Set Reply-To Header
     *
     * @param string $email
     * @param string|null $name
     *
     * @return $this
     * @throws MailException
     */
    private function setReplyTo($email, $name = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (method_exists(\Magento\Framework\Mail\Template\TransportBuilder::class, 'addAddressByType')) {
            $this->addAddressByType('replyTo', $email, $name);
        } else {
            parent::setReplyTo($email, $name);
        }
        return $this;
		}
	}
    /**
     * Set mail from address
     *
     * @param string|array $from
     *
     * @return $this
     * @throws MailException
     * @see setFromByScope()
     */
    private function setFrom($from)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->setFromByScope($from);
		}
	}
    /**
     * Set mail from address by scopeId
     *
     * @param string|array $from
     * @param string|int $scopeId
     *
     * @return $this
     * @throws MailException
     */
    private function setFromByScope($from, $scopeId = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$result = $this->_senderResolver->resolve($from, $scopeId);
        if (method_exists(\Magento\Framework\Mail\Template\TransportBuilder::class, 'addAddressByType')) {
            //M2.3.3 support
            $this->addAddressByType('from', $result['email'], $result['name']);
        } else {
            if (method_exists(\Magento\Framework\Mail\Template\TransportBuilder::class, 'setFromByScope')) {
                //M2.3.1 support (and some M2.2.x versions)
                parent::setFromByScope($from, $scopeId);
            } else {
                //M2.1 support
                $this->message->setFrom($result['email'], $result['name']);
            }
        }
        return $this;
		}
	}
    /**
     * Function to attach a file to an outgoing email
     *
     * @param string $file
     * @param string $name
     * @return array
     */
    private function attachFile($file, $name)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			return $this->zendAdapter->attachFileAdapter($file, $name);
		}
	}
    /**
     * Get mail message
     *
     * @param array $attachedPart
     * @return \Magento\Framework\Mail\TransportInterface
     */
    private function getMessage($attachedPart)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$template = $this->getTemplate();
        $body = $template->processTemplate();
        if (class_exists('Magento\Framework\Mail\MimeMessage')) {
            $this->messageData['subject'] = html_entity_decode($template->getSubject(), ENT_QUOTES);
            return $this->prepareQuoteMessage($attachedPart, $body);
        }
        $this->zendAdapter->getMessageAdapter($attachedPart, $body, $this->message);
        $this->message->setSubject(html_entity_decode($template->getSubject(), ENT_QUOTES));
        return $this->mailTransportFactory->create(['message' => clone $this->message]);
		}
	}
    /**
     * Reset UploadTransportBuilder object state
     */
    private function resetUploadTransportBuilder()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->reset();
		}
	}
    /**
     * Sets up template filter
     *
     * @param AbstractTemplate $template
     *
     * @return void
     */
    private function setTemplateFilter(AbstractTemplate $template)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (isset($this->templateData['template_filter'])) {
            $template->setTemplateFilter($this->templateData['template_filter']);
        }
		}
	}
    /**
     * @param array $attachedPart
     * @param string $body
     * @return \Magento\Framework\Mail\TransportInterface
     */
    private function prepareQuoteMessage($attachedPart, $body)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$mimePart = $this->mimePartInterfaceFactory->create(
            ['content' => $body]
        );
        $mimeParts[] = $mimePart;
        foreach ($attachedPart as $part) {
            $mimeParts[] = $part;
            //set charset based on attached parts
            try {
                $this->messageData['encoding'] = $part->getCharset();
            } catch (\Exception $exception) {
                //do nothing
            }
        }
        $this->messageData['encoding'] = $mimePart->getCharset();
        $this->messageData['body'] = $this->mimeMessageInterfaceFactory->create(
            ['parts' => $mimeParts]
        );
        $this->message = $this->emailMessageInterfaceFactory->create($this->messageData);
        return $this->mailTransportFactory->create(['message' => clone $this->message]);
		}
	}
    /**
     * Handles possible incoming types of email (string or array)
     * Note: addressConverter is only set when on Magento 2.3.3+
     *
     * @param string $addressType
     * @param string|array $email
     * @param string|null $name
     *
     * @return void
     * @throws MailException
     */
    private function addAddressByType(string $addressType, $email, $name = null)
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			if (is_array($email)) {
            $this->messageData[$addressType] = array_merge(
                $this->messageData[$addressType],
                $this->addressConverter->convertMany($email)
            );
            return;
        }
        $this->messageData[$addressType][] = $this->addressConverter->convert($email, $name);
		}
	}
    /**
     * Reset object state
     *
     * @return $this|\Magento\Framework\Mail\Template\TransportBuilder
     */
    private function reset()
    {
		if(\Cart2Quote\License\Model\License::getInstance()->isValid()) {
			$this->messageData = [];
        $this->templateIdentifier = null;
        $this->templateVars = null;
        $this->templateOptions = null;
        return parent::reset();
		}
	}
}

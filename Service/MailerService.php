<?php

namespace Grizzlylab\Bundle\MailerBundle\Service;

use Swift_Mailer;
use Swift_Attachment;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

/**
 * class MailerService
 *
 * @author Jean-Louis Pirson <jeanlouis@myqm.io>
 */
class MailerService implements MailerServiceInterface
{
    protected $mailer;
    protected $sender;
    protected $templating;

    /**
     * {@inheritdoc}
     */
    public function __construct(
        Swift_Mailer $mailer,
        ?EngineInterface $templating, // if null, it must be set with the setter method "setTemplating"
        array $sender
    ) {
        $this->mailer = $mailer;
        $this->templating = $templating;
        $this->sender = $sender;
    }

    /**
     * {@inheritdoc}
     */
    public function send(
        $content,
        $addresses,
        $subject = null,
        array $templateParameters = [],
        $contentIsATemplate = true,
        array $sender = null,
        Swift_Attachment $attachment = null,
        $contentType = 'text/html',
        $charset = null
    ) {
        if ($contentIsATemplate) {
            // Render the email, use the first line as the subject, && the rest as the body
            $renderedLines = explode("\n", trim($this->templating->render($content, $templateParameters)));

            if ($subject === null) {
                $subject = $renderedLines[0];
            }
            $body = implode("\n", array_slice($renderedLines, 1));
        } else {
            $body = $content;
        }

        /** @var \Swift_Message $message */
        $message = $this->mailer->createMessage()
            ->setSubject($subject)
            ->setTo($addresses)
            ->setBody($body, $contentType, $charset);

        // Default sender
        if ($sender == null) {
            $sender = $this->sender;
        }

        if (!empty($attachment)) {
            $message->attach($attachment);
        }

        $message->setFrom($sender['address'], $sender['name']);

        return $this->mailer->send($message);
    }

    /**
     * Get sender
     * return array
     */
    public function getSender()
    {
        return $this->getSender();
    }

    /**
     * setTemplating
     *
     * @param EngineInterface $templating
     */
    public function setTemplating(EngineInterface $templating)
    {
        $this->templating = $templating;
    }
}

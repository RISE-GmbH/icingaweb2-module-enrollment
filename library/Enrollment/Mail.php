<?php

// Icinga Reporting | (c) 2018 Icinga GmbH | GPLv2

namespace Icinga\Module\Enrollment;

use Zend_Mail;
use Zend_Mail_Transport_Sendmail;


class Mail
{
    /** @var string */
    public const DEFAULT_SUBJECT = 'Icinga Enrollment';

    /** @var ?string */
    protected $from;

    /** @var string */
    protected $subject = self::DEFAULT_SUBJECT;

    /** @var ?Zend_Mail_Transport_Sendmail */
    protected $transport;


    /**
     * Get the from part
     *
     * @return  string
     */
    public function getFrom()
    {
        if (isset($this->from)) {
            return $this->from;
        }

        if (isset($_SERVER['SERVER_ADMIN'])) {
            $this->from = $_SERVER['SERVER_ADMIN'];

            return $this->from;
        }

        foreach (['HTTP_HOST', 'SERVER_NAME', 'HOSTNAME'] as $key) {
            if (isset($_SERVER[$key])) {
                $this->from = 'icinga-enrollment@' . $_SERVER[$key];

                return $this->from;
            }
        }

        $this->from = 'icinga-enrollment@localhost';

        return $this->from;
    }

    /**
     * Set the from part
     *
     * @param string $from
     *
     * @return  $this
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * Get the subject
     *
     * @return  string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set the subject
     *
     * @param string $subject
     *
     * @return  $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get the mail transport
     *
     * @return  Zend_Mail_Transport_Sendmail
     */
    public function getTransport()
    {
        if (! isset($this->transport)) {
            $this->transport = new Zend_Mail_Transport_Sendmail('-f ' . escapeshellarg($this->getFrom()));
        }

        return $this->transport;
    }


    public function send($body, $recipient)
    {
        $mail = new Zend_Mail('UTF-8');

        $mail->setFrom($this->getFrom(), '');
        $mail->addTo($recipient);
        $mail->setSubject($this->getSubject());

        if ($body && (strlen($body) !== strlen(strip_tags($body)))) {
            $mail->setBodyHtml($body);
        } else {
            $mail->setBodyText($body ?? '');
        }


        $mail->send($this->getTransport());
    }
}

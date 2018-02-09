<?php

namespace AppBundle\Entity;


//use annotation with alias Assert to fixe the valides rules
use Symfony\Component\Validator\Constraints as Assert;


class Contact
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $subject;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $fromEmail;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $toEmail;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $body;
    
     public function __construct($toEmail)
    {
        $this->setToEmail($toEmail);
    }

    /**
     * Set subject
     *
     * @param string $subject
     *
     * @return Email
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set fromEmail
     *
     * @param string $fromEmail
     *
     * @return Email
     */
    public function setFromEmail($fromEmail)
    {
        $this->fromEmail = $fromEmail;

        return $this;
    }

    /**
     * Get fromEmail
     *
     * @return string
     */
    public function getFromEmail()
    {
        return $this->fromEmail;
    }

    /**
     * Set toEmail
     *
     * @param string $toEmail
     *
     * @return Email
     */
    public function setToEmail($toEmail)
    {
        $this->toEmail = $toEmail;

        return $this;
    }

    /**
     * Get toEmail
     *
     * @return string
     */
    public function getToEmail()
    {
        return $this->toEmail;
    }

    /**
     * Set body
     *
     * @param string $body
     *
     * @return Email
     */
    public function setBody($body)
    {
        $this->body = $body;

        return $this;
    }

    /**
     * Get body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->body;
    }
}

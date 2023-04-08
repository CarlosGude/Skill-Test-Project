<?php

namespace App\Email;

use App\Entity\AbstractEntity;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractEmail
{
    protected ?Email $email;

    public function __construct(protected MailerInterface $mailer, protected RouterInterface $router)
    {
    }

    abstract public function prepareEmail(AbstractEntity $data): ?self;

    public function send(): void
    {
        if (!$this->email) {
            return;
        }
        try {
            $this->mailer->send($this->email);
        } catch (TransportExceptionInterface $e) {
            throw $e;
        }
    }
}

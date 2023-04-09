<?php

namespace App\Email;

use App\Entity\AbstractEntity;
use App\Logger\EmailLogger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractEmail
{
    protected ?Email $email;

    public function __construct(
        protected LoggerInterface $logger,
        protected MailerInterface $mailer,
        protected RouterInterface $router
    ) {
    }

    abstract public function prepareEmail(AbstractEntity $data): ?self;

    public function send(): void
    {
        if (!$this->email) {
            $this->logger->error(EmailLogger::ERROR_EMAIL_NOT_CREATED);
            return;
        }
        try {
            $this->mailer->send($this->email);
        } catch (TransportExceptionInterface $e) {
            $this->logger->error(EmailLogger::ERROR_SENDING_EMAIL, [
                'exception' => $e->getMessage(),
                'email' => $this->email,
            ]);
            throw $e;
        }
    }
}

<?php

namespace App\Email;

use App\Entity\AbstractEntity;
use App\Entity\User;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SendActiveUserEmail extends AbstractEmail
{
    public function prepareEmail(AbstractEntity $data): ?self
    {
        if(!$data instanceof  User) {
            return null;
        }

        $this->email = (new Email())
            ->from('carlos.sgude@gmail.com')
            ->to(new Address($data->getEmail(), $data->getName()))
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<a href="'.$this->router->generate(
                'app_activate_user',
                ['token' => $data->getToken()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ).'">Activate User</a>');

        return $this;
    }
}

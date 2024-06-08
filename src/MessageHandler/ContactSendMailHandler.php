<?php

namespace App\MessageHandler;

use App\Message\ContactSendMail;
use App\Repository\ContactRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use Symfony\Component\Messenger\MessageBusInterface;

#[AsMessageHandler]
final class ContactSendMailHandler
{
    public function __construct(
        private ContactRepository      $contactRepository,
        private EntityManagerInterface $entityManager,
        private MailerInterface        $mailer)
    {
    }

    public function __invoke(ContactSendMail $message): void
    {
        $newtSendContact = $this->contactRepository->findBy(['send' => null]);
        $dateNow = new DateTime('now', new DateTimeZone('Europe/Paris'));

        foreach ($newtSendContact as $contact) {
            if ($contact->getStartAt()->format('Y-m-d H:i') === $dateNow->format('Y-m-d H:i')) {
                $mail = (new TemplatedEmail())
                    ->to('toto@toto.com')
                    ->from($contact->getEmail())
                    ->subject('Test d envoi de contact programmé')
                    ->htmlTemplate('emails/contact.html.twig');
                $this->mailer->send($mail);

                $contact->setSend(true);
                $this->entityManager->persist($contact);
                $this->entityManager->flush();
            }
        }
    }
}

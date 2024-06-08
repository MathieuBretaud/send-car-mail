<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Message\ContactSendMail;
use App\Repository\ContactRepository;
use DateTime;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Messenger\Exception\ExceptionInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;
use function Symfony\Component\Clock\now;

class ContactController extends AbstractController
{
    #[Route('/', name: 'contact')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $contact = new Contact();
        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $contact->setEmail($form->get('email')->getData());
            $contact->setStartAt($form->get('startAt')->getData());

            $entityManager->persist($contact);
            $entityManager->flush();

            $this->addFlash('success', 'Votre email a bien été programmé');
            return $this->redirectToRoute('contact');
        }

        return $this->render('contact/index.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/send', name: 'send')]
    public function send(MessageBusInterface $bus, ContactRepository $contactRepository, MailerInterface $mailer, EntityManagerInterface $entityManager): Response
    {
        $newtSendContact = $contactRepository->findOneBy(['send' => null]);
        $dateNow = new DateTime('now', new DateTimeZone('Europe/Paris'));

//        foreach ($newtSendContact as $contact) {
//            if ($contact->getStartAt()->format('Y-m-d H:i') === $dateNow->format('Y-m-d H:i')) {
//        $mail = (new TemplatedEmail())
//            ->to('toto@toto.com')
//            ->from('tata@tata.com')
//            ->subject('Demande de contact')
//            ->htmlTemplate('emails/contact.html.twig')
//            ->context(['data' => 'je suis le text du contenu']);
//        $mailer->send($mail);
//        $newtSendContact->setSend(true);
//        $entityManager->persist($newtSendContact);
//        $entityManager->flush();
//        dd($newtSendContact);
//            }
//        }
//        $bus->dispatch(new ContactSendMail());

        $this->addFlash('success', 'Votre email est en cours');
        return $this->redirectToRoute('contact');
    }
}

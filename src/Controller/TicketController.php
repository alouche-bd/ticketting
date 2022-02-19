<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use App\Repository\TicketRepository;
use App\Service\Ticket\TicketService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/ticket')]
class TicketController extends AbstractController
{
    #[Route('/on', name: 'ticket_index_on', methods: ['GET'])]
    public function indexOn(TicketRepository $ticketRepository, TicketService $ticketService): Response
    {

        $entityDisplayed = $ticketService->returnEntityDisplayed();


        $tickets = $ticketRepository->findOnByEntity($entityDisplayed);

        return $this->render('ticket/index_on.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/off', name: 'ticket_index_off', methods: ['GET'])]
    public function indexOff(TicketRepository $ticketRepository, TicketService $ticketService): Response
    {
        $entityDisplayed = $ticketService->returnEntityDisplayed();


        $tickets = $ticketRepository->findOffByEntity($entityDisplayed);

        return $this->render('ticket/index_off.html.twig', [
            'tickets' => $tickets,
        ]);
    }


    #[Route('/new', name: 'ticket_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $ticket = new Ticket();
        $user = $this->getUser();
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $ticket->setUser($user);
            $date = new \DateTimeImmutable("now");
            $ticket->setCreatedAt($date);
            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_index_on', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ticket/new.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'ticket_show', methods: ['GET'])]
    public function show(Ticket $ticket): Response
    {
        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }

    #[Route('/{id}/edit', name: 'ticket_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TicketType::class, $ticket);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $date = new \DateTimeImmutable("now");
            $ticket->setUpdatedAt($date);
            $entityManager->flush();

            return $this->redirectToRoute('ticket_index_on', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/done', name: 'ticket_done', methods: ['GET', 'POST'])]
    public function markAsDoneTicket(Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        $date = new \DateTimeImmutable("now");
        $ticket->setUpdatedAt($date);

        $ticket->setStatus("DONE");
        $entityManager->flush();

        return $this->redirectToRoute('ticket_index_on', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}', name: 'ticket_delete', methods: ['POST'])]
    public function delete(Request $request, Ticket $ticket, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $ticket->getId(), $request->request->get('_token'))) {
            $entityManager->remove($ticket);
            $entityManager->flush();
        }

        return $this->redirectToRoute('ticket_index_on', [], Response::HTTP_SEE_OTHER);
    }
}
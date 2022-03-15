<?php

namespace App\Controller;

use App\Repository\TicketRepository;
use App\Service\Ticket\TicketService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Request\RequestService;
use Symfony\Component\HttpFoundation\JsonResponse;

class TicketApiController extends AbstractController
{
    private TicketService $ticketService;

    public function __construct(TicketService $ticketService)
    {
        $this->ticketService = $ticketService;
    }
    #[Route(path: '/api/tickets', name: 'api_tickets_get', methods: ['GET'])]
    public function getTickets(TicketRepository $ticketRepository)
    {
        $tickets = $ticketRepository->findAll();

        return RequestService::returnJsonData($tickets);
    }

    #[Route(path: '/api/tickets/on', name: 'api_tickets_on', methods: ['GET'])]
    public function getTicketsOn(TicketRepository $ticketRepository)
    {
        $tickets = $ticketRepository->findOn();

        return RequestService::returnJsonData($tickets);
    }

    #[Route(path: '/api/tickets/off', name: 'api_tickets_off', methods: ['GET'])]
    public function getTicketsOff(TicketRepository $ticketRepository)
    {
        $tickets = $ticketRepository->findOff();

        return RequestService::returnJsonData($tickets);
    }

    #[Route(path: '/api/tickets', name: 'api_tickets_post', methods: ['POST'])]
    public function postTicket(Request $request)
    {
        $this->ticketService->autoCreate(
            RequestService::getField($request, 'userEmail'),
            RequestService::getField($request, 'userEntity'),
            RequestService::getField($request, 'priority'),
            RequestService::getField($request, 'client', false),
            RequestService::getField($request, 'clientName'),
            RequestService::getField($request, 'clientEmail'),
            RequestService::getField($request, 'category'),
            RequestService::getField($request, 'description', false),
            RequestService::getField($request, 'entity'),
            RequestService::getField($request, 'status'),
        );

        return new JsonResponse('Tikcet created.', 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route(path: '/api/tickets/{id}', name: 'api_tickets_put', methods: ['PUT'])]
    public function updateTicket(TicketRepository $ticketRepository, Request $request, int $id)
    {
        $ticket = $ticketRepository->findOneBy(['id' => $id]);

        $this->ticketService->autoUpdate(
            $ticket,
            RequestService::getField($request, 'priority'),
            RequestService::getField($request, 'client', false),
            RequestService::getField($request, 'clientName'),
            RequestService::getField($request, 'clientEmail'),
            RequestService::getField($request, 'category'),
            RequestService::getField($request, 'description', false),
            RequestService::getField($request, 'entity'),
            RequestService::getField($request, 'status'),
        );
        return new JsonResponse('Ticket updated.', 200, [
            'Content-Type' => 'application/json'
        ]);
    }

    #[Route(path: '/api/tickets/{id}', name: 'api_ticket_get_one', methods: ['GET'])]
    public function getOneTiket(TicketRepository $ticketRepository, int $id)
    {
        $ticket = $ticketRepository->findOneBy(['id' => $id]);

        return RequestService::returnJsonData($ticket);
    }
}
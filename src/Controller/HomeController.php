<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TicketRepository;


class HomeController extends AbstractController
{
    #[Route('/', name: 'home')]
    public function index(TicketRepository $ticketRepository): Response
    {
        return $this->render('ticket/index_on.html.twig', [
            'tickets' => $ticketRepository->findAll(),
        ]);
    }
}
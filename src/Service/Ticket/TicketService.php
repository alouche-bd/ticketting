<?php

declare(strict_types=1);

namespace App\Service\Ticket;

use App\Entity\Ticket;
use App\Entity\User;
use App\Repository\TicketRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class TicketService
{
    private UserRepository $userRepo;
    private UserPasswordHasherInterface $userPasswordHasher;
    private EntityManagerInterface $entityManager;
    private $security;

    public function __construct(Security $security, UserRepository $userRepo, TicketRepository $ticketRepo, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager)
    {
        $this->ticketRepo = $ticketRepo;
        $this->userRepo = $userRepo;
        $this->userPasswordHasher = $userPasswordHasher;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @throws \Exception
     */
    public function autoCreate(string $userEmail, string $userEntity, int $priority, ?string $client, string $clientName, string $clientEmail, string $category, ?string $description, string $entity, int $status): Ticket
    {
        $date = new \DateTimeImmutable("now");

        $ticket = new Ticket();
        if ($userEmail) {
            $isUser = $this->userRepo->findOneBy(['email' => $userEmail]);
            if (!$isUser) {
                $user = $this->createUser($userEmail, $userEntity);
                $ticket
                    ->setUser(
                        $user
                    );
            } else {
                $ticket
                    ->setUser(
                        $isUser
                    );
            }
            $ticket
                ->setPriority(
                    $priority
                )
                ->setClient(
                    $client ?? null
                )
                ->setClientName($clientName)
                ->setClientEmail($clientEmail)
                ->setCategory($category)
                ->setDescription($description)
                ->setEntity($entity)
                ->setCreatedAt($date)
                ->setStatus($status);
            try {
                $this->entityManager->persist($ticket);
                $this->entityManager->flush();
            } catch (\Exception $e) {
                throw BadRequestHttpException::class;
            }
        }

        return $ticket;
    }

    public function createUser(string $email, string $entity)
    {
        $user = new User();

        $user
            ->setEmail($email)
            ->setEntity($entity)
            ->setRoles(['ROLE_ADV'])
            ->setPassword(
                $this->userPasswordHasher->hashPassword(
                    $user,
                    '12345678'
                )
            );
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    /**
     * @throws \Exception
     */
    public function autoUpdate(Ticket $ticket, int $priority, ?string $client, string $clientName, string $clientEmail, string $category, ?string $description, string $entity, int $status): Ticket
    {
        $date = new \DateTimeImmutable("now");

        $ticket
            ->setPriority(
                $priority
            )
            ->setClient(
                $client ?? null
            )
            ->setClientName($clientName)
            ->setClientEmail($clientEmail)
            ->setCategory($category)
            ->setDescription($description)
            ->setEntity($entity)
            ->setUpdatedAt($date)
            ->setStatus($status);
        try {
            $this->entityManager->flush();
        } catch (\Exception $e) {
            throw BadRequestHttpException::class;
        }

        return $ticket;
    }

    public function returnEntityDisplayed()
    {
        $user = $this->security->getUser();

        $userEntity = $user->getEntity();

        $biotechEntities = [
            'Biotech Dental',
            'Biotech Dental Academy',
            'Biotech Dental Digital',
            'Chirurgie guidée',
        ];

        if (in_array($userEntity, $biotechEntities)) {
            return $biotechEntities;
        } else {
            return $userEntity;
        }
    }
}
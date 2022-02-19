<?php

namespace App\Form;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('client', TextType::class, [
                'label' => 'Code client SF',
                'required' => false
            ])
            ->add('clientName', TextType::class, [
                'label' => 'Nom du client*'
            ])
            ->add('clientEmail', EmailType::class, [
                'label' => 'Email du client*'
            ])
            ->add('entity', ChoiceType::class, [
                'multiple' => false,
                'choices' => [
                    'Biotech Dental' => 'Biotech Dental',
                    'Biotech Dental Academy' => 'Biotech Dental Academy',
                    'Biotech Dental Digital' => 'Biotech Dental Digital',
                    'Chirurgie guidée' => 'Chirurgie guidée',
                    'Smilers' => 'Smilers',
                    'Smilers Lab' => 'Smilers Lab',
                    'Label Dent' => 'Label Dent',
                ],
                'label' => 'Entité*'
            ])
            ->add('category', ChoiceType::class, [
                'multiple' => false,
                'choices' => [
                    'Connexion' => "Connexion",
                    'Synchronisation de données' => "Synchro",
                    'SSO' => "SSO",
                    'Bug technique' => 'bug',
                    'Autre' => 'autre'
                ],
                'label' => 'Catégorie*'
            ])
            ->add('description', TextareaType::class, [
                'required' => false
            ])
            ->add('priority', ChoiceType::class, [
                'multiple' => false,
                'choices' => [
                    'Haute' => 3,
                    'Moyenne' => 2,
                    'Faible' => 1,
                ],
                'label' => 'Priorité*'
            ])
            ->add('status', ChoiceType::class, [
                'multiple' => false,
                'choices' => [
                    'Ouvert' => 0,
                    'Fermé' => 1,
                ],
                'label' => "Status*"
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
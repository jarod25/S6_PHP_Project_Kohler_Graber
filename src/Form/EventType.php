<?php

namespace App\Form;

use App\Entity\Event;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\NotBlank;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre',
                'required' => true,
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir un titre'),
                ],
            ])
            ->add('description', TextareaType::class, [
                'row_attr' => [
                    "data-controller" => "ckeditor",
                ],
                'attr' => [
                    "data-ckeditor-target" => "txt",
                ],
                'required' => true,
                'label' => 'Description',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir une description'),
                ],
            ])
            ->add('startDate', DateTimeType::class, [
                'label' => 'Date de début',
                'required' => true,
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir une date de début d\'événement'),
                ],
            ])
            ->add('endDate', DateTimeType::class, [
                'label' => 'Date de fin',
                'required' => true,
                'widget' => 'single_text',
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir une date de fin d\'événement'),
                ],
            ])
            ->add('nbMaxParticipants', IntegerType::class, [
                'label' => 'Nombre de participants',
                'required' => true,
                'constraints' => [
                    new NotBlank(message: 'Veuillez saisir un nombre de participants'),
                    new GreaterThan(0, message: 'Veuillez saisir un nombre de participants supérieur à 0')
                ],
            ])
            ->add('isPublic', ChoiceType::class, [
                'required' => true,
                'label' => 'Évènement public ?',
                'choices' => [
                    'Oui' => true,
                    'Non' => false,
                ],
                'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Event::class,
        ]);
    }
}

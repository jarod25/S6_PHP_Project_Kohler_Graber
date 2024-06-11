<?php

namespace App\Form\User;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

class SignInType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname',TextType::class, [
                'label' => 'Prénom',
                'attr' => [
                    'placeholder' => 'Prénom',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez saisir un prénom',
                    ),
                ],
            ])
            ->add('lastname',TextType::class, [
                'label' => 'Nom',
                'attr' => [
                    'placeholder' => 'Nom',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez saisir un nom',
                    ),
                ],
            ])
            ->add('email',TextType::class, [
                'label' => 'Email',
                'attr' => [
                    'placeholder' => 'Email',
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez saisir une adresse email',
                    ),
                    new Email(
                        message: 'L\'adresse email n\'est pas valide',
                    ),
                ],
            ])
            ->add('password', RepeatedType::class, [
                'type'            => PasswordType::class,
                'invalid_message' => 'Les deux mots de passe doivent être identiques.',
                'first_options'  => [
                    'attr'     => [
                        'label' => 'Mot de passe',
                        'placeholder' => 'Mot de passe',
                        'autocomplete' => 'new-password'
                    ],
                    'row_attr' => [
                        'class' => 'form-floating',
                    ],
                ],
                'second_options' => [
                    'attr'     => [
                        'label' => 'Confirmation du mot de passe',
                        'placeholder' => 'Confirmation du mot de passe',
                        'autocomplete' => 'new-password'
                    ],
                    'row_attr' => [
                        'class' => 'form-floating',
                    ],
                ],
                'attr'     => [
                    'placeholder' => 'Mot de passe',
                    'autocomplete' => 'new-password'
                ],
                'row_attr' => [
                    'class' => 'form-floating',
                ],
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Length(
                        min: 8,
                        max: 4096,
                        minMessage: 'Votre mot de passe doit comporter au moins {{ limit }} caractères',
                        maxMessage: 'Votre mot de passe doit comporter maximum {{ limit }} caractères',
                    ),
                    new Regex(
                        pattern: '#^(?=.*[A-Za-z])(?=.*\d).+$#',
                        message: 'Votre mot de passe doit contenir au moins une lettre et un chiffre'
                    )
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
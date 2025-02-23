<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre prénom'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le prénom est requis']),
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre nom'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le nom est requis']),
                ],
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse e-mail',
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre adresse e-mail'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'L\'email est requis']),
                    new Assert\Email(['message' => 'L\'email n\'est pas valide']),
                ],
            ])
            ->add('plainPassword', PasswordType::class, [
                'label' => 'Mot de passe',
                'mapped' => false, // 🔹 Ce champ n'est pas stocké directement dans l'entité User
                'attr' => ['class' => 'form-control', 'placeholder' => 'Votre mot de passe'],
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Le mot de passe est requis']),
                    new Assert\Length([
                        'min' => 6,
                        'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères',
                    ]),
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'label' => 'J\'accepte les conditions d\'utilisation',
                'mapped' => false,
                'required' => true,
                'attr' => ['class' => 'form-check-input'],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}

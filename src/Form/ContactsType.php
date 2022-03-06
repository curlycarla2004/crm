<?php

namespace App\Form;

use App\Entity\Contacts;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Regex;

class ContactsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('FirstName', TextType::class, [
                'constraints' => [
                    new NotNull([
                        'message' => 'Please enter the new contact name.'
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'The first name has to have at least {{ limit }} letters.',
                        'max' => 50,
                        'maxMessage' => 'The first name cannot contain more than {{ limit }} letters.',
                    ]),
                ],
            ])
            ->add('LastName', TextType::class, [
                'constraints' => [
                    new NotNull([
                        'message' => 'Please enter the new contact name.'
                    ]),
                    new Length([
                        'min' => 2,
                        'minMessage' => 'The last name has to have at least {{ limit }} letters.',
                        'max' => 50,
                        'maxMessage' => 'The last name cannot contain more than {{ limit }} letters.',
                    ]),
                ],
            ])
            ->add('email', EmailType::class, [
                "invalid_message" => "The email format is not correct.",
                'constraints' => [
                    new Email ([
                        'message' => "This email is not valide.",
                    ])
                ],
            ])
            ->add('address', TextType::class)
            ->add('city', TextType::class)
            ->add('zipCode')
            ->add('country', ChoiceType::class, [
                'choices' => [
                    'France' => 'France',
                    'USA' => 'USA',
                    'Germany' => 'Germany',
                    'Czech Republic' => 'Czech Republic',

                ],
                'preferred_choices' => ['France'],
            ])
            ->add('phone', TelType::class, [
                'constraints'=> [
                    new Regex([
                            'pattern' => "/(?:(?:\+|00)33|0)\s*[1-9](?:[\s.-]*\d{2}){4}$/",
                            'message' => "The phone number is not valid. ",
                        ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contacts::class,
        ]);
    }
}

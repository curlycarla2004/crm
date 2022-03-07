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
                    new Length([
                        'min' => 2,
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('LastName', TextType::class, [
                'constraints' => [
                    new Length([
                        'min' => 2,
                        'max' => 50,
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
            ->add('zipCode', TextType::class)
            ->add('country', TextType::class)
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

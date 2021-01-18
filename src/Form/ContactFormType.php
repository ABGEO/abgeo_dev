<?php

namespace App\Form;

use Karser\Recaptcha3Bundle\Form\Recaptcha3Type;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class ContactFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('full_name', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter Your Name'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'validation.is_blank'
                    ])
                ]
            ])
            ->add('email', EmailType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter E-Mail address'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'validation.is_blank'
                    ]),
                    new Email([
                        'message' => 'validation.invalid_email'
                    ])
                ]
            ])
            ->add('subject', TextType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter Subject'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'validation.is_blank'
                    ])
                ]
            ])
            ->add('captcha', Recaptcha3Type::class, [
                'action_name' => 'contact',
                'constraints' => new Recaptcha3(),
            ])
            ->add('message', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter Message'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'validation.is_blank'
                    ])
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}

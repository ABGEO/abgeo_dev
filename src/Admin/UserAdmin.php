<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\User;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class UserAdmin extends AbstractAdmin
{

    protected $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('username');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', NumberType::class)
            ->addIdentifier('username', TextType::class)
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $roleChoices = [
            'User' => 'ROLE_USER',
            'Super Admin' => 'ROLE_SUPER_ADMIN'
        ];

        $formMapper
            ->add('username', TextType::class)
            ->add('roles', ChoiceType::class, [
                'choices' => $roleChoices,
                'expanded' => false,
                'multiple' => true
            ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => 'Passwords don\'t match!',
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Repeat Password'],
                'required' => false
            ]);
    }

    public function prePersist($object)
    {
        /** @var User $object */
        $container = $this->getConfigurationPool()
            ->getContainer();

        // Get password encoder
        $encoder = $container->get('security.password_encoder');

        // Encode password
        $object->setPassword($encoder->encodePassword(
            $object,
            $object->getPlainPassword()
        ));

        parent::prePersist($object);
    }

    public function preUpdate($object)
    {
        /** @var User $object */
        $container = $this->getConfigurationPool()
            ->getContainer();

        $doctrine = $container->get('doctrine');
        $userRepo = $doctrine->getRepository(User::class);

        // Get editing user from database.
        $user = $userRepo->findOneBy(['id' => $object->getId()]);

        if ($newPassword = $object->getPlainPassword()) {
            // Get password encoder
            $encoder = $container->get('security.password_encoder');

            // Encode password
            $object->setPassword($encoder->encodePassword(
                $object,
                $newPassword
            ));
        } else {
            $object->setPassword($user->getPassword());
        }
    }

}

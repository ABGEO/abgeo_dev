<?php

declare(strict_types=1);

namespace App\Admin;

use App\Entity\Project;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\Form\Validator\ErrorElement;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

final class ProjectAdmin extends AbstractAdmin
{

    protected $datagridValues = [
        '_sort_order' => 'DESC',
        '_sort_by' => 'id',
    ];

    protected function configureDatagridFilters(DatagridMapper $datagridMapper): void
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('startDate')
            ->add('description');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', NumberType::class)
            ->addIdentifier('title', TextType::class)
            ->add('startDate')
            ->add('_action', null, [
                'actions' => [
                    'edit' => [],
                    'delete' => [],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('title')
            ->add('uploadedImage', FileType::class, [
                'label' => 'Image *',
                'required' => false
            ])
            ->add('startDate')
            ->add('description');
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        /** @var Project $object */
        if (
            null === $object->getId()
            && null === $object->getImage()
        ) {
            $errorElement
                ->with('uploadedImage')
                ->assertNotNull(array())
                ->end();
        }

        parent::validate($errorElement, $object);
    }

    public function preRemove($object)
    {
        /** @var Project $object */
        $this->_removeFile($object->getImage());
        parent::preRemove($object);
    }

    public function getBatchActions()
    {
        $actions = parent::getBatchActions();
        unset($actions['delete']);

        return $actions;
    }

    private function _removeFile(string $fileName): void
    {
        $filePath = __DIR__ . '/../../public/uploads/images/projects/' . $fileName;

        $filesystem = new Filesystem();

        if ($filesystem->exists($filePath)) {
            $filesystem->remove($filePath);
        }
    }

}

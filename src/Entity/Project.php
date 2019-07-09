<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity(repositoryClass="App\Repository\ProjectRepository")
 */
class Project
{
    const PATH_TO_PROJECT_IMAGES_FOLDER = __DIR__ . '/../../public/uploads/images/projects';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $title;

    private $uploadedImage;

    /**
     * @ORM\Column(type="string", length=50)
     */
    private $image;

    /**
     * @ORM\Column(type="date")
     */
    private $startDate;

    /**
     * @ORM\Column(type="text")
     */
    private $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    /**
     * @param UploadedFile|null $image
     */
    public function setUploadedImage(UploadedFile $image = null)
    {
        $this->uploadedImage = $image;
        $this->_upload();
    }

    /**
     * @return UploadedFile
     */
    public function getUploadedImage()
    {
        return $this->uploadedImage;
    }

    /**
     * Manages the copying of the file to the relevant place on the server
     */
    private function _upload()
    {
        if ($image = $this->getUploadedImage()) {
            $this->image = $this->image ?: uniqid() . '.' . $image->getClientOriginalExtension();
            $imagePath = self::PATH_TO_PROJECT_IMAGES_FOLDER;

            $filesystem = new Filesystem();

            // Create images directory if dont exists.
            if (!$filesystem->exists($imagePath)) {
                $filesystem->mkdir($imagePath, 0755);
            }

            // Move file to images directory.
            $image->move($imagePath, $this->image);

            $this->setUploadedImage(null);
        }
    }

    public function getStartDate(): ?\DateTimeInterface
    {
        return $this->startDate;
    }

    public function setStartDate(\DateTimeInterface $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }
}

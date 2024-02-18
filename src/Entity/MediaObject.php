<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiProperty;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\OpenApi\Model;
use App\Controller\MediaObjectController;
use App\Repository\MediaObjectRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

#[Vich\Uploadable]
#[ORM\Entity(repositoryClass: MediaObjectRepository::class)]
#[ApiResource(
    types: ['https://schema.org/MediaObject'],
    operations: [
        new Get(),
        new GetCollection(),
        new Post(
            controller: MediaObjectController::class,
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ]
                                ]
                            ]
                        ]
                    ])
                )
            ),
            validationContext: ['groups' => ['Default', 'media_object:create']],
            deserialize: false
        )
    ],
    normalizationContext: ['groups' => ['media_object:read']]
)]
class MediaObject
{
    #[ORM\Id, ORM\Column, ORM\GeneratedValue]
    #[Groups(['media_object:read'])]
    private ?int $id = null;

    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
    #[Groups(['media_object:read', 'movie:read', 'actor:read', 'user:read'])]
    public ?string $contentUrl = null;

    #[Vich\UploadableField(mapping: "media_object", fileNameProperty: "filePath")]
    #[Assert\NotNull(groups: ['media_object:create'])]
    #[Assert\File(
        maxSize: '1M',
        mimeTypes: ['image/jpeg', 'image/png', 'image/gif'],
        mimeTypesMessage: 'Le fichier doit être une image',
    )]
    public ?File $file = null;

    #[ORM\Column(nullable: true)]
    public ?string $filePath = null;

    #[ORM\ManyToOne(targetEntity: Movie::class, inversedBy: 'mediaObjects')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Movie $movie = null;

    #[ORM\ManyToOne(targetEntity: Actor::class, inversedBy: 'mediaObjects')]
    #[ORM\JoinColumn(onDelete: 'CASCADE')]
    private ?Actor $actor = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMovie(): ?Movie
    {
        return $this->movie;
    }

    public function setMovie(?Movie $movie): self
    {
        $this->movie = $movie;

        return $this;
    }

    public function getActor(): ?Actor
    {
        return $this->actor;
    }

    public function setActor(?Actor $actor): self
    {
        $this->actor = $actor;

        return $this;
    }

    public function getFile(): ?File
    {
        return $this->file;
    }

    public function setFile(?File $file): MediaObject
    {
        $this->file = $file;
        return $this;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): MediaObject
    {
        $this->filePath = $filePath;
        return $this;
    }
}

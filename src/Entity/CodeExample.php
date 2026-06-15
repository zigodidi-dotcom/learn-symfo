<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use App\Enum\CodeLanguage;
use App\Repository\CodeExampleRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CodeExampleRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['example:read']],
    denormalizationContext: ['groups' => ['example:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Put(),
        new Delete(),
    ]
)]
class CodeExample
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['example:read', 'feature:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 200)]
    #[Assert\NotBlank]
    #[Groups(['example:read', 'example:write', 'feature:read'])]
    private string $title;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['example:read', 'example:write', 'feature:read'])]
    private string $code;

    #[ORM\Column(enumType: CodeLanguage::class)]
    #[Assert\NotNull]
    #[Groups(['example:read', 'example:write', 'feature:read'])]
    private CodeLanguage $language = CodeLanguage::Php;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups(['example:read', 'example:write', 'feature:read'])]
    private ?string $description = null;

    #[ORM\ManyToOne(targetEntity: Feature::class, inversedBy: 'examples')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['example:read', 'example:write'])]
    private ?Feature $feature = null;

    public function getId(): ?int { return $this->id; }

    public function getTitle(): string { return $this->title; }
    public function setTitle(string $title): static { $this->title = $title; return $this; }

    public function getCode(): string { return $this->code; }
    public function setCode(string $code): static { $this->code = $code; return $this; }

    public function getLanguage(): CodeLanguage { return $this->language; }
    public function setLanguage(CodeLanguage $language): static { $this->language = $language; return $this; }

    public function getDescription(): ?string { return $this->description; }
    public function setDescription(?string $description): static { $this->description = $description; return $this; }

    public function getFeature(): ?Feature { return $this->feature; }
    public function setFeature(?Feature $feature): static { $this->feature = $feature; return $this; }
}

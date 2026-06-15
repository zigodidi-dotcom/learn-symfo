<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Put;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;
use App\Repository\TermRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: TermRepository::class)]
#[UniqueEntity('slug')]
#[ApiResource(
    normalizationContext: ['groups' => ['term:read']],
    denormalizationContext: ['groups' => ['term:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Put(),
        new Delete(),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: ['name' => 'partial'])]
class Term
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['term:read', 'feature:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 150, unique: true)]
    #[Assert\NotBlank]
    #[Groups(['term:read', 'term:write', 'feature:read'])]
    private string $name;

    #[ORM\Column(length: 170, unique: true)]
    #[Assert\NotBlank]
    #[Groups(['term:read', 'term:write'])]
    private string $slug;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['term:read', 'term:write'])]
    private string $definition;

    #[ORM\ManyToMany(targetEntity: Feature::class, mappedBy: 'terms')]
    #[Groups(['term:read'])]
    private Collection $features;

    public function __construct()
    {
        $this->features = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): static { $this->slug = $slug; return $this; }

    public function getDefinition(): string { return $this->definition; }
    public function setDefinition(string $definition): static { $this->definition = $definition; return $this; }

    public function getFeatures(): Collection { return $this->features; }
    public function addFeature(Feature $feature): static
    {
        if (!$this->features->contains($feature)) {
            $this->features->add($feature);
        }
        return $this;
    }
    public function removeFeature(Feature $feature): static { $this->features->removeElement($feature); return $this; }
}

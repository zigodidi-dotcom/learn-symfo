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
use ApiPlatform\Doctrine\Orm\Filter\OrderFilter;
use App\Enum\Difficulty;
use App\Enum\FeatureType;
use App\Repository\FeatureRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FeatureRepository::class)]
#[UniqueEntity('slug')]
#[ApiResource(
    normalizationContext: ['groups' => ['feature:read']],
    denormalizationContext: ['groups' => ['feature:write']],
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Put(),
        new Delete(),
    ]
)]
#[ApiFilter(SearchFilter::class, properties: [
    'name' => 'partial',
    'category.slug' => 'exact',
    'difficulty' => 'exact',
    'type' => 'exact',
    'tags.slug' => 'exact',
])]
#[ApiFilter(OrderFilter::class, properties: ['name', 'difficulty', 'sinceVersion'])]
class Feature
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['feature:read'])]
    private ?int $id = null;

    #[ORM\Column(length: 150)]
    #[Assert\NotBlank]
    #[Groups(['feature:read', 'feature:write'])]
    private string $name;

    #[ORM\Column(length: 170, unique: true)]
    #[Assert\NotBlank]
    #[Groups(['feature:read', 'feature:write'])]
    private string $slug;

    #[ORM\Column(type: 'text')]
    #[Assert\NotBlank]
    #[Groups(['feature:read', 'feature:write'])]
    private string $description;

    #[ORM\Column(length: 10, nullable: true)]
    #[Groups(['feature:read', 'feature:write'])]
    private ?string $sinceVersion = null;

    #[ORM\Column(enumType: Difficulty::class)]
    #[Assert\NotNull]
    #[Groups(['feature:read', 'feature:write'])]
    private Difficulty $difficulty = Difficulty::Beginner;

    #[ORM\Column(enumType: FeatureType::class)]
    #[Assert\NotNull]
    #[Groups(['feature:read', 'feature:write'])]
    private FeatureType $type = FeatureType::Concept;

    #[ORM\ManyToOne(targetEntity: Category::class, inversedBy: 'features')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotNull]
    #[Groups(['feature:read', 'feature:write'])]
    private ?Category $category = null;

    #[ORM\ManyToMany(targetEntity: Tag::class, inversedBy: 'features')]
    #[Groups(['feature:read', 'feature:write'])]
    private Collection $tags;

    #[ORM\ManyToMany(targetEntity: Term::class, inversedBy: 'features')]
    #[Groups(['feature:read'])]
    private Collection $terms;

    #[ORM\OneToMany(targetEntity: CodeExample::class, mappedBy: 'feature', cascade: ['persist', 'remove'])]
    #[Groups(['feature:read'])]
    private Collection $examples;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
        $this->terms = new ArrayCollection();
        $this->examples = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }

    public function getName(): string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getSlug(): string { return $this->slug; }
    public function setSlug(string $slug): static { $this->slug = $slug; return $this; }

    public function getDescription(): string { return $this->description; }
    public function setDescription(string $description): static { $this->description = $description; return $this; }

    public function getSinceVersion(): ?string { return $this->sinceVersion; }
    public function setSinceVersion(?string $sinceVersion): static { $this->sinceVersion = $sinceVersion; return $this; }

    public function getDifficulty(): Difficulty { return $this->difficulty; }
    public function setDifficulty(Difficulty $difficulty): static { $this->difficulty = $difficulty; return $this; }

    public function getType(): FeatureType { return $this->type; }
    public function setType(FeatureType $type): static { $this->type = $type; return $this; }

    public function getCategory(): ?Category { return $this->category; }
    public function setCategory(?Category $category): static { $this->category = $category; return $this; }

    public function getTags(): Collection { return $this->tags; }
    public function addTag(Tag $tag): static
    {
        if (!$this->tags->contains($tag)) {
            $this->tags->add($tag);
        }
        return $this;
    }
    public function removeTag(Tag $tag): static { $this->tags->removeElement($tag); return $this; }

    public function getTerms(): Collection { return $this->terms; }
    public function addTerm(Term $term): static
    {
        if (!$this->terms->contains($term)) {
            $this->terms->add($term);
            $term->addFeature($this);
        }
        return $this;
    }
    public function removeTerm(Term $term): static
    {
        if ($this->terms->removeElement($term)) {
            $term->removeFeature($this);
        }
        return $this;
    }

    public function getExamples(): Collection { return $this->examples; }
    public function addExample(CodeExample $example): static
    {
        if (!$this->examples->contains($example)) {
            $this->examples->add($example);
            $example->setFeature($this);
        }
        return $this;
    }
    public function removeExample(CodeExample $example): static
    {
        if ($this->examples->removeElement($example)) {
            if ($example->getFeature() === $this) {
                $example->setFeature(null);
            }
        }
        return $this;
    }
}

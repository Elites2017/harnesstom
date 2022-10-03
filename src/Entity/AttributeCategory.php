<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AttributeCategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=AttributeCategoryRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"attribute_category:read"}},
 *      denormalizationContext={"groups"={"attribute_category:write"}}
 * )
 */
class AttributeCategory
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"attribute_category:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"attribute_category:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"attribute_category:read"})
     * 
     */
    private $description;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="attributeCategories")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Attribute::class, mappedBy="category")
     */
    private $attributes;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $ontology_id;

    /**
     * @ORM\ManyToOne(targetEntity=AttributeCategory::class, inversedBy="attributeCategories")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=AttributeCategory::class, mappedBy="parentTerm")
     */
    private $attributeCategories;

    public function __construct()
    {
        $this->attributes = new ArrayCollection();
        $this->attributeCategories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * @return Collection<int, Attribute>
     */
    public function getAttributes(): Collection
    {
        return $this->attributes;
    }

    public function addAttribute(Attribute $attribute): self
    {
        if (!$this->attributes->contains($attribute)) {
            $this->attributes[] = $attribute;
            $attribute->setCategory($this);
        }

        return $this;
    }

    public function removeAttribute(Attribute $attribute): self
    {
        if ($this->attributes->removeElement($attribute)) {
            // set the owning side to null (unless already changed)
            if ($attribute->getCategory() === $this) {
                $attribute->setCategory(null);
            }
        }

        return $this;
    }

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->name;
    }

    public function getOntologyId(): ?string
    {
        return $this->ontology_id;
    }

    public function setOntologyId(string $ontology_id): self
    {
        $this->ontology_id = $ontology_id;

        return $this;
    }

    public function getParentTerm(): ?self
    {
        return $this->parentTerm;
    }

    public function setParentTerm(?self $parentTerm): self
    {
        $this->parentTerm = $parentTerm;

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getAttributeCategories(): Collection
    {
        return $this->attributeCategories;
    }

    public function addAttributeCategory(self $attributeCategory): self
    {
        if (!$this->attributeCategories->contains($attributeCategory)) {
            $this->attributeCategories[] = $attributeCategory;
            $attributeCategory->setParentTerm($this);
        }

        return $this;
    }

    public function removeAttributeCategory(self $attributeCategory): self
    {
        if ($this->attributeCategories->removeElement($attributeCategory)) {
            // set the owning side to null (unless already changed)
            if ($attributeCategory->getParentTerm() === $this) {
                $attributeCategory->setParentTerm(null);
            }
        }

        return $this;
    }
}

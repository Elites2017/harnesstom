<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AttributeRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=AttributeRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"attribute:read"}},
 *      denormalizationContext={"groups"={"attribute:write"}}
 * )
 */
class Attribute
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"attribute:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"attribute:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"attribute:read"})
     */
    private $abbreviation;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"attribute:read"})
     */
    private $description;

    /**
     * @ORM\Column(type="array", nullable=true)
     * @Groups({"attribute:read"})
     */
    private $publicationReference = [];

    /**
     * @ORM\ManyToOne(targetEntity=AttributeCategory::class, inversedBy="attributes")
     */
    private $category;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="attributes")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=AttributeTraitValue::class, mappedBy="attribute")
     */
    private $attributeTraitValues;

    public function __construct()
    {
        $this->attributeTraitValues = new ArrayCollection();
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

    public function getAbbreviation(): ?string
    {
        return $this->abbreviation;
    }

    public function setAbbreviation(?string $abbreviation): self
    {
        $this->abbreviation = $abbreviation;

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

    public function getPublicationReference(): ?array
    {
        return $this->publicationReference;
    }

    public function setPublicationReference(?array $publicationReference): self
    {
        $this->publicationReference = $publicationReference;

        return $this;
    }

    public function getCategory(): ?AttributeCategory
    {
        return $this->category;
    }

    public function setCategory(?AttributeCategory $category): self
    {
        $this->category = $category;

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
     * @return Collection<int, AttributeTraitValue>
     */
    public function getAttributeTraitValues(): Collection
    {
        return $this->attributeTraitValues;
    }

    public function addAttributeTraitValue(AttributeTraitValue $attributeTraitValue): self
    {
        if (!$this->attributeTraitValues->contains($attributeTraitValue)) {
            $this->attributeTraitValues[] = $attributeTraitValue;
            $attributeTraitValue->setAttribute($this);
        }

        return $this;
    }

    public function removeAttributeTraitValue(AttributeTraitValue $attributeTraitValue): self
    {
        if ($this->attributeTraitValues->removeElement($attributeTraitValue)) {
            // set the owning side to null (unless already changed)
            if ($attributeTraitValue->getAttribute() === $this) {
                $attributeTraitValue->setAttribute(null);
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
}

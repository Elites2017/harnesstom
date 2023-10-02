<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\AttributeTraitValueRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=AttributeTraitValueRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"attribute_t_v:read"}},
 *      denormalizationContext={"groups"={"attribute_t_v:write"}}
 * )
 */
class AttributeTraitValue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"attribute_t_v:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TraitClass::class, inversedBy="attributeTraitValues")
     * @Groups({"attribute_t_v:read"})
     */
    private $trait;

    /**
     * @ORM\ManyToOne(targetEntity=MetabolicTrait::class, inversedBy="attributeTraitValues")
     */
    private $metabolicTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Attribute::class, inversedBy="attributeTraitValues")
     */
    private $attribute;

    /**
     * @ORM\ManyToOne(targetEntity=Accession::class, inversedBy="attributeTraitValues")
     */
    private $accession;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"attribute_t_v:read"})
     */
    private $value;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $publicationReference = [];

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="attributeTraitValues")
     */
    private $createdBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTrait(): ?TraitClass
    {
        return $this->trait;
    }

    public function setTrait(?TraitClass $trait): self
    {
        $this->trait = $trait;

        return $this;
    }

    public function getMetabolicTrait(): ?MetabolicTrait
    {
        return $this->metabolicTrait;
    }

    public function setMetabolicTrait(?MetabolicTrait $metabolicTrait): self
    {
        $this->metabolicTrait = $metabolicTrait;

        return $this;
    }

    public function getAttribute(): ?Attribute
    {
        return $this->attribute;
    }

    public function setAttribute(?Attribute $attribute): self
    {
        $this->attribute = $attribute;

        return $this;
    }

    public function getAccession(): ?Accession
    {
        return $this->accession;
    }

    public function setAccession(?Accession $accession): self
    {
        $this->accession = $accession;

        return $this;
    }

    public function getValue(): ?string
    {
        return $this->value;
    }

    public function setValue(string $value): self
    {
        $this->value = $value;

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

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->trait;
    }

    // API Septembre 2023
    
    /**
     * @Groups({"attribute_t_v:read"})
     * 
     */
    public function getAttributeName() {
        return $this->attribute->getName();
    }

    /**
     * @Groups({"attribute_t_v:read"})
     * 
     */
    public function getAttributeValueDbId() {
        return $this->id;
    }

    /**
     * @Groups({"attribute_t_v:read"})
     * 
     */
    public function getAttributeDbId() {
        return $this->attribute->getId();
    }

    /**
     * @Groups({"attribute_t_v:read"})
     * 
     */
    public function getGermplasmDbId() {
        return $this->accession->getId();
    }

    /**
     * @Groups({"attribute_t_v:read"})
     * 
     */
    public function getGermplasmName() {
        return $this->accession->getAccename();
    }

}

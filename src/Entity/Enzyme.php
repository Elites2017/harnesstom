<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\EnzymeRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=EnzymeRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"enzyme:read"}},
 *      denormalizationContext={"groups"={"enzyme:write"}}
 * )
 */
class Enzyme
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"enzyme:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"enzyme:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="enzymes")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $ontology_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=Enzyme::class, inversedBy="enzymes")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=Enzyme::class, mappedBy="parentTerm")
     */
    private $enzymes;

    /**
     * 
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $par_ont;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $is_poau;

    public function __construct()
    {
        $this->enzymes = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

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
    public function getEnzymes(): Collection
    {
        return $this->enzymes;
    }

    public function addEnzyme(self $enzyme): self
    {
        if (!$this->enzymes->contains($enzyme)) {
            $this->enzymes[] = $enzyme;
            $enzyme->setParentTerm($this);
        }

        return $this;
    }

    public function removeEnzyme(self $enzyme): self
    {
        if ($this->enzymes->removeElement($enzyme)) {
            // set the owning side to null (unless already changed)
            if ($enzyme->getParentTerm() === $this) {
                $enzyme->setParentTerm(null);
            }
        }

        return $this;
    }

    public function getParOnt(): ?string
    {
        return $this->par_ont;
    }

    public function setParOnt(string $par_ont): self
    {
        $this->par_ont = $par_ont;

        return $this;
    }

    public function getIsPoau(): ?bool
    {
        return $this->is_poau;
    }

    public function setIsPoau(?bool $is_poau): self
    {
        $this->is_poau = $is_poau;

        return $this;
    }
}

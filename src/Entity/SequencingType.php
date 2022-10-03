<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SequencingTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=SequencingTypeRepository::class)
 * @ApiResource
 */
class SequencingType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="text")
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="sequencingTypes")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=GenotypingPlatform::class, mappedBy="sequencingType")
     */
    private $genotypingPlatforms;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $ontology_id;

    /**
     * @ORM\ManyToOne(targetEntity=SequencingType::class, inversedBy="sequencingTypes")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=SequencingType::class, mappedBy="parentTerm")
     */
    private $sequencingTypes;

    public function __construct()
    {
        $this->genotypingPlatforms = new ArrayCollection();
        $this->sequencingTypes = new ArrayCollection();
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

    /**
     * @return Collection<int, GenotypingPlatform>
     */
    public function getGenotypingPlatforms(): Collection
    {
        return $this->genotypingPlatforms;
    }

    public function addGenotypingPlatform(GenotypingPlatform $genotypingPlatform): self
    {
        if (!$this->genotypingPlatforms->contains($genotypingPlatform)) {
            $this->genotypingPlatforms[] = $genotypingPlatform;
            $genotypingPlatform->setSequencingType($this);
        }

        return $this;
    }

    public function removeGenotypingPlatform(GenotypingPlatform $genotypingPlatform): self
    {
        if ($this->genotypingPlatforms->removeElement($genotypingPlatform)) {
            // set the owning side to null (unless already changed)
            if ($genotypingPlatform->getSequencingType() === $this) {
                $genotypingPlatform->setSequencingType(null);
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
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
    public function getSequencingTypes(): Collection
    {
        return $this->sequencingTypes;
    }

    public function addSequencingType(self $sequencingType): self
    {
        if (!$this->sequencingTypes->contains($sequencingType)) {
            $this->sequencingTypes[] = $sequencingType;
            $sequencingType->setParentTerm($this);
        }

        return $this;
    }

    public function removeSequencingType(self $sequencingType): self
    {
        if ($this->sequencingTypes->removeElement($sequencingType)) {
            // set the owning side to null (unless already changed)
            if ($sequencingType->getParentTerm() === $this) {
                $sequencingType->setParentTerm(null);
            }
        }

        return $this;
    }
}

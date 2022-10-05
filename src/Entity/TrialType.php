<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\TrialTypeRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=TrialTypeRepository::class)
 * @ApiResource
 */
class TrialType
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
     * 
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $ontology_id;

    /**
     * @ORM\Column(type="text", nullable=true)
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="trialTypes")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Trial::class, mappedBy="trialType")
     */
    private $trials;

    /**
     * @ORM\ManyToOne(targetEntity=TrialType::class, inversedBy="trialTypes")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=TrialType::class, mappedBy="parentTerm")
     */
    private $trialTypes;

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
        $this->trials = new ArrayCollection();
        $this->trialTypes = new ArrayCollection();
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

    public function getOntologyId(): ?string
    {
        return $this->ontology_id;
    }

    public function setOntologyId(?string $ontology_id): self
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
     * @return Collection<int, Trial>
     */
    public function getTrials(): Collection
    {
        return $this->trials;
    }

    public function addTrial(Trial $trial): self
    {
        if (!$this->trials->contains($trial)) {
            $this->trials[] = $trial;
            $trial->setTrialType($this);
        }

        return $this;
    }

    public function removeTrial(Trial $trial): self
    {
        if ($this->trials->removeElement($trial)) {
            // set the owning side to null (unless already changed)
            if ($trial->getTrialType() === $this) {
                $trial->setTrialType(null);
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
    public function getTrialTypes(): Collection
    {
        return $this->trialTypes;
    }

    public function addTrialType(self $trialType): self
    {
        if (!$this->trialTypes->contains($trialType)) {
            $this->trialTypes[] = $trialType;
            $trialType->setParentTerm($this);
        }

        return $this;
    }

    public function removeTrialType(self $trialType): self
    {
        if ($this->trialTypes->removeElement($trialType)) {
            // set the owning side to null (unless already changed)
            if ($trialType->getParentTerm() === $this) {
                $trialType->setParentTerm(null);
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

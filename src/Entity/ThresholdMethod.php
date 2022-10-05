<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ThresholdMethodRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=ThresholdMethodRepository::class)
 * @ApiResource
 */
class ThresholdMethod
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
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="thresholdMethods")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=GWAS::class, mappedBy="thresholdMethod")
     */
    private $gWAS;

    /**
     * @ORM\OneToMany(targetEntity=QTLStudy::class, mappedBy="thresholdMethod")
     */
    private $qTLStudies;

    /**
     * @ORM\ManyToOne(targetEntity=ThresholdMethod::class, inversedBy="thresholdMethods")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=ThresholdMethod::class, mappedBy="parentTerm")
     */
    private $thresholdMethods;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

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
        $this->gWAS = new ArrayCollection();
        $this->qTLStudies = new ArrayCollection();
        $this->thresholdMethods = new ArrayCollection();
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

    public function setOntologyId(string $ontology_id): self
    {
        $this->ontology_id = $ontology_id;

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
     * @return Collection<int, GWAS>
     */
    public function getGWAS(): Collection
    {
        return $this->gWAS;
    }

    public function addGWA(GWAS $gWA): self
    {
        if (!$this->gWAS->contains($gWA)) {
            $this->gWAS[] = $gWA;
            $gWA->setThresholdMethod($this);
        }

        return $this;
    }

    public function removeGWA(GWAS $gWA): self
    {
        if ($this->gWAS->removeElement($gWA)) {
            // set the owning side to null (unless already changed)
            if ($gWA->getThresholdMethod() === $this) {
                $gWA->setThresholdMethod(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, QTLStudy>
     */
    public function getQTLStudies(): Collection
    {
        return $this->qTLStudies;
    }

    public function addQTLStudy(QTLStudy $qTLStudy): self
    {
        if (!$this->qTLStudies->contains($qTLStudy)) {
            $this->qTLStudies[] = $qTLStudy;
            $qTLStudy->setThresholdMethod($this);
        }

        return $this;
    }

    public function removeQTLStudy(QTLStudy $qTLStudy): self
    {
        if ($this->qTLStudies->removeElement($qTLStudy)) {
            // set the owning side to null (unless already changed)
            if ($qTLStudy->getThresholdMethod() === $this) {
                $qTLStudy->setThresholdMethod(null);
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
    public function getThresholdMethods(): Collection
    {
        return $this->thresholdMethods;
    }

    public function addThresholdMethod(self $thresholdMethod): self
    {
        if (!$this->thresholdMethods->contains($thresholdMethod)) {
            $this->thresholdMethods[] = $thresholdMethod;
            $thresholdMethod->setParentTerm($this);
        }

        return $this;
    }

    public function removeThresholdMethod(self $thresholdMethod): self
    {
        if ($this->thresholdMethods->removeElement($thresholdMethod)) {
            // set the owning side to null (unless already changed)
            if ($thresholdMethod->getParentTerm() === $this) {
                $thresholdMethod->setParentTerm(null);
            }
        }

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

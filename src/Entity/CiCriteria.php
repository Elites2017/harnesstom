<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CiCriteriaRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=CiCriteriaRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"ci_criteria:read"}},
 *      denormalizationContext={"groups"={"ci_criteria:write"}}
 * )
 */
class CiCriteria
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"ci_criteria:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"ci_criteria:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Groups({"ci_criteria:read"})
     */
    private $description;

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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ciCriterias")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=QTLStudy::class, mappedBy="ciCriteria")
     */
    private $qTLStudies;

    /**
     * @ORM\ManyToOne(targetEntity=CiCriteria::class, inversedBy="ciCriterias")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=CiCriteria::class, mappedBy="parentTerm")
     */
    private $ciCriterias;

    public function __construct()
    {
        $this->qTLStudies = new ArrayCollection();
        $this->ciCriterias = new ArrayCollection();
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

    public function setDescription(string $description): self
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
            $qTLStudy->setCiCriteria($this);
        }

        return $this;
    }

    public function removeQTLStudy(QTLStudy $qTLStudy): self
    {
        if ($this->qTLStudies->removeElement($qTLStudy)) {
            // set the owning side to null (unless already changed)
            if ($qTLStudy->getCiCriteria() === $this) {
                $qTLStudy->setCiCriteria(null);
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
    public function getCiCriterias(): Collection
    {
        return $this->ciCriterias;
    }

    public function addCiCriteria(self $ciCriteria): self
    {
        if (!$this->ciCriterias->contains($ciCriteria)) {
            $this->ciCriterias[] = $ciCriteria;
            $ciCriteria->setParentTerm($this);
        }

        return $this;
    }

    public function removeCiCriteria(self $ciCriteria): self
    {
        if ($this->ciCriterias->removeElement($ciCriteria)) {
            // set the owning side to null (unless already changed)
            if ($ciCriteria->getParentTerm() === $this) {
                $ciCriteria->setParentTerm(null);
            }
        }

        return $this;
    }
}

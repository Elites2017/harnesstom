<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\QTLStatisticRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=QTLStatisticRepository::class)
 * @ApiResource
 */
class QTLStatistic
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="qTLStatistics")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=QTLStudy::class, mappedBy="multiEnvironmentStat")
     */
    private $qTLStudies;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ontology_id;

    /**
     * @ORM\ManyToOne(targetEntity=QTLStatistic::class, inversedBy="qTLStatistics")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=QTLStatistic::class, mappedBy="parentTerm")
     */
    private $qTLStatistics;

    public function __construct()
    {
        $this->qTLStudies = new ArrayCollection();
        $this->qTLStatistics = new ArrayCollection();
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
            $qTLStudy->setMultiEnvironmentStat($this);
        }

        return $this;
    }

    public function removeQTLStudy(QTLStudy $qTLStudy): self
    {
        if ($this->qTLStudies->removeElement($qTLStudy)) {
            // set the owning side to null (unless already changed)
            if ($qTLStudy->getMultiEnvironmentStat() === $this) {
                $qTLStudy->setMultiEnvironmentStat(null);
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
    public function getQTLStatistics(): Collection
    {
        return $this->qTLStatistics;
    }

    public function addQTLStatistic(self $qTLStatistic): self
    {
        if (!$this->qTLStatistics->contains($qTLStatistic)) {
            $this->qTLStatistics[] = $qTLStatistic;
            $qTLStatistic->setParentTerm($this);
        }

        return $this;
    }

    public function removeQTLStatistic(self $qTLStatistic): self
    {
        if ($this->qTLStatistics->removeElement($qTLStatistic)) {
            // set the owning side to null (unless already changed)
            if ($qTLStatistic->getParentTerm() === $this) {
                $qTLStatistic->setParentTerm(null);
            }
        }

        return $this;
    }
}

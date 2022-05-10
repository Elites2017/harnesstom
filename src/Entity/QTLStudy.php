<?php

namespace App\Entity;

use App\Repository\QTLStudyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=QTLStudyRepository::class)
 */
class QTLStudy
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer")
     */
    private $qtlCount;

    /**
     * @ORM\Column(type="float")
     */
    private $thresholdValue;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $publicationReference = [];

    /**
     * @ORM\ManyToOne(targetEntity=CiCriteria::class, inversedBy="qTLStudies")
     */
    private $ciCriteria;

    /**
     * @ORM\ManyToOne(targetEntity=ThresholdMethod::class, inversedBy="qTLStudies")
     */
    private $thresholdMethod;

    /**
     * @ORM\ManyToOne(targetEntity=Software::class, inversedBy="qTLStudies")
     */
    private $software;

    /**
     * @ORM\ManyToOne(targetEntity=QTLStatistic::class, inversedBy="qTLStudies")
     */
    private $multiEnvironmentStat;

    /**
     * @ORM\ManyToOne(targetEntity=QTLMethod::class, inversedBy="qTLStudies")
     */
    private $method;

    /**
     * @ORM\Column(type="array")
     */
    private $study = [];

    /**
     * @ORM\ManyToOne(targetEntity=VariantSet::class, inversedBy="qTLStudies")
     */
    private $variantSet;

    /**
     * @ORM\ManyToOne(targetEntity=MappingPopulation::class, inversedBy="qTLStudies")
     */
    private $mappingPopulation;

    /**
     * @ORM\ManyToOne(targetEntity=Unit::class, inversedBy="qTLStudies")
     */
    private $genomeMapUnit;

    /**
     * @ORM\ManyToOne(targetEntity=QTLStatistic::class, inversedBy="qTLStudies")
     */
    private $statistic;

    /**
     * @ORM\OneToMany(targetEntity=QTLVariant::class, mappedBy="qtlStudy")
     */
    private $qTLVariants;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="qTLStudies")
     */
    private $createdBy;

    public function __construct()
    {
        $this->qTLVariants = new ArrayCollection();
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

    public function getQtlCount(): ?int
    {
        return $this->qtlCount;
    }

    public function setQtlCount(int $qtlCount): self
    {
        $this->qtlCount = $qtlCount;

        return $this;
    }

    public function getThresholdValue(): ?float
    {
        return $this->thresholdValue;
    }

    public function setThresholdValue(float $thresholdValue): self
    {
        $this->thresholdValue = $thresholdValue;

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

    public function getPublicationReference(): ?array
    {
        return $this->publicationReference;
    }

    public function setPublicationReference(?array $publicationReference): self
    {
        $this->publicationReference = $publicationReference;

        return $this;
    }

    public function getCiCriteria(): ?CiCriteria
    {
        return $this->ciCriteria;
    }

    public function setCiCriteria(?CiCriteria $ciCriteria): self
    {
        $this->ciCriteria = $ciCriteria;

        return $this;
    }

    public function getThresholdMethod(): ?ThresholdMethod
    {
        return $this->thresholdMethod;
    }

    public function setThresholdMethod(?ThresholdMethod $thresholdMethod): self
    {
        $this->thresholdMethod = $thresholdMethod;

        return $this;
    }

    public function getSoftware(): ?Software
    {
        return $this->software;
    }

    public function setSoftware(?Software $software): self
    {
        $this->software = $software;

        return $this;
    }

    public function getMultiEnvironmentStat(): ?QTLStatistic
    {
        return $this->multiEnvironmentStat;
    }

    public function setMultiEnvironmentStat(?QTLStatistic $multiEnvironmentStat): self
    {
        $this->multiEnvironmentStat = $multiEnvironmentStat;

        return $this;
    }

    public function getMethod(): ?QTLMethod
    {
        return $this->method;
    }

    public function setMethod(?QTLMethod $method): self
    {
        $this->method = $method;

        return $this;
    }

    public function getStudy(): ?array
    {
        return $this->study;
    }

    public function setStudy(array $study): self
    {
        $this->study = $study;

        return $this;
    }

    public function getVariantSet(): ?VariantSet
    {
        return $this->variantSet;
    }

    public function setVariantSet(?VariantSet $variantSet): self
    {
        $this->variantSet = $variantSet;

        return $this;
    }

    public function getMappingPopulation(): ?MappingPopulation
    {
        return $this->mappingPopulation;
    }

    public function setMappingPopulation(?MappingPopulation $mappingPopulation): self
    {
        $this->mappingPopulation = $mappingPopulation;

        return $this;
    }

    public function getGenomeMapUnit(): ?Unit
    {
        return $this->genomeMapUnit;
    }

    public function setGenomeMapUnit(?Unit $genomeMapUnit): self
    {
        $this->genomeMapUnit = $genomeMapUnit;

        return $this;
    }

    public function getStatistic(): ?QTLStatistic
    {
        return $this->statistic;
    }

    public function setStatistic(?QTLStatistic $statistic): self
    {
        $this->statistic = $statistic;

        return $this;
    }

    /**
     * @return Collection<int, QTLVariant>
     */
    public function getQTLVariants(): Collection
    {
        return $this->qTLVariants;
    }

    public function addQTLVariant(QTLVariant $qTLVariant): self
    {
        if (!$this->qTLVariants->contains($qTLVariant)) {
            $this->qTLVariants[] = $qTLVariant;
            $qTLVariant->setQtlStudy($this);
        }

        return $this;
    }

    public function removeQTLVariant(QTLVariant $qTLVariant): self
    {
        if ($this->qTLVariants->removeElement($qTLVariant)) {
            // set the owning side to null (unless already changed)
            if ($qTLVariant->getQtlStudy() === $this) {
                $qTLVariant->setQtlStudy(null);
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

    public function getCreatedBy(): ?User
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?User $createdBy): self
    {
        $this->createdBy = $createdBy;

        return $this;
    }
}

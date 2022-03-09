<?php

namespace App\Entity;

use App\Repository\GWASRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GWASRepository::class)
 */
class GWAS
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $preprocessing;

    /**
     * @ORM\Column(type="float")
     */
    private $thresholdValue;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $studyID = [];

    /**
     * @ORM\ManyToOne(targetEntity=VariantSetMetadata::class, inversedBy="gWAS")
     */
    private $variantSetMetada;

    /**
     * @ORM\ManyToOne(targetEntity=Software::class, inversedBy="gWAS")
     */
    private $software;

    /**
     * @ORM\ManyToOne(targetEntity=GWASModel::class, inversedBy="gWAS")
     */
    private $gwasModel;

    /**
     * @ORM\ManyToOne(targetEntity=KinshipAlgorithm::class, inversedBy="gWAS")
     */
    private $kinshipAlgorithm;

    /**
     * @ORM\ManyToOne(targetEntity=StructureMethod::class, inversedBy="gWAS")
     */
    private $structureMethod;

    /**
     * @ORM\ManyToOne(targetEntity=GeneticTestingModel::class, inversedBy="gWAS")
     */
    private $geneticTestingModel;

    /**
     * @ORM\ManyToOne(targetEntity=AllelicEffectEstimator::class, inversedBy="gWAS")
     */
    private $allelicEffectEstimator;

    /**
     * @ORM\ManyToOne(targetEntity=GWASStatTest::class, inversedBy="gWAS")
     */
    private $gwasStatTest;

    /**
     * @ORM\ManyToOne(targetEntity=ThresholdMethod::class, inversedBy="gWAS")
     */
    private $thresholdMethod;

    /**
     * @ORM\Column(type="array", nullable=true)
     */
    private $publicationReference = [];

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="gWAS")
     */
    private $createdBy;

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

    public function getPreprocessing(): ?string
    {
        return $this->preprocessing;
    }

    public function setPreprocessing(?string $preprocessing): self
    {
        $this->preprocessing = $preprocessing;

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

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(bool $isActive): self
    {
        $this->isActive = $isActive;

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

    public function getStudyID(): ?array
    {
        return $this->studyID;
    }

    public function setStudyID(?array $studyID): self
    {
        $this->studyID = $studyID;

        return $this;
    }

    public function getVariantSetMetada(): ?VariantSetMetadata
    {
        return $this->variantSetMetada;
    }

    public function setVariantSetMetada(?VariantSetMetadata $variantSetMetada): self
    {
        $this->variantSetMetada = $variantSetMetada;

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

    public function getGwasModel(): ?GWASModel
    {
        return $this->gwasModel;
    }

    public function setGwasModel(?GWASModel $gwasModel): self
    {
        $this->gwasModel = $gwasModel;

        return $this;
    }

    public function getKinshipAlgorithm(): ?KinshipAlgorithm
    {
        return $this->kinshipAlgorithm;
    }

    public function setKinshipAlgorithm(?KinshipAlgorithm $kinshipAlgorithm): self
    {
        $this->kinshipAlgorithm = $kinshipAlgorithm;

        return $this;
    }

    public function getStructureMethod(): ?StructureMethod
    {
        return $this->structureMethod;
    }

    public function setStructureMethod(?StructureMethod $structureMethod): self
    {
        $this->structureMethod = $structureMethod;

        return $this;
    }

    public function getGeneticTestingModel(): ?GeneticTestingModel
    {
        return $this->geneticTestingModel;
    }

    public function setGeneticTestingModel(?GeneticTestingModel $geneticTestingModel): self
    {
        $this->geneticTestingModel = $geneticTestingModel;

        return $this;
    }

    public function getAllelicEffectEstimator(): ?AllelicEffectEstimator
    {
        return $this->allelicEffectEstimator;
    }

    public function setAllelicEffectEstimator(?AllelicEffectEstimator $allelicEffectEstimator): self
    {
        $this->allelicEffectEstimator = $allelicEffectEstimator;

        return $this;
    }

    public function getGwasStatTest(): ?GWASStatTest
    {
        return $this->gwasStatTest;
    }

    public function setGwasStatTest(?GWASStatTest $gwasStatTest): self
    {
        $this->gwasStatTest = $gwasStatTest;

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

    public function getPublicationReference(): ?array
    {
        return $this->publicationReference;
    }

    public function setPublicationReference(?array $publicationReference): self
    {
        $this->publicationReference = $publicationReference;

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
}

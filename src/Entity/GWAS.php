<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\GWASRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=GWASRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"gwas:read"}},
 *      denormalizationContext={"groups"={"gwas:write"}}
 * )
 */
class GWAS
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"gwas:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"gwas:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"gwas:read"})
     */
    private $preprocessing;

    /**
     * @ORM\Column(type="float")
     * @Groups({"gwas:read"})
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
     * @ORM\ManyToOne(targetEntity=VariantSetMetadata::class, inversedBy="gWAS")
     */
    private $variantSetMetadata;

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

    /**
     * @ORM\OneToMany(targetEntity=GWASVariant::class, mappedBy="gwas")
     */
    private $gWASVariants;

    /**
     * @ORM\ManyToMany(targetEntity=Study::class, inversedBy="gwas")
     */
    private $studyList;

    public function __construct()
    {
        $this->gWASVariants = new ArrayCollection();
        $this->studyList = new ArrayCollection();
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

    public function getVariantSetMetadata(): ?VariantSetMetadata
    {
        return $this->variantSetMetadata;
    }

    public function setVariantSetMetadata(?VariantSetMetadata $variantSetMetadata): self
    {
        $this->variantSetMetadata = $variantSetMetadata;

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

    /**
     * @return Collection<int, GWASVariant>
     */
    public function getGWASVariants(): Collection
    {
        return $this->gWASVariants;
    }

    public function addGWASVariant(GWASVariant $gWASVariant): self
    {
        if (!$this->gWASVariants->contains($gWASVariant)) {
            $this->gWASVariants[] = $gWASVariant;
            $gWASVariant->setGwas($this);
        }

        return $this;
    }

    public function removeGWASVariant(GWASVariant $gWASVariant): self
    {
        if ($this->gWASVariants->removeElement($gWASVariant)) {
            // set the owning side to null (unless already changed)
            if ($gWASVariant->getGwas() === $this) {
                $gWASVariant->setGwas(null);
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

    /**
     * @return Collection<int, Study>
     */
    public function getStudyList(): Collection
    {
        return $this->studyList;
    }

    public function addStudyList(Study $studyList): self
    {
        if (!$this->studyList->contains($studyList)) {
            $this->studyList[] = $studyList;
        }

        return $this;
    }

    public function removeStudyList(Study $studyList): self
    {
        $this->studyList->removeElement($studyList);

        return $this;
    }
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AnalyteRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=AnalyteRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"analyte:read"}},
 *      denormalizationContext={"groups"={"analyte:write"}}
 * )
 */
class Analyte
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"analyte:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"analyte:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"analyte:read"})
     */
    private $analyteCode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"analyte:read"})
     */
    private $retentionTime;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"analyte:read"})
     */
    private $massToChargeRatio;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=AnnotationLevel::class, inversedBy="analytes")
     */
    private $annotationLevel;

    /**
     * @ORM\ManyToOne(targetEntity=IdentificationLevel::class, inversedBy="analytes")
     */
    private $identificationLevel;

    /**
     * @ORM\ManyToOne(targetEntity=ObservationVariableMethod::class, inversedBy="analytes")
     */
    private $observationVariableMethod;

    /**
     * @ORM\ManyToOne(targetEntity=AnalyteFlavorHealth::class, inversedBy="analytes")
     */
    private $healthAndFlavor;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="analytes")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Metabolite::class, mappedBy="analyte")
     */
    private $metabolites;

    /**
     * @ORM\ManyToOne(targetEntity=MetaboliteClass::class, inversedBy="analytes")
     */
    private $metaboliteClass;

    public function __construct()
    {
        $this->metabolites = new ArrayCollection();
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

    public function getAnalyteCode(): ?string
    {
        return $this->analyteCode;
    }

    public function setAnalyteCode(string $analyteCode): self
    {
        $this->analyteCode = $analyteCode;

        return $this;
    }

    public function getRetentionTime(): ?string
    {
        return $this->retentionTime;
    }

    public function setRetentionTime(?string $retentionTime): self
    {
        $this->retentionTime = $retentionTime;

        return $this;
    }

    public function getMassToChargeRatio(): ?string
    {
        return $this->massToChargeRatio;
    }

    public function setMassToChargeRatio(?string $massToChargeRatio): self
    {
        $this->massToChargeRatio = $massToChargeRatio;

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

    public function getAnnotationLevel(): ?AnnotationLevel
    {
        return $this->annotationLevel;
    }

    public function setAnnotationLevel(?AnnotationLevel $annotationLevel): self
    {
        $this->annotationLevel = $annotationLevel;

        return $this;
    }

    public function getIdentificationLevel(): ?IdentificationLevel
    {
        return $this->identificationLevel;
    }

    public function setIdentificationLevel(?IdentificationLevel $identificationLevel): self
    {
        $this->identificationLevel = $identificationLevel;

        return $this;
    }

    public function getObservationVariableMethod(): ?ObservationVariableMethod
    {
        return $this->observationVariableMethod;
    }

    public function setObservationVariableMethod(?ObservationVariableMethod $observationVariableMethod): self
    {
        $this->observationVariableMethod = $observationVariableMethod;

        return $this;
    }

    public function getHealthAndFlavor(): ?AnalyteFlavorHealth
    {
        return $this->healthAndFlavor;
    }

    public function setHealthAndFlavor(?AnalyteFlavorHealth $healthAndFlavor): self
    {
        $this->healthAndFlavor = $healthAndFlavor;

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
     * @return Collection<int, Metabolite>
     */
    public function getMetabolites(): Collection
    {
        return $this->metabolites;
    }

    public function addMetabolite(Metabolite $metabolite): self
    {
        if (!$this->metabolites->contains($metabolite)) {
            $this->metabolites[] = $metabolite;
            $metabolite->setAnalyte($this);
        }

        return $this;
    }

    public function removeMetabolite(Metabolite $metabolite): self
    {
        if ($this->metabolites->removeElement($metabolite)) {
            // set the owning side to null (unless already changed)
            if ($metabolite->getAnalyte() === $this) {
                $metabolite->setAnalyte(null);
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

    public function getMetaboliteClass(): ?MetaboliteClass
    {
        return $this->metaboliteClass;
    }

    public function setMetaboliteClass(?MetaboliteClass $metaboliteClass): self
    {
        $this->metaboliteClass = $metaboliteClass;

        return $this;
    }
}

<?php

namespace App\Entity;

use App\Repository\GWASVariantRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GWASVariantRepository::class)
 */
class GWASVariant
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
     * @ORM\Column(type="string", length=255)
     */
    private $alternativeAllele;

    /**
     * @ORM\Column(type="float")
     */
    private $maf;

    /**
     * @ORM\Column(type="integer")
     */
    private $sampleSize;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $snppValue;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $adjustedPValue;

    /**
     * @ORM\Column(type="float")
     */
    private $allelicEffect;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $allelicEffectStat;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $allelicEffectdf;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $allelicEffStdE;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $beta;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $betaStdE;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $oddsRatio;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ciLower;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ciUpper;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $rSquareOfMode;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $rSquareOfModeWithSNP;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $rSquareOfModeWithoutSNP;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $refAllele;

    /**
     * @ORM\ManyToOne(targetEntity=Marker::class, inversedBy="gWASVariants")
     */
    private $marker;

    /**
     * @ORM\ManyToOne(targetEntity=Metabolite::class, inversedBy="gWASVariants")
     */
    private $metabolite;

    /**
     * @ORM\ManyToOne(targetEntity=GWAS::class, inversedBy="gWASVariants")
     */
    private $gwas;

    /**
     * @ORM\ManyToOne(targetEntity=TraitProcessing::class, inversedBy="gWASVariants")
     */
    private $traitPreprocessing;

    /**
     * @ORM\ManyToOne(targetEntity=ObservationVariable::class, inversedBy="gWASVariants")
     */
    private $observationVariable;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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

    public function getAlternativeAllele(): ?string
    {
        return $this->alternativeAllele;
    }

    public function setAlternativeAllele(string $alternativeAllele): self
    {
        $this->alternativeAllele = $alternativeAllele;

        return $this;
    }

    public function getMaf(): ?float
    {
        return $this->maf;
    }

    public function setMaf(float $maf): self
    {
        $this->maf = $maf;

        return $this;
    }

    public function getSampleSize(): ?int
    {
        return $this->sampleSize;
    }

    public function setSampleSize(int $sampleSize): self
    {
        $this->sampleSize = $sampleSize;

        return $this;
    }

    public function getSnppValue(): ?string
    {
        return $this->snppValue;
    }

    public function setSnppValue(string $snppValue): self
    {
        $this->snppValue = $snppValue;

        return $this;
    }

    public function getAdjustedPValue(): ?float
    {
        return $this->adjustedPValue;
    }

    public function setAdjustedPValue(?float $adjustedPValue): self
    {
        $this->adjustedPValue = $adjustedPValue;

        return $this;
    }

    public function getAllelicEffect(): ?float
    {
        return $this->allelicEffect;
    }

    public function setAllelicEffect(float $allelicEffect): self
    {
        $this->allelicEffect = $allelicEffect;

        return $this;
    }

    public function getAllelicEffectStat(): ?float
    {
        return $this->allelicEffectStat;
    }

    public function setAllelicEffectStat(?float $allelicEffectStat): self
    {
        $this->allelicEffectStat = $allelicEffectStat;

        return $this;
    }

    public function getAllelicEffectdf(): ?float
    {
        return $this->allelicEffectdf;
    }

    public function setAllelicEffectdf(?float $allelicEffectdf): self
    {
        $this->allelicEffectdf = $allelicEffectdf;

        return $this;
    }

    public function getAllelicEffStdE(): ?float
    {
        return $this->allelicEffStdE;
    }

    public function setAllelicEffStdE(?float $allelicEffStdE): self
    {
        $this->allelicEffStdE = $allelicEffStdE;

        return $this;
    }

    public function getBeta(): ?float
    {
        return $this->beta;
    }

    public function setBeta(?float $beta): self
    {
        $this->beta = $beta;

        return $this;
    }

    public function getBetaStdE(): ?float
    {
        return $this->betaStdE;
    }

    public function setBetaStdE(?float $betaStdE): self
    {
        $this->betaStdE = $betaStdE;

        return $this;
    }

    public function getOddsRatio(): ?float
    {
        return $this->oddsRatio;
    }

    public function setOddsRatio(?float $oddsRatio): self
    {
        $this->oddsRatio = $oddsRatio;

        return $this;
    }

    public function getCiLower(): ?float
    {
        return $this->ciLower;
    }

    public function setCiLower(?float $ciLower): self
    {
        $this->ciLower = $ciLower;

        return $this;
    }

    public function getCiUpper(): ?float
    {
        return $this->ciUpper;
    }

    public function setCiUpper(?float $ciUpper): self
    {
        $this->ciUpper = $ciUpper;

        return $this;
    }

    public function getRSquareOfMode(): ?float
    {
        return $this->rSquareOfMode;
    }

    public function setRSquareOfMode(?float $rSquareOfMode): self
    {
        $this->rSquareOfMode = $rSquareOfMode;

        return $this;
    }

    public function getRSquareOfModeWithSNP(): ?float
    {
        return $this->rSquareOfModeWithSNP;
    }

    public function setRSquareOfModeWithSNP(?float $rSquareOfModeWithSNP): self
    {
        $this->rSquareOfModeWithSNP = $rSquareOfModeWithSNP;

        return $this;
    }

    public function getRSquareOfModeWithoutSNP(): ?float
    {
        return $this->rSquareOfModeWithoutSNP;
    }

    public function setRSquareOfModeWithoutSNP(?float $rSquareOfModeWithoutSNP): self
    {
        $this->rSquareOfModeWithoutSNP = $rSquareOfModeWithoutSNP;

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

    public function getRefAllele(): ?string
    {
        return $this->refAllele;
    }

    public function setRefAllele(string $refAllele): self
    {
        $this->refAllele = $refAllele;

        return $this;
    }

    public function getMarker(): ?Marker
    {
        return $this->marker;
    }

    public function setMarker(?Marker $marker): self
    {
        $this->marker = $marker;

        return $this;
    }

    public function getMetabolite(): ?Metabolite
    {
        return $this->metabolite;
    }

    public function setMetabolite(?Metabolite $metabolite): self
    {
        $this->metabolite = $metabolite;

        return $this;
    }

    public function getGwas(): ?GWAS
    {
        return $this->gwas;
    }

    public function setGwas(?GWAS $gwas): self
    {
        $this->gwas = $gwas;

        return $this;
    }

    public function getTraitPreprocessing(): ?TraitProcessing
    {
        return $this->traitPreprocessing;
    }

    public function setTraitPreprocessing(?TraitProcessing $traitPreprocessing): self
    {
        $this->traitPreprocessing = $traitPreprocessing;

        return $this;
    }

    public function getObservationVariable(): ?ObservationVariable
    {
        return $this->observationVariable;
    }

    public function setObservationVariable(?ObservationVariable $observationVariable): self
    {
        $this->observationVariable = $observationVariable;

        return $this;
    }

    public function getCreatedBy(): ?string
    {
        return $this->createdBy;
    }

    public function setCreatedBy(?string $createdBy): self
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
}

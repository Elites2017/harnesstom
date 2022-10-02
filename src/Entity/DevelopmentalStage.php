<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\DevelopmentalStageRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=DevelopmentalStageRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"developmental_stage:read"}},
 *      denormalizationContext={"groups"={"developmental_stage:write"}}
 * )
 */
class DevelopmentalStage
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"developmental_stage:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"developmental_stage:read"})
     */
    private $name;

    /**
     * 
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     */
    private $ontology_id;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"developmental_stage:read"})
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="developmentalStages")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Sample::class, mappedBy="developmentalStage")
     */
    private $samples;

    /**
     * @ORM\OneToMany(targetEntity=GermplasmStudyImage::class, mappedBy="developmentStage")
     */
    private $germplasmStudyImages;

    /**
     * @ORM\ManyToOne(targetEntity=DevelopmentalStage::class, inversedBy="developmentalStages")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=DevelopmentalStage::class, mappedBy="parentTerm")
     */
    private $developmentalStages;

    public function __construct()
    {
        $this->samples = new ArrayCollection();
        $this->germplasmStudyImages = new ArrayCollection();
        $this->developmentalStages = new ArrayCollection();
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
     * @return Collection<int, Sample>
     */
    public function getSamples(): Collection
    {
        return $this->samples;
    }

    public function addSample(Sample $sample): self
    {
        if (!$this->samples->contains($sample)) {
            $this->samples[] = $sample;
            $sample->setDevelopmentalStage($this);
        }

        return $this;
    }

    public function removeSample(Sample $sample): self
    {
        if ($this->samples->removeElement($sample)) {
            // set the owning side to null (unless already changed)
            if ($sample->getDevelopmentalStage() === $this) {
                $sample->setDevelopmentalStage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, GermplasmStudyImage>
     */
    public function getGermplasmStudyImages(): Collection
    {
        return $this->germplasmStudyImages;
    }

    public function addGermplasmStudyImage(GermplasmStudyImage $germplasmStudyImage): self
    {
        if (!$this->germplasmStudyImages->contains($germplasmStudyImage)) {
            $this->germplasmStudyImages[] = $germplasmStudyImage;
            $germplasmStudyImage->setDevelopmentStage($this);
        }

        return $this;
    }

    public function removeGermplasmStudyImage(GermplasmStudyImage $germplasmStudyImage): self
    {
        if ($this->germplasmStudyImages->removeElement($germplasmStudyImage)) {
            // set the owning side to null (unless already changed)
            if ($germplasmStudyImage->getDevelopmentStage() === $this) {
                $germplasmStudyImage->setDevelopmentStage(null);
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
    public function getDevelopmentalStages(): Collection
    {
        return $this->developmentalStages;
    }

    public function addDevelopmentalStage(self $developmentalStage): self
    {
        if (!$this->developmentalStages->contains($developmentalStage)) {
            $this->developmentalStages[] = $developmentalStage;
            $developmentalStage->setParentTerm($this);
        }

        return $this;
    }

    public function removeDevelopmentalStage(self $developmentalStage): self
    {
        if ($this->developmentalStages->removeElement($developmentalStage)) {
            // set the owning side to null (unless already changed)
            if ($developmentalStage->getParentTerm() === $this) {
                $developmentalStage->setParentTerm(null);
            }
        }

        return $this;
    }
}

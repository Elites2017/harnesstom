<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\FactorTypeRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=FactorTypeRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"factor_type:read"}},
 *      denormalizationContext={"groups"={"factor_type:write"}}
 * )
 */
class FactorType
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"factor_type:read", "study:read"})
     * @SerializedName("parameterDbId")
     */
    private $id;

    /**
     * 
     * @ORM\Column(type="string", length=255, unique=true, nullable=false)
     * * @Groups({"factor_type:read", "study:read"})
     */
    private $ontology_id;

    /**
     * @ORM\Column(type="text")
     * @Groups({"factor_type:read", "study:read"})
     * @SerializedName("parameterName")
     */
    private $name;

    /**
     * @ORM\Column(type="text", nullable=true)
     * @Groups({"factor_type:read", "study:read"})
     * @SerializedName("description")
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="factorTypes")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Parameter::class, mappedBy="factorType")
     */
    private $parameters;

    /**
     * @ORM\OneToMany(targetEntity=Study::class, mappedBy="factor")
     */
    private $studies;

    /**
     * @ORM\OneToMany(targetEntity=GermplasmStudyImage::class, mappedBy="factor")
     */
    private $germplasmStudyImages;

    /**
     * @ORM\ManyToMany(targetEntity=FactorType::class, inversedBy="factorTypes")
     */
    private $parentTerm;

    /**
     * @ORM\ManyToMany(targetEntity=FactorType::class, mappedBy="parentTerm")
     */
    private $factorTypes;

    public function __construct()
    {
        $this->parameters = new ArrayCollection();
        $this->studies = new ArrayCollection();
        $this->germplasmStudyImages = new ArrayCollection();
        $this->factorTypes = new ArrayCollection();
        $this->parentTerm = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
     * @return Collection<int, Parameter>
     */
    public function getParameters(): Collection
    {
        return $this->parameters;
    }

    public function addParameter(Parameter $parameter): self
    {
        if (!$this->parameters->contains($parameter)) {
            $this->parameters[] = $parameter;
            $parameter->setFactorType($this);
        }

        return $this;
    }

    public function removeParameter(Parameter $parameter): self
    {
        if ($this->parameters->removeElement($parameter)) {
            // set the owning side to null (unless already changed)
            if ($parameter->getFactorType() === $this) {
                $parameter->setFactorType(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Study>
     */
    public function getStudies(): Collection
    {
        return $this->studies;
    }

    public function addStudy(Study $study): self
    {
        if (!$this->studies->contains($study)) {
            $this->studies[] = $study;
            $study->setFactor($this);
        }

        return $this;
    }

    public function removeStudy(Study $study): self
    {
        if ($this->studies->removeElement($study)) {
            // set the owning side to null (unless already changed)
            if ($study->getFactor() === $this) {
                $study->setFactor(null);
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
            $germplasmStudyImage->setFactor($this);
        }

        return $this;
    }

    public function removeGermplasmStudyImage(GermplasmStudyImage $germplasmStudyImage): self
    {
        if ($this->germplasmStudyImages->removeElement($germplasmStudyImage)) {
            // set the owning side to null (unless already changed)
            if ($germplasmStudyImage->getFactor() === $this) {
                $germplasmStudyImage->setFactor(null);
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
     * @return Collection<int, self>
     */
    public function getParentTerm(): Collection
    {
        return $this->parentTerm;
    }

    public function addParentTerm(self $parentTerm): self
    {
        if (!$this->parentTerm->contains($parentTerm)) {
            $this->parentTerm[] = $parentTerm;
        }

        return $this;
    }

    public function removeParentTerm(self $parentTerm): self
    {
        $this->parentTerm->removeElement($parentTerm);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getFactorTypes(): Collection
    {
        return $this->factorTypes;
    }

    public function addFactorType(self $factorType): self
    {
        if (!$this->factorTypes->contains($factorType)) {
            $this->factorTypes[] = $factorType;
            $factorType->addParentTerm($this);
        }

        return $this;
    }

    public function removeFactorType(self $factorType): self
    {
        if ($this->factorTypes->removeElement($factorType)) {
            $factorType->removeParentTerm($this);
        }

        return $this;
    }

}

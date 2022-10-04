<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use App\Repository\AnalyteFlavorHealthRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=AnalyteFlavorHealthRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"analyte_f_h:read"}},
 *      denormalizationContext={"groups"={"analyte_f_h:write"}}
 * )
 */
class AnalyteFlavorHealth
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"analyte_f_h:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"analyte_f_h:read"})
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
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="analyteFlavorHealths")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Analyte::class, mappedBy="healthAndFlavor")
     */
    private $analytes;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ontologyId;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @ORM\ManyToOne(targetEntity=AnalyteFlavorHealth::class, inversedBy="analyteFlavorHealths")
     */
    private $parentTerm;

    /**
     * @ORM\OneToMany(targetEntity=AnalyteFlavorHealth::class, mappedBy="parentTerm")
     */
    private $analyteFlavorHealths;

    public function __construct()
    {
        $this->analytes = new ArrayCollection();
        $this->analyteFlavorHealths = new ArrayCollection();
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
     * @return Collection<int, Analyte>
     */
    public function getAnalytes(): Collection
    {
        return $this->analytes;
    }

    public function addAnalyte(Analyte $analyte): self
    {
        if (!$this->analytes->contains($analyte)) {
            $this->analytes[] = $analyte;
            $analyte->setHealthAndFlavor($this);
        }

        return $this;
    }

    public function removeAnalyte(Analyte $analyte): self
    {
        if ($this->analytes->removeElement($analyte)) {
            // set the owning side to null (unless already changed)
            if ($analyte->getHealthAndFlavor() === $this) {
                $analyte->setHealthAndFlavor(null);
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

    public function getOntologyId(): ?string
    {
        return $this->ontologyId;
    }

    public function setOntologyId(string $ontologyId): self
    {
        $this->ontologyId = $ontologyId;

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
    public function getAnalyteFlavorHealths(): Collection
    {
        return $this->analyteFlavorHealths;
    }

    public function addAnalyteFlavorHealth(self $analyteFlavorHealth): self
    {
        if (!$this->analyteFlavorHealths->contains($analyteFlavorHealth)) {
            $this->analyteFlavorHealths[] = $analyteFlavorHealth;
            $analyteFlavorHealth->setParentTerm($this);
        }

        return $this;
    }

    public function removeAnalyteFlavorHealth(self $analyteFlavorHealth): self
    {
        if ($this->analyteFlavorHealths->removeElement($analyteFlavorHealth)) {
            // set the owning side to null (unless already changed)
            if ($analyteFlavorHealth->getParentTerm() === $this) {
                $analyteFlavorHealth->setParentTerm(null);
            }
        }

        return $this;
    }
}

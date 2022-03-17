<?php

namespace App\Entity;

use App\Repository\MetaboliteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MetaboliteRepository::class)
 */
class Metabolite
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Analyte::class, inversedBy="metabolites")
     */
    private $analyte;

    /**
     * @ORM\ManyToOne(targetEntity=MetabolicTrait::class, inversedBy="metabolites")
     */
    private $metabolicTrait;

    /**
     * @ORM\ManyToOne(targetEntity=Scale::class, inversedBy="metabolites")
     */
    private $scale;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="metabolites")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=GWASVariant::class, mappedBy="metabolite")
     */
    private $gWASVariants;

    /**
     * @ORM\OneToMany(targetEntity=MetaboliteValue::class, mappedBy="metabolite")
     */
    private $metaboliteValues;

    /**
     * @ORM\OneToMany(targetEntity=QTLVariant::class, mappedBy="metabolite")
     */
    private $qTLVariants;

    public function __construct()
    {
        $this->gWASVariants = new ArrayCollection();
        $this->metaboliteValues = new ArrayCollection();
        $this->qTLVariants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAnalyte(): ?Analyte
    {
        return $this->analyte;
    }

    public function setAnalyte(?Analyte $analyte): self
    {
        $this->analyte = $analyte;

        return $this;
    }

    public function getMetabolicTrait(): ?MetabolicTrait
    {
        return $this->metabolicTrait;
    }

    public function setMetabolicTrait(?MetabolicTrait $metabolicTrait): self
    {
        $this->metabolicTrait = $metabolicTrait;

        return $this;
    }

    public function getScale(): ?Scale
    {
        return $this->scale;
    }

    public function setScale(?Scale $scale): self
    {
        $this->scale = $scale;

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
            $gWASVariant->setMetabolite($this);
        }

        return $this;
    }

    public function removeGWASVariant(GWASVariant $gWASVariant): self
    {
        if ($this->gWASVariants->removeElement($gWASVariant)) {
            // set the owning side to null (unless already changed)
            if ($gWASVariant->getMetabolite() === $this) {
                $gWASVariant->setMetabolite(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, MetaboliteValue>
     */
    public function getMetaboliteValues(): Collection
    {
        return $this->metaboliteValues;
    }

    public function addMetaboliteValue(MetaboliteValue $metaboliteValue): self
    {
        if (!$this->metaboliteValues->contains($metaboliteValue)) {
            $this->metaboliteValues[] = $metaboliteValue;
            $metaboliteValue->setMetabolite($this);
        }

        return $this;
    }

    public function removeMetaboliteValue(MetaboliteValue $metaboliteValue): self
    {
        if ($this->metaboliteValues->removeElement($metaboliteValue)) {
            // set the owning side to null (unless already changed)
            if ($metaboliteValue->getMetabolite() === $this) {
                $metaboliteValue->setMetabolite(null);
            }
        }

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
            $qTLVariant->setMetabolite($this);
        }

        return $this;
    }

    public function removeQTLVariant(QTLVariant $qTLVariant): self
    {
        if ($this->qTLVariants->removeElement($qTLVariant)) {
            // set the owning side to null (unless already changed)
            if ($qTLVariant->getMetabolite() === $this) {
                $qTLVariant->setMetabolite(null);
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
}

<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\SequencingTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=SequencingTypeRepository::class)
 * @ApiResource
 */
class SequencingType
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
    private $label;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="sequencingTypes")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=GenotypingPlatform::class, mappedBy="sequencingType")
     */
    private $genotypingPlatforms;

    public function __construct()
    {
        $this->genotypingPlatforms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLabel(): ?string
    {
        return $this->label;
    }

    public function setLabel(string $label): self
    {
        $this->label = $label;

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
     * @return Collection<int, GenotypingPlatform>
     */
    public function getGenotypingPlatforms(): Collection
    {
        return $this->genotypingPlatforms;
    }

    public function addGenotypingPlatform(GenotypingPlatform $genotypingPlatform): self
    {
        if (!$this->genotypingPlatforms->contains($genotypingPlatform)) {
            $this->genotypingPlatforms[] = $genotypingPlatform;
            $genotypingPlatform->setSequencingType($this);
        }

        return $this;
    }

    public function removeGenotypingPlatform(GenotypingPlatform $genotypingPlatform): self
    {
        if ($this->genotypingPlatforms->removeElement($genotypingPlatform)) {
            // set the owning side to null (unless already changed)
            if ($genotypingPlatform->getSequencingType() === $this) {
                $genotypingPlatform->setSequencingType(null);
            }
        }

        return $this;
    }

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->label;
    }
}

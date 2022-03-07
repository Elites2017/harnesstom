<?php

namespace App\Entity;

use App\Repository\VarCallSoftwareRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=VarCallSoftwareRepository::class)
 */
class VarCallSoftware
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
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="varCallSoftware")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=GenotypingPlatform::class, mappedBy="varCallSoftware")
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
            $genotypingPlatform->setVarCallSoftware($this);
        }

        return $this;
    }

    public function removeGenotypingPlatform(GenotypingPlatform $genotypingPlatform): self
    {
        if ($this->genotypingPlatforms->removeElement($genotypingPlatform)) {
            // set the owning side to null (unless already changed)
            if ($genotypingPlatform->getVarCallSoftware() === $this) {
                $genotypingPlatform->setVarCallSoftware(null);
            }
        }

        return $this;
    }
}

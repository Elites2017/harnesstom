<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\MLSStatusRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=MLSStatusRepository::class)
 * @ApiResource
 */
class MLSStatus
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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $code;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="mLSStatuses")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Accession::class, mappedBy="mlsStatus")
     */
    private $accessions;

    public function __construct()
    {
        $this->accessions = new ArrayCollection();
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

    public function getCode(): ?string
    {
        return $this->code;
    }

    public function setCode(?string $code): self
    {
        $this->code = $code;

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

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->label;
    }

    /**
     * @return Collection<int, Accession>
     */
    public function getAccessions(): Collection
    {
        return $this->accessions;
    }

    public function addAccession(Accession $accession): self
    {
        if (!$this->accessions->contains($accession)) {
            $this->accessions[] = $accession;
            $accession->setMlsStatus($this);
        }

        return $this;
    }

    public function removeAccession(Accession $accession): self
    {
        if ($this->accessions->removeElement($accession)) {
            // set the owning side to null (unless already changed)
            if ($accession->getMlsStatus() === $this) {
                $accession->setMlsStatus(null);
            }
        }

        return $this;
    }
}

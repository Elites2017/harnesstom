<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\SynonymRepository;
use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=SynonymRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"synonym:read"}},
 *      denormalizationContext={"groups"={"synonym:write"}}
 * )
 */
class Synonym
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"synonym:read", "accession:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=Accession::class, inversedBy="synonyms")
     */
    private $accession;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="synonyms")
     */
    private $createdBy;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"synonym:read", "accession:read"})
     */
    private $synonymSource;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"synonym:read", "accession:read"})
     */
    private $synonymId;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getAccession(): ?Accession
    {
        return $this->accession;
    }

    public function setAccession(?Accession $accession): self
    {
        $this->accession = $accession;

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

    public function getSynonymSource(): ?string
    {
        return $this->synonymSource;
    }

    public function setSynonymSource(string $synonymSource): self
    {
        $this->synonymSource = $synonymSource;

        return $this;
    }

    public function getSynonymId(): ?string
    {
        return $this->synonymId;
    }

    public function setSynonymId(string $synonymId): self
    {
        $this->synonymId = $synonymId;

        return $this;
    }
}

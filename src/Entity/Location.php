<?php

namespace App\Entity;

use App\Repository\LocationRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LocationRepository::class)
 */
class Location
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
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     */
    private $longitudeCo;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     */
    private $latitudeCo;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     */
    private $altitudeCo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $siteStatus;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="locations")
     */
    private $createdBy;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class, inversedBy="locations")
     */
    private $country;

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

    public function getLongitudeCo(): ?string
    {
        return $this->longitudeCo;
    }

    public function setLongitudeCo(?string $longitudeCo): self
    {
        $this->longitudeCo = $longitudeCo;

        return $this;
    }

    public function getLatitudeCo(): ?string
    {
        return $this->latitudeCo;
    }

    public function setLatitudeCo(?string $latitudeCo): self
    {
        $this->latitudeCo = $latitudeCo;

        return $this;
    }

    public function getAltitudeCo(): ?string
    {
        return $this->altitudeCo;
    }

    public function setAltitudeCo(?string $altitudeCo): self
    {
        $this->altitudeCo = $altitudeCo;

        return $this;
    }

    public function getSiteStatus(): ?string
    {
        return $this->siteStatus;
    }

    public function setSiteStatus(?string $siteStatus): self
    {
        $this->siteStatus = $siteStatus;

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

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }
}

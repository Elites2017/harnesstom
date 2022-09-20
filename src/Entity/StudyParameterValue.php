<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\StudyParameterValueRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=StudyParameterValueRepository::class)
 * @ApiResource(
 *      normalizationContext={"groups"={"study_p_v:read"}},
 *      denormalizationContext={"groups"={"study_p_v:write"}}
 * )
 */
class StudyParameterValue
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"study_p_v:read", "study:read"})
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Parameter::class, inversedBy="studyParameterValues")
     * @ORM\JoinColumn(nullable=false)
     */
    private $parameter;

    /**
     * @ORM\OneToOne(targetEntity=Study::class, inversedBy="studyParameterValue", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $study;

    /**
     * @ORM\Column(type="float")
     * @Groups({"study_p_v:read", "study:read"})
     */
    private $value;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="studyParameterValues")
     */
    private $createdBy;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getParameter(): ?Parameter
    {
        return $this->parameter;
    }

    public function setParameter(?Parameter $parameter): self
    {
        $this->parameter = $parameter;

        return $this;
    }

    public function getStudy(): ?Study
    {
        return $this->study;
    }

    public function setStudy(Study $study): self
    {
        $this->study = $study;

        return $this;
    }

    public function getValue(): ?float
    {
        return $this->value;
    }

    public function setValue(float $value): self
    {
        $this->value = $value;

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
}

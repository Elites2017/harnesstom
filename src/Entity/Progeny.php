<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Repository\ProgenyRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ApiResource()
 * @ORM\Entity(repositoryClass=ProgenyRepository::class)
 */
class Progeny
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $progenyId;

    /**
     * @ORM\ManyToOne(targetEntity=Cross::class, inversedBy="progenies")
     */
    private $progenyCross;

    /**
     * @ORM\ManyToOne(targetEntity=Germplasm::class, inversedBy="progenies")
     */
    private $progenyParent1;

    /**
     * @ORM\ManyToOne(targetEntity=Germplasm::class, inversedBy="progenies")
     */
    private $progenyParent2;

    /**
     * @ORM\OneToOne(targetEntity=Germplasm::class, inversedBy="progeny", cascade={"persist", "remove"})
     */
    private $pedigreeGermplasm;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProgenyId(): ?string
    {
        return $this->progenyId;
    }

    public function setProgenyId(?string $progenyId): self
    {
        $this->progenyId = $progenyId;

        return $this;
    }

    public function getProgenyCross(): ?Cross
    {
        return $this->progenyCross;
    }

    public function setProgenyCross(?Cross $progenyCross): self
    {
        $this->progenyCross = $progenyCross;

        return $this;
    }

    public function getProgenyParent1(): ?Germplasm
    {
        return $this->progenyParent1;
    }

    public function setProgenyParent1(?Germplasm $progenyParent1): self
    {
        $this->progenyParent1 = $progenyParent1;

        return $this;
    }

    public function getProgenyParent2(): ?Germplasm
    {
        return $this->progenyParent2;
    }

    public function setProgenyParent2(?Germplasm $progenyParent2): self
    {
        $this->progenyParent2 = $progenyParent2;

        return $this;
    }

    public function getPedigreeGermplasm(): ?Germplasm
    {
        return $this->pedigreeGermplasm;
    }

    public function setPedigreeGermplasm(?Germplasm $pedigreeGermplasm): self
    {
        $this->pedigreeGermplasm = $pedigreeGermplasm;

        return $this;
    }
}

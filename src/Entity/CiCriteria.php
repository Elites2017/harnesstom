<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\CiCriteriaRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=CiCriteriaRepository::class)
 * @ApiResource(attributes={
 *  "normalizationContext"={"groups"={"ci_criteria:read"}, "enable_max_depth"=true},
 *  "denormalizationContext"={"groups"={"ci_criteria:write"}}
 * })
 */
class CiCriteria
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolic_trait:read", "metabolite_class:read", "gwas:read", "gwas_model:read", "qtl_study:ready", "observation_level:read",
     * "germplasm_s_i:read", "genotyping_platform:read", "user:read", "person:read", "enzyme:read", "factor_type:read", "season:read",
     * "location:read", "growth_f_t:read", "experimental_d_t:read", "study_p_v:read", "study_image:read", "study_g_i:read", "crop:read",
     * "biological_status:read", "analyte:read", "analyte_class:read", "allelic_e_e:read", "analyte_f_h:read", "anatomical_entity:read",
     * "annotation_level:read", "attribute:read", "attribute_category:read", "attribute_t_v:read", "breeding_method:read", "ci_criteria:read",
     * "collecting_mission:read", "collecting_source:read", "contact:read", "genetic_t_m:read", "gwas_stat_test:read", "gwas_variant:read",
     * "identifcation_level:read", "metabolite_value", "method_class:read", "observation_value:read", "qtl_method:read", "qtl_statistic:read",
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read", "taxonomy:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolic_trait:read", "metabolite_class:read", "gwas:read", "gwas_model:read", "qtl_study:ready", "observation_level:read",
     * "germplasm_s_i:read", "genotyping_platform:read", "user:read", "person:read", "enzyme:read", "factor_type:read", "season:read",
     * "location:read", "growth_f_t:read", "experimental_d_t:read", "study_p_v:read", "study_image:read", "study_g_i:read", "crop:read",
     * "biological_status:read", "analyte:read", "analyte_class:read", "allelic_e_e:read", "analyte_f_h:read", "anatomical_entity:read",
     * "annotation_level:read", "attribute:read", "attribute_category:read", "attribute_t_v:read", "breeding_method:read", "ci_criteria:read",
     * "collecting_mission:read", "collecting_source:read", "contact:read", "genetic_t_m:read", "gwas_stat_test:read", "gwas_variant:read",
     * "identifcation_level:read", "metabolite_value", "method_class:read", "observation_value:read", "qtl_method:read", "qtl_statistic:read",
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read", "taxonomy:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $ontology_id;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $parentTerm;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="ciCriterias")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=QTLStudy::class, mappedBy="ciCriteria")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolic_trait:read", "metabolite_class:read", "gwas:read", "gwas_model:read", "observation_level:read",
     * "germplasm_s_i:read", "genotyping_platform:read", "user:read", "person:read", "enzyme:read", "factor_type:read", "season:read",
     * "location:read", "growth_f_t:read", "experimental_d_t:read", "study_p_v:read", "study_image:read", "study_g_i:read", "crop:read",
     * "biological_status:read", "analyte:read", "analyte_class:read", "allelic_e_e:read", "analyte_f_h:read", "anatomical_entity:read",
     * "annotation_level:read", "attribute:read", "attribute_category:read", "attribute_t_v:read", "breeding_method:read", "ci_criteria:read",
     * "collecting_mission:read", "collecting_source:read", "contact:read", "genetic_t_m:read", "gwas_stat_test:read", "gwas_variant:read",
     * "identifcation_level:read", "metabolite_value", "method_class:read", "observation_value:read", "qtl_method:read", "qtl_statistic:read",
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read", "taxonomy:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $qTLStudies;

    public function __construct()
    {
        $this->qTLStudies = new ArrayCollection();
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

    public function getOntologyId(): ?string
    {
        return $this->ontology_id;
    }

    public function setOntologyId(string $ontology_id): self
    {
        $this->ontology_id = $ontology_id;

        return $this;
    }

    public function getParentTerm(): ?string
    {
        return $this->parentTerm;
    }

    public function setParentTerm(?string $parentTerm): self
    {
        $this->parentTerm = $parentTerm;

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
     * @return Collection<int, QTLStudy>
     */
    public function getQTLStudies(): Collection
    {
        return $this->qTLStudies;
    }

    public function addQTLStudy(QTLStudy $qTLStudy): self
    {
        if (!$this->qTLStudies->contains($qTLStudy)) {
            $this->qTLStudies[] = $qTLStudy;
            $qTLStudy->setCiCriteria($this);
        }

        return $this;
    }

    public function removeQTLStudy(QTLStudy $qTLStudy): self
    {
        if ($this->qTLStudies->removeElement($qTLStudy)) {
            // set the owning side to null (unless already changed)
            if ($qTLStudy->getCiCriteria() === $this) {
                $qTLStudy->setCiCriteria(null);
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

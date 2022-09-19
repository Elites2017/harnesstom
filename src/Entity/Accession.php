<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\AccessionRepository;
use Doctrine\Common\Collections\Collection;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=AccessionRepository::class)
 * @ApiResource(attributes={
 *  "normalizationContext"={"groups"={"accession:read"}, "enable_max_depth"=true},
 *  "denormalizationContext"={"groups"={"accession:write"}}
 * })
 */
class Accession
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
    private $accenumb;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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
    private $accename;

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
    private $puid;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class, inversedBy="accessions")
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
    private $origcty;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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
    private $origmuni;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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
    private $origadmin1;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
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
    private $origadmin2;

    /**
     * @ORM\ManyToOne(targetEntity=CollectingSource::class, inversedBy="accessions")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolic_trait:read", "metabolite_class:read", "gwas:read", "gwas_model:read", "qtl_study:ready", "observation_level:read",
     * "germplasm_s_i:read", "genotyping_platform:read", "user:read", "person:read", "enzyme:read", "factor_type:read", "season:read",
     * "location:read", "growth_f_t:read", "experimental_d_t:read", "study_p_v:read", "study_image:read", "study_g_i:read", "crop:read",
     * "biological_status:read", "analyte:read", "analyte_class:read", "allelic_e_e:read", "analyte_f_h:read", "anatomical_entity:read",
     * "annotation_level:read", "attribute:read", "attribute_category:read", "attribute_t_v:read", "breeding_method:read", "ci_criteria:read",
     * "collecting_mission:read", "contact:read", "genetic_t_m:read", "gwas_stat_test:read", "gwas_variant:read",
     * "identifcation_level:read", "metabolite_value", "method_class:read", "observation_value:read", "qtl_method:read", "qtl_statistic:read",
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read", "taxonomy:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $collsrc;

    /**
     * @ORM\ManyToOne(targetEntity=BiologicalStatus::class, inversedBy="accessions")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "institute:read", "kinship_algorithm:read", "trial:read",
     * "metabolic_trait:read", "metabolite_class:read", "gwas:read", "gwas_model:read", "qtl_study:ready", "observation_level:read",
     * "germplasm_s_i:read", "genotyping_platform:read", "user:read", "person:read", "enzyme:read", "factor_type:read", "season:read",
     * "location:read", "growth_f_t:read", "experimental_d_t:read", "study_p_v:read", "study_image:read", "study_g_i:read", "crop:read",
     * "analyte:read", "analyte_class:read", "allelic_e_e:read", "analyte_f_h:read", "anatomical_entity:read",
     * "annotation_level:read", "attribute:read", "attribute_category:read", "attribute_t_v:read", "breeding_method:read", "ci_criteria:read",
     * "collecting_mission:read", "collecting_source:read", "contact:read", "genetic_t_m:read", "gwas_stat_test:read", "gwas_variant:read",
     * "identifcation_level:read", "metabolite_value", "method_class:read", "observation_value:read", "qtl_method:read", "qtl_statistic:read",
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read", "taxonomy:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $sampstat;

    /**
     * @ORM\ManyToOne(targetEntity=Taxonomy::class, inversedBy="accessions")
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
     * "qtl_e_e:read", "qtl_variant:read", "scale:read", "scale_category:read", "software:read", "sequencing_type:read",
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $taxon;

    /**
     * @ORM\ManyToOne(targetEntity=Institute::class, inversedBy="accessions")
     * @Groups({"mls_status:read", "marker:read", "mapping_population:read", "country:read", "data_type:read", "study:read",
     * "metabolite:read", "observation_variable:read", "observation_v_m:read", "parameter:read", "germplasm:read", "pedigree:read",
     * "program:read", "accession:read", "cross:read", "sample:read", "kinship_algorithm:read", "trial:read",
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
    private $instcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $maintainernumb;

    /**
     * @ORM\Column(type="date", nullable=true)
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
    private $acqdate;

    /**
     * @ORM\ManyToOne(targetEntity=StorageType::class, inversedBy="accessions")
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
     * "sequencing_instrument:read", "structure_method:read", "synonym:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $storage;

    /**
     * @ORM\ManyToOne(targetEntity=Institute::class, inversedBy="accessions")
     */
    private $donorcode;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $donornumb;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $collnumb;

    /**
     * @ORM\ManyToOne(targetEntity=Institute::class, inversedBy="accessions")
     */
    private $collcode;

    /**
     * @ORM\ManyToOne(targetEntity=CollectingMission::class, inversedBy="accessions")
     */
    private $collmissid;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $colldate;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     */
    private $declatitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     */
    private $declongitude;

    /**
     * @ORM\Column(type="decimal", precision=10, scale=6, nullable=true)
     */
    private $elevation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $collsite;

    /**
     * @ORM\ManyToOne(targetEntity=Institute::class, inversedBy="accessions")
     */
    private $bredcode;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isActive;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="accessions")
     */
    private $createdBy;

    /**
     * @ORM\OneToMany(targetEntity=Synonym::class, mappedBy="accession")
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
     * "sequencing_instrument:read", "storage_type:read", "structure_method:read", "threshold_method:read", "unit:read",
     * "trait_class:read", "trait_processing:read", "trial_type:read", "var_c_s:read", "variant_set:read", "variant_s_m:read"})
     */
    private $synonyms;

    /**
     * @ORM\OneToMany(targetEntity=AttributeTraitValue::class, mappedBy="accession")
     */
    private $attributeTraitValues;

    /**
     * @ORM\OneToMany(targetEntity=Germplasm::class, mappedBy="accession")
     */
    private $germplasms;

    /**
     * @ORM\ManyToOne(targetEntity=MLSStatus::class, inversedBy="accessions")
     */
    private $mlsStatus;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $breedingInfo;

    public function __construct()
    {
        $this->synonyms = new ArrayCollection();
        $this->attributeTraitValues = new ArrayCollection();
        $this->germplasms = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAccenumb(): ?string
    {
        return $this->accenumb;
    }

    public function setAccenumb(string $accenumb): self
    {
        $this->accenumb = $accenumb;

        return $this;
    }

    public function getAccename(): ?string
    {
        return $this->accename;
    }

    public function setAccename(?string $accename): self
    {
        $this->accename = $accename;

        return $this;
    }

    public function getPuid(): ?string
    {
        return $this->puid;
    }

    public function setPuid(string $puid): self
    {
        $this->puid = $puid;

        return $this;
    }

    public function getOrigcty(): ?Country
    {
        return $this->origcty;
    }

    public function setOrigcty(?Country $origcty): self
    {
        $this->origcty = $origcty;

        return $this;
    }

    public function getOrigmuni(): ?string
    {
        return $this->origmuni;
    }

    public function setOrigmuni(?string $origmuni): self
    {
        $this->origmuni = $origmuni;

        return $this;
    }

    public function getOrigadmin1(): ?string
    {
        return $this->origadmin1;
    }

    public function setOrigadmin1(?string $origadmin1): self
    {
        $this->origadmin1 = $origadmin1;

        return $this;
    }

    public function getOrigadmin2(): ?string
    {
        return $this->origadmin2;
    }

    public function setOrigadmin2(?string $origadmin2): self
    {
        $this->origadmin2 = $origadmin2;

        return $this;
    }

    public function getCollsrc(): ?CollectingSource
    {
        return $this->collsrc;
    }

    public function setCollsrc(?CollectingSource $collsrc): self
    {
        $this->collsrc = $collsrc;

        return $this;
    }

    public function getSampstat(): ?BiologicalStatus
    {
        return $this->sampstat;
    }

    public function setSampstat(?BiologicalStatus $sampstat): self
    {
        $this->sampstat = $sampstat;

        return $this;
    }

    public function getTaxon(): ?Taxonomy
    {
        return $this->taxon;
    }

    public function setTaxon(?Taxonomy $taxon): self
    {
        $this->taxon = $taxon;

        return $this;
    }

    public function getInstcode(): ?Institute
    {
        return $this->instcode;
    }

    public function setInstcode(?Institute $instcode): self
    {
        $this->instcode = $instcode;

        return $this;
    }

    public function getMaintainernumb(): ?string
    {
        return $this->maintainernumb;
    }

    public function setMaintainernumb(string $maintainernumb): self
    {
        $this->maintainernumb = $maintainernumb;

        return $this;
    }

    public function getAcqdate(): ?\DateTimeInterface
    {
        return $this->acqdate;
    }

    public function setAcqdate(?\DateTimeInterface $acqdate): self
    {
        $this->acqdate = $acqdate;

        return $this;
    }

    public function getStorage(): ?StorageType
    {
        return $this->storage;
    }

    public function setStorage(?StorageType $storage): self
    {
        $this->storage = $storage;

        return $this;
    }

    public function getDonorcode(): ?Institute
    {
        return $this->donorcode;
    }

    public function setDonorcode(?Institute $donorcode): self
    {
        $this->donorcode = $donorcode;

        return $this;
    }

    public function getDonornumb(): ?string
    {
        return $this->donornumb;
    }

    public function setDonornumb(string $donornumb): self
    {
        $this->donornumb = $donornumb;

        return $this;
    }

    public function getCollnumb(): ?string
    {
        return $this->collnumb;
    }

    public function setCollnumb(?string $collnumb): self
    {
        $this->collnumb = $collnumb;

        return $this;
    }

    public function getCollcode(): ?Institute
    {
        return $this->collcode;
    }

    public function setCollcode(?Institute $collcode): self
    {
        $this->collcode = $collcode;

        return $this;
    }

    public function getCollmissid(): ?CollectingMission
    {
        return $this->collmissid;
    }

    public function setCollmissid(?CollectingMission $collmissid): self
    {
        $this->collmissid = $collmissid;

        return $this;
    }

    public function getColldate(): ?\DateTimeInterface
    {
        return $this->colldate;
    }

    public function setColldate(?\DateTimeInterface $colldate): self
    {
        $this->colldate = $colldate;

        return $this;
    }

    public function getDeclatitude(): ?string
    {
        return $this->declatitude;
    }

    public function setDeclatitude(?string $declatitude): self
    {
        $this->declatitude = $declatitude;

        return $this;
    }

    public function getDeclongitude(): ?string
    {
        return $this->declongitude;
    }

    public function setDeclongitude(?string $declongitude): self
    {
        $this->declongitude = $declongitude;

        return $this;
    }

    public function getElevation(): ?string
    {
        return $this->elevation;
    }

    public function setElevation(?string $elevation): self
    {
        $this->elevation = $elevation;

        return $this;
    }

    public function getCollsite(): ?string
    {
        return $this->collsite;
    }

    public function setCollsite(?string $collsite): self
    {
        $this->collsite = $collsite;

        return $this;
    }

    public function getBredcode(): ?Institute
    {
        return $this->bredcode;
    }

    public function setBredcode(?Institute $bredcode): self
    {
        $this->bredcode = $bredcode;

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
     * @return Collection<int, Synonym>
     */
    public function getSynonyms(): Collection
    {
        return $this->synonyms;
    }

    public function addSynonym(Synonym $synonym): self
    {
        if (!$this->synonyms->contains($synonym)) {
            $this->synonyms[] = $synonym;
            $synonym->setAccession($this);
        }

        return $this;
    }

    public function removeSynonym(Synonym $synonym): self
    {
        if ($this->synonyms->removeElement($synonym)) {
            // set the owning side to null (unless already changed)
            if ($synonym->getAccession() === $this) {
                $synonym->setAccession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, AttributeTraitValue>
     */
    public function getAttributeTraitValues(): Collection
    {
        return $this->attributeTraitValues;
    }

    public function addAttributeTraitValue(AttributeTraitValue $attributeTraitValue): self
    {
        if (!$this->attributeTraitValues->contains($attributeTraitValue)) {
            $this->attributeTraitValues[] = $attributeTraitValue;
            $attributeTraitValue->setAccession($this);
        }

        return $this;
    }

    public function removeAttributeTraitValue(AttributeTraitValue $attributeTraitValue): self
    {
        if ($this->attributeTraitValues->removeElement($attributeTraitValue)) {
            // set the owning side to null (unless already changed)
            if ($attributeTraitValue->getAccession() === $this) {
                $attributeTraitValue->setAccession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Germplasm>
     */
    public function getGermplasms(): Collection
    {
        return $this->germplasms;
    }

    public function addGermplasm(Germplasm $germplasm): self
    {
        if (!$this->germplasms->contains($germplasm)) {
            $this->germplasms[] = $germplasm;
            $germplasm->setAccession($this);
        }

        return $this;
    }

    public function removeGermplasm(Germplasm $germplasm): self
    {
        if ($this->germplasms->removeElement($germplasm)) {
            // set the owning side to null (unless already changed)
            if ($germplasm->getAccession() === $this) {
                $germplasm->setAccession(null);
            }
        }

        return $this;
    }

    // create a toString method to return the object name / code which will appear
    // in an upper level related form field from a foreign key
    public function __toString()
    {
        return (string) $this->accenumb;
    }

    public function getMlsStatus(): ?MLSStatus
    {
        return $this->mlsStatus;
    }

    public function setMlsStatus(?MLSStatus $mlsStatus): self
    {
        $this->mlsStatus = $mlsStatus;

        return $this;
    }

    public function getBreedingInfo(): ?string
    {
        return $this->breedingInfo;
    }

    public function setBreedingInfo(?string $breedingInfo): self
    {
        $this->breedingInfo = $breedingInfo;

        return $this;
    }
}

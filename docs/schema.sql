-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mar. 03 juin 2025 à 18:53
-- Version du serveur : 10.4.32-MariaDB
-- Version de PHP : 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `krosmozdb`
--

-- --------------------------------------------------------

--
-- Structure de la table `attributes`
--

CREATE TABLE `attributes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `attribute_creature`
--

CREATE TABLE `attribute_creature` (
  `attribute_id` bigint(20) UNSIGNED NOT NULL,
  `creature_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `campaigns`
--

CREATE TABLE `campaigns` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `state` int(11) NOT NULL DEFAULT 0,
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `campaign_page`
--

CREATE TABLE `campaign_page` (
  `campaign_id` bigint(20) UNSIGNED NOT NULL,
  `page_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `campaign_panoply`
--

CREATE TABLE `campaign_panoply` (
  `panoply_id` bigint(20) UNSIGNED NOT NULL,
  `campaign_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `campaign_scenario`
--

CREATE TABLE `campaign_scenario` (
  `campaign_id` bigint(20) UNSIGNED NOT NULL,
  `scenario_id` bigint(20) UNSIGNED NOT NULL,
  `order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `campaign_shop`
--

CREATE TABLE `campaign_shop` (
  `shop_id` bigint(20) UNSIGNED NOT NULL,
  `campaign_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `campaign_spell`
--

CREATE TABLE `campaign_spell` (
  `spell_id` bigint(20) UNSIGNED NOT NULL,
  `campaign_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `campaign_user`
--

CREATE TABLE `campaign_user` (
  `campaign_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `capabilities`
--

CREATE TABLE `capabilities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `effect` varchar(255) DEFAULT NULL,
  `level` varchar(255) NOT NULL DEFAULT '1',
  `pa` varchar(255) NOT NULL DEFAULT '3',
  `po` varchar(255) NOT NULL DEFAULT '0',
  `po_editable` tinyint(1) NOT NULL DEFAULT 1,
  `time_before_use_again` varchar(255) NOT NULL DEFAULT '0',
  `casting_time` varchar(255) NOT NULL DEFAULT '0',
  `duration` varchar(255) NOT NULL DEFAULT '0',
  `element` varchar(255) NOT NULL DEFAULT 'neutral',
  `is_magic` tinyint(1) NOT NULL DEFAULT 1,
  `ritual_available` tinyint(1) NOT NULL DEFAULT 1,
  `powerful` varchar(255) DEFAULT NULL,
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `capability_creature`
--

CREATE TABLE `capability_creature` (
  `capability_id` bigint(20) UNSIGNED NOT NULL,
  `creature_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `capability_specialization`
--

CREATE TABLE `capability_specialization` (
  `capability_id` bigint(20) UNSIGNED NOT NULL,
  `specialization_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `classes`
--

CREATE TABLE `classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `official_id` varchar(255) DEFAULT NULL,
  `dofusdb_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description_fast` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `life` varchar(255) DEFAULT NULL,
  `life_dice` varchar(255) DEFAULT NULL,
  `specificity` varchar(255) DEFAULT NULL,
  `dofus_version` varchar(255) NOT NULL DEFAULT '3',
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `image` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `auto_update` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `consumables`
--

CREATE TABLE `consumables` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `official_id` varchar(255) DEFAULT NULL,
  `dofusdb_id` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `effect` varchar(255) DEFAULT NULL,
  `level` varchar(255) DEFAULT NULL,
  `recipe` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `rarity` int(11) NOT NULL DEFAULT 0,
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `dofus_version` varchar(255) NOT NULL DEFAULT '3',
  `image` varchar(255) DEFAULT NULL,
  `auto_update` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `consumable_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `consumable_campaign`
--

CREATE TABLE `consumable_campaign` (
  `consumable_id` bigint(20) UNSIGNED NOT NULL,
  `campaign_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `consumable_creature`
--

CREATE TABLE `consumable_creature` (
  `consumable_id` bigint(20) UNSIGNED NOT NULL,
  `creature_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` varchar(255) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `consumable_resource`
--

CREATE TABLE `consumable_resource` (
  `consumable_id` bigint(20) UNSIGNED NOT NULL,
  `resource_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` varchar(255) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `consumable_scenario`
--

CREATE TABLE `consumable_scenario` (
  `consumable_id` bigint(20) UNSIGNED NOT NULL,
  `scenario_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `consumable_shop`
--

CREATE TABLE `consumable_shop` (
  `consumable_id` bigint(20) UNSIGNED NOT NULL,
  `shop_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `consumable_types`
--

CREATE TABLE `consumable_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `creatures`
--

CREATE TABLE `creatures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `hostility` int(11) NOT NULL DEFAULT 2,
  `location` varchar(255) DEFAULT NULL,
  `level` varchar(255) NOT NULL DEFAULT '1',
  `other_info` varchar(255) DEFAULT NULL,
  `life` varchar(255) NOT NULL DEFAULT '30',
  `pa` varchar(255) NOT NULL DEFAULT '6',
  `pm` varchar(255) NOT NULL DEFAULT '3',
  `po` varchar(255) NOT NULL DEFAULT '0',
  `ini` varchar(255) NOT NULL DEFAULT '0',
  `invocation` varchar(255) NOT NULL DEFAULT '0',
  `touch` varchar(255) NOT NULL DEFAULT '0',
  `ca` varchar(255) NOT NULL DEFAULT '0',
  `dodge_pa` varchar(255) NOT NULL DEFAULT '0',
  `dodge_pm` varchar(255) NOT NULL DEFAULT '0',
  `fuite` varchar(255) NOT NULL DEFAULT '0',
  `tacle` varchar(255) NOT NULL DEFAULT '0',
  `vitality` varchar(255) NOT NULL DEFAULT '0',
  `sagesse` varchar(255) NOT NULL DEFAULT '0',
  `strong` varchar(255) NOT NULL DEFAULT '0',
  `intel` varchar(255) NOT NULL DEFAULT '0',
  `agi` varchar(255) NOT NULL DEFAULT '0',
  `chance` varchar(255) NOT NULL DEFAULT '0',
  `do_fixe_neutre` varchar(255) NOT NULL DEFAULT '0',
  `do_fixe_terre` varchar(255) NOT NULL DEFAULT '0',
  `do_fixe_feu` varchar(255) NOT NULL DEFAULT '0',
  `do_fixe_air` varchar(255) NOT NULL DEFAULT '0',
  `do_fixe_eau` varchar(255) NOT NULL DEFAULT '0',
  `res_fixe_neutre` text NOT NULL DEFAULT '0',
  `res_fixe_terre` text NOT NULL DEFAULT '0',
  `res_fixe_feu` text NOT NULL DEFAULT '0',
  `res_fixe_air` text NOT NULL DEFAULT '0',
  `res_fixe_eau` text NOT NULL DEFAULT '0',
  `res_neutre` varchar(255) NOT NULL DEFAULT '0',
  `res_terre` varchar(255) NOT NULL DEFAULT '0',
  `res_feu` varchar(255) NOT NULL DEFAULT '0',
  `res_air` varchar(255) NOT NULL DEFAULT '0',
  `res_eau` varchar(255) NOT NULL DEFAULT '0',
  `acrobatie_bonus` varchar(255) NOT NULL DEFAULT '0',
  `discretion_bonus` varchar(255) NOT NULL DEFAULT '0',
  `escamotage_bonus` varchar(255) NOT NULL DEFAULT '0',
  `athletisme_bonus` varchar(255) NOT NULL DEFAULT '0',
  `intimidation_bonus` varchar(255) NOT NULL DEFAULT '0',
  `arcane_bonus` varchar(255) NOT NULL DEFAULT '0',
  `histoire_bonus` varchar(255) NOT NULL DEFAULT '0',
  `investigation_bonus` varchar(255) NOT NULL DEFAULT '0',
  `nature_bonus` varchar(255) NOT NULL DEFAULT '0',
  `religion_bonus` varchar(255) NOT NULL DEFAULT '0',
  `dressage_bonus` varchar(255) NOT NULL DEFAULT '0',
  `medecine_bonus` varchar(255) NOT NULL DEFAULT '0',
  `perception_bonus` varchar(255) NOT NULL DEFAULT '0',
  `perspicacite_bonus` varchar(255) NOT NULL DEFAULT '0',
  `survie_bonus` varchar(255) NOT NULL DEFAULT '0',
  `persuasion_bonus` varchar(255) NOT NULL DEFAULT '0',
  `representation_bonus` varchar(255) NOT NULL DEFAULT '0',
  `supercherie_bonus` varchar(255) NOT NULL DEFAULT '0',
  `acrobatie_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `discretion_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `escamotage_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `athletisme_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `intimidation_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `arcane_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `histoire_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `investigation_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `nature_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `religion_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `dressage_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `medecine_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `perception_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `perspicacite_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `survie_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `persuasion_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `representation_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `supercherie_mastery` tinyint(4) NOT NULL DEFAULT 0,
  `kamas` varchar(255) DEFAULT NULL,
  `drop_` varchar(255) DEFAULT NULL,
  `other_item` varchar(255) DEFAULT NULL,
  `other_consumable` varchar(255) DEFAULT NULL,
  `other_resource` varchar(255) DEFAULT NULL,
  `other_spell` varchar(255) DEFAULT NULL,
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `creature_item`
--

CREATE TABLE `creature_item` (
  `creature_id` bigint(20) UNSIGNED NOT NULL,
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` varchar(255) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `creature_resource`
--

CREATE TABLE `creature_resource` (
  `creature_id` bigint(20) UNSIGNED NOT NULL,
  `resource_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` varchar(255) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `creature_spell`
--

CREATE TABLE `creature_spell` (
  `creature_id` bigint(20) UNSIGNED NOT NULL,
  `spell_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `files`
--

CREATE TABLE `files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `file` varchar(255) NOT NULL,
  `title` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `file_campaign`
--

CREATE TABLE `file_campaign` (
  `file_id` bigint(20) UNSIGNED NOT NULL,
  `campaign_id` bigint(20) UNSIGNED NOT NULL,
  `order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `file_scenario`
--

CREATE TABLE `file_scenario` (
  `file_id` bigint(20) UNSIGNED NOT NULL,
  `scenario_id` bigint(20) UNSIGNED NOT NULL,
  `order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `file_section`
--

CREATE TABLE `file_section` (
  `file_id` bigint(20) UNSIGNED NOT NULL,
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `order` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `items`
--

CREATE TABLE `items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `official_id` varchar(255) DEFAULT NULL,
  `dofusdb_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `level` varchar(255) DEFAULT NULL,
  `description` varchar(255) DEFAULT NULL,
  `effect` varchar(255) DEFAULT NULL,
  `bonus` varchar(255) DEFAULT NULL,
  `recipe` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `rarity` int(11) NOT NULL DEFAULT 0,
  `dofus_version` varchar(255) NOT NULL DEFAULT '3',
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `image` varchar(255) DEFAULT NULL,
  `auto_update` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `item_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `item_campaign`
--

CREATE TABLE `item_campaign` (
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `campaign_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `item_panoply`
--

CREATE TABLE `item_panoply` (
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `panoply_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `item_resource`
--

CREATE TABLE `item_resource` (
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `resource_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` varchar(255) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `item_scenario`
--

CREATE TABLE `item_scenario` (
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `scenario_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `item_shop`
--

CREATE TABLE `item_shop` (
  `item_id` bigint(20) UNSIGNED NOT NULL,
  `shop_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `item_types`
--

CREATE TABLE `item_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2025_06_01_100000_entity_users_table', 1),
(2, '2025_06_01_100001_jobs_table', 1),
(3, '2025_06_01_100002_cache_table', 1),
(4, '2025_06_01_100010_type_consumable_types_table', 1),
(5, '2025_06_01_100020_type_item_types_table', 1),
(6, '2025_06_01_100030_type_resource_types_table', 1),
(7, '2025_06_01_100040_type_monster_races_table', 1),
(8, '2025_06_01_100070_type_spell_types_table', 1),
(9, '2025_06_01_100100_entity_capabilities_table', 1),
(10, '2025_06_01_100110_entity_classes_table', 1),
(11, '2025_06_01_100120_entity_specializations_table', 1),
(12, '2025_06_01_100130_entity_creatures_table', 1),
(13, '2025_06_01_100140_entity_npcs_table', 1),
(14, '2025_06_01_100150_entity_shops_table', 1),
(15, '2025_06_01_100160_entity_items_table', 1),
(16, '2025_06_01_100170_entity_consumables_table', 1),
(17, '2025_06_01_100180_entity_resources_table', 1),
(18, '2025_06_01_100190_entity_spells_table', 1),
(19, '2025_06_01_100200_entity_attributes_table', 1),
(20, '2025_06_01_100210_entity_panoplies_table', 1),
(21, '2025_06_01_100220_entity_monsters_table', 1),
(22, '2025_06_01_100230_entity_scenarios_table', 1),
(23, '2025_06_01_100240_entity_campaigns_table', 1),
(24, '2025_06_01_100250_entity_pages_table', 1),
(25, '2025_06_01_100260_entity_files_table', 1),
(26, '2025_06_01_100260_entity_sections_table', 1),
(27, '2025_06_01_100300_pivot_consumable_resource_table', 1),
(28, '2025_06_01_100310_pivot_item_resource_table', 1),
(29, '2025_06_01_100320_pivot_item_panoply_table', 1),
(30, '2025_06_01_100330_pivot_capability_specialization_table', 1),
(31, '2025_06_01_100340_pivot_capability_creature_table', 1),
(32, '2025_06_01_100350_pivot_consumable_creature_table', 1),
(33, '2025_06_01_100360_pivot_creature_item_table', 1),
(34, '2025_06_01_100370_pivot_creature_spell_table', 1),
(35, '2025_06_01_100380_pivot_attribute_creature_table', 1),
(36, '2025_06_01_100390_pivot_creature_resource_table', 1),
(37, '2025_06_01_100400_pivot_file_scenario_table', 1),
(38, '2025_06_01_100410_pivot_consumable_scenario_table', 1),
(39, '2025_06_01_100420_pivot_item_scenario_table', 1),
(40, '2025_06_01_100430_pivot_npc_scenario_table', 1),
(41, '2025_06_01_100440_pivot_monster_scenario_table', 1),
(42, '2025_06_01_100450_pivot_campagin_user_table', 1),
(43, '2025_06_01_100450_pivot_page_user_table', 1),
(44, '2025_06_01_100450_pivot_scenario_user_table', 1),
(45, '2025_06_01_100450_pivot_section_user_table', 1),
(46, '2025_06_01_100460_pivot_scenario_shop_table', 1),
(47, '2025_06_01_100470_pivot_scenario_spell_table', 1),
(48, '2025_06_01_100480_pivot_resource_scenario_table', 1),
(49, '2025_06_01_100481_pivot_resource_campaign_table', 1),
(50, '2025_06_01_100490_pivot_scenario_panoply_table', 1),
(51, '2025_06_01_100500_pivot_campaign_scenario_table', 1),
(52, '2025_06_01_100510_pivot_file_campaign_table', 1),
(53, '2025_06_01_100510_pivot_file_section_table', 1),
(54, '2025_06_01_100520_pivot_consumable_campaign_table', 1),
(55, '2025_06_01_100530_pivot_item_campaign_table', 1),
(56, '2025_06_01_100540_pivot_monster_campaign_table', 1),
(57, '2025_06_01_100550_pivot_npc_campaign_table', 1),
(58, '2025_06_01_100560_pivot_campaign_shop_table', 1),
(59, '2025_06_01_100570_pivot_campaign_spell_table', 1),
(60, '2025_06_01_100580_pivot_campaign_panoply_table', 1),
(61, '2025_06_01_100590_pivot_scenario_link_table', 1),
(62, '2025_06_01_100600_pivot_campaign_page_table', 1),
(63, '2025_06_01_100610_pivot_scenario_page_table', 1),
(64, '2025_06_01_100620_pivot_consumable_shop_table', 1),
(65, '2025_06_01_100630_pivot_item_shop_table', 1),
(66, '2025_06_01_100640_pivot_resource_shop_table', 1),
(67, '2025_06_01_100650_pivot_spell_invocation_table', 1),
(68, '2025_06_01_100660_pivot_spell_type_table', 1);

-- --------------------------------------------------------

--
-- Structure de la table `monsters`
--

CREATE TABLE `monsters` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `creature_id` bigint(20) UNSIGNED DEFAULT NULL,
  `official_id` varchar(255) DEFAULT NULL,
  `dofusdb_id` varchar(255) DEFAULT NULL,
  `dofus_version` varchar(255) NOT NULL DEFAULT '3',
  `auto_update` tinyint(1) NOT NULL DEFAULT 1,
  `size` int(11) NOT NULL DEFAULT 2,
  `monster_race_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `monster_campaign`
--

CREATE TABLE `monster_campaign` (
  `monster_id` bigint(20) UNSIGNED NOT NULL,
  `campaign_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `monster_races`
--

CREATE TABLE `monster_races` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `id_super_race` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `monster_scenario`
--

CREATE TABLE `monster_scenario` (
  `monster_id` bigint(20) UNSIGNED NOT NULL,
  `scenario_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `npcs`
--

CREATE TABLE `npcs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `creature_id` bigint(20) UNSIGNED DEFAULT NULL,
  `story` varchar(255) DEFAULT NULL,
  `historical` varchar(255) DEFAULT NULL,
  `age` varchar(255) DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `classe_id` bigint(20) UNSIGNED DEFAULT NULL,
  `specialization_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `npc_campaign`
--

CREATE TABLE `npc_campaign` (
  `npc_id` bigint(20) UNSIGNED NOT NULL,
  `campaign_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `npc_scenario`
--

CREATE TABLE `npc_scenario` (
  `npc_id` bigint(20) UNSIGNED NOT NULL,
  `scenario_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `pages`
--

CREATE TABLE `pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `in_menu` tinyint(1) NOT NULL DEFAULT 1,
  `state` varchar(255) NOT NULL DEFAULT 'draft',
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `menu_order` int(11) NOT NULL DEFAULT 0,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `page_user`
--

CREATE TABLE `page_user` (
  `page_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `panoplies`
--

CREATE TABLE `panoplies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `bonus` varchar(255) DEFAULT NULL,
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `resources`
--

CREATE TABLE `resources` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dofusdb_id` varchar(255) DEFAULT NULL,
  `official_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `level` varchar(255) NOT NULL DEFAULT '1',
  `price` varchar(255) DEFAULT NULL,
  `weight` varchar(255) DEFAULT NULL,
  `rarity` int(11) NOT NULL DEFAULT 0,
  `dofus_version` varchar(255) NOT NULL DEFAULT '3',
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `image` varchar(255) DEFAULT NULL,
  `auto_update` tinyint(1) NOT NULL DEFAULT 1,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `resource_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `resource_campaign`
--

CREATE TABLE `resource_campaign` (
  `resource_id` bigint(20) UNSIGNED NOT NULL,
  `campaign_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `resource_scenario`
--

CREATE TABLE `resource_scenario` (
  `resource_id` bigint(20) UNSIGNED NOT NULL,
  `scenario_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `resource_shop`
--

CREATE TABLE `resource_shop` (
  `resource_id` bigint(20) UNSIGNED NOT NULL,
  `shop_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` varchar(255) DEFAULT NULL,
  `price` varchar(255) DEFAULT NULL,
  `comment` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `resource_types`
--

CREATE TABLE `resource_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `scenarios`
--

CREATE TABLE `scenarios` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `slug` varchar(255) NOT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `is_public` tinyint(1) NOT NULL DEFAULT 0,
  `state` int(11) NOT NULL DEFAULT 0,
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `scenario_link`
--

CREATE TABLE `scenario_link` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `scenario_id` bigint(20) UNSIGNED NOT NULL,
  `next_scenario_id` bigint(20) UNSIGNED NOT NULL,
  `condition` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `scenario_page`
--

CREATE TABLE `scenario_page` (
  `scenario_id` bigint(20) UNSIGNED NOT NULL,
  `page_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `scenario_panoply`
--

CREATE TABLE `scenario_panoply` (
  `scenario_id` bigint(20) UNSIGNED NOT NULL,
  `panoply_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `scenario_shop`
--

CREATE TABLE `scenario_shop` (
  `scenario_id` bigint(20) UNSIGNED NOT NULL,
  `shop_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `scenario_spell`
--

CREATE TABLE `scenario_spell` (
  `scenario_id` bigint(20) UNSIGNED NOT NULL,
  `spell_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `scenario_user`
--

CREATE TABLE `scenario_user` (
  `scenario_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sections`
--

CREATE TABLE `sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `page_id` bigint(20) UNSIGNED NOT NULL,
  `order` int(11) NOT NULL DEFAULT 0,
  `type` varchar(255) NOT NULL,
  `params` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`params`)),
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `state` varchar(255) NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `section_user`
--

CREATE TABLE `section_user` (
  `section_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `shops`
--

CREATE TABLE `shops` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `price` int(11) NOT NULL DEFAULT 0,
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `npc_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `specializations`
--

CREATE TABLE `specializations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `spells`
--

CREATE TABLE `spells` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `official_id` varchar(255) DEFAULT NULL,
  `dofusdb_id` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) NOT NULL,
  `effect` varchar(255) DEFAULT NULL,
  `area` int(11) NOT NULL DEFAULT 0,
  `level` varchar(255) NOT NULL DEFAULT '1',
  `po` varchar(255) NOT NULL DEFAULT '1',
  `po_editable` tinyint(1) NOT NULL DEFAULT 1,
  `pa` varchar(255) NOT NULL DEFAULT '3',
  `cast_per_turn` varchar(255) NOT NULL DEFAULT '1',
  `cast_per_target` varchar(255) NOT NULL DEFAULT '0',
  `sight_line` tinyint(1) NOT NULL DEFAULT 1,
  `number_between_two_cast` varchar(255) NOT NULL DEFAULT '0',
  `number_between_two_cast_editable` tinyint(1) NOT NULL DEFAULT 1,
  `element` int(11) NOT NULL DEFAULT 0,
  `category` int(11) NOT NULL DEFAULT 0,
  `is_magic` tinyint(1) NOT NULL DEFAULT 1,
  `powerful` int(11) NOT NULL DEFAULT 0,
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `image` varchar(255) DEFAULT NULL,
  `auto_update` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `spell_invocation`
--

CREATE TABLE `spell_invocation` (
  `spell_id` bigint(20) UNSIGNED NOT NULL,
  `monster_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `spell_type`
--

CREATE TABLE `spell_type` (
  `spell_id` bigint(20) UNSIGNED NOT NULL,
  `spell_type_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `spell_types`
--

CREATE TABLE `spell_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `color` varchar(255) NOT NULL DEFAULT 'zinc',
  `icon` varchar(255) DEFAULT NULL,
  `usable` tinyint(4) NOT NULL DEFAULT 0,
  `is_visible` varchar(255) NOT NULL DEFAULT 'guest',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `role` varchar(255) NOT NULL DEFAULT 'user',
  `avatar` varchar(255) DEFAULT NULL,
  `notifications_enabled` tinyint(1) NOT NULL DEFAULT 1,
  `notification_channels` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '["database"]' CHECK (json_valid(`notification_channels`)),
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `attributes`
--
ALTER TABLE `attributes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `attributes_created_by_foreign` (`created_by`);

--
-- Index pour la table `attribute_creature`
--
ALTER TABLE `attribute_creature`
  ADD PRIMARY KEY (`attribute_id`,`creature_id`),
  ADD KEY `attribute_creature_creature_id_foreign` (`creature_id`);

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Index pour la table `campaigns`
--
ALTER TABLE `campaigns`
  ADD PRIMARY KEY (`id`),
  ADD KEY `campaigns_created_by_foreign` (`created_by`);

--
-- Index pour la table `campaign_page`
--
ALTER TABLE `campaign_page`
  ADD PRIMARY KEY (`campaign_id`,`page_id`),
  ADD KEY `campaign_page_page_id_foreign` (`page_id`);

--
-- Index pour la table `campaign_panoply`
--
ALTER TABLE `campaign_panoply`
  ADD PRIMARY KEY (`panoply_id`,`campaign_id`),
  ADD KEY `campaign_panoply_campaign_id_foreign` (`campaign_id`);

--
-- Index pour la table `campaign_scenario`
--
ALTER TABLE `campaign_scenario`
  ADD PRIMARY KEY (`campaign_id`,`scenario_id`),
  ADD KEY `campaign_scenario_scenario_id_foreign` (`scenario_id`);

--
-- Index pour la table `campaign_shop`
--
ALTER TABLE `campaign_shop`
  ADD PRIMARY KEY (`shop_id`,`campaign_id`),
  ADD KEY `campaign_shop_campaign_id_foreign` (`campaign_id`);

--
-- Index pour la table `campaign_spell`
--
ALTER TABLE `campaign_spell`
  ADD PRIMARY KEY (`spell_id`,`campaign_id`),
  ADD KEY `campaign_spell_campaign_id_foreign` (`campaign_id`);

--
-- Index pour la table `campaign_user`
--
ALTER TABLE `campaign_user`
  ADD PRIMARY KEY (`campaign_id`,`user_id`),
  ADD KEY `campaign_user_user_id_foreign` (`user_id`);

--
-- Index pour la table `capabilities`
--
ALTER TABLE `capabilities`
  ADD PRIMARY KEY (`id`),
  ADD KEY `capabilities_created_by_foreign` (`created_by`);

--
-- Index pour la table `capability_creature`
--
ALTER TABLE `capability_creature`
  ADD PRIMARY KEY (`capability_id`,`creature_id`),
  ADD KEY `capability_creature_creature_id_foreign` (`creature_id`);

--
-- Index pour la table `capability_specialization`
--
ALTER TABLE `capability_specialization`
  ADD PRIMARY KEY (`capability_id`,`specialization_id`),
  ADD KEY `capability_specialization_specialization_id_foreign` (`specialization_id`);

--
-- Index pour la table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `classes_created_by_foreign` (`created_by`);

--
-- Index pour la table `consumables`
--
ALTER TABLE `consumables`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consumables_consumable_type_id_foreign` (`consumable_type_id`),
  ADD KEY `consumables_created_by_foreign` (`created_by`);

--
-- Index pour la table `consumable_campaign`
--
ALTER TABLE `consumable_campaign`
  ADD PRIMARY KEY (`consumable_id`,`campaign_id`),
  ADD KEY `consumable_campaign_campaign_id_foreign` (`campaign_id`);

--
-- Index pour la table `consumable_creature`
--
ALTER TABLE `consumable_creature`
  ADD PRIMARY KEY (`consumable_id`,`creature_id`),
  ADD KEY `consumable_creature_creature_id_foreign` (`creature_id`);

--
-- Index pour la table `consumable_resource`
--
ALTER TABLE `consumable_resource`
  ADD PRIMARY KEY (`consumable_id`,`resource_id`),
  ADD KEY `consumable_resource_resource_id_foreign` (`resource_id`);

--
-- Index pour la table `consumable_scenario`
--
ALTER TABLE `consumable_scenario`
  ADD PRIMARY KEY (`consumable_id`,`scenario_id`),
  ADD KEY `consumable_scenario_scenario_id_foreign` (`scenario_id`);

--
-- Index pour la table `consumable_shop`
--
ALTER TABLE `consumable_shop`
  ADD PRIMARY KEY (`consumable_id`,`shop_id`),
  ADD KEY `consumable_shop_shop_id_foreign` (`shop_id`);

--
-- Index pour la table `consumable_types`
--
ALTER TABLE `consumable_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `consumable_types_created_by_foreign` (`created_by`);

--
-- Index pour la table `creatures`
--
ALTER TABLE `creatures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `creatures_created_by_foreign` (`created_by`);

--
-- Index pour la table `creature_item`
--
ALTER TABLE `creature_item`
  ADD PRIMARY KEY (`creature_id`,`item_id`),
  ADD KEY `creature_item_item_id_foreign` (`item_id`);

--
-- Index pour la table `creature_resource`
--
ALTER TABLE `creature_resource`
  ADD PRIMARY KEY (`creature_id`,`resource_id`),
  ADD KEY `creature_resource_resource_id_foreign` (`resource_id`);

--
-- Index pour la table `creature_spell`
--
ALTER TABLE `creature_spell`
  ADD PRIMARY KEY (`creature_id`,`spell_id`),
  ADD KEY `creature_spell_spell_id_foreign` (`spell_id`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `file_campaign`
--
ALTER TABLE `file_campaign`
  ADD PRIMARY KEY (`file_id`,`campaign_id`),
  ADD KEY `file_campaign_campaign_id_foreign` (`campaign_id`);

--
-- Index pour la table `file_scenario`
--
ALTER TABLE `file_scenario`
  ADD PRIMARY KEY (`file_id`,`scenario_id`),
  ADD KEY `file_scenario_scenario_id_foreign` (`scenario_id`);

--
-- Index pour la table `file_section`
--
ALTER TABLE `file_section`
  ADD PRIMARY KEY (`file_id`,`section_id`),
  ADD KEY `file_section_section_id_foreign` (`section_id`);

--
-- Index pour la table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `items_item_type_id_foreign` (`item_type_id`),
  ADD KEY `items_created_by_foreign` (`created_by`);

--
-- Index pour la table `item_campaign`
--
ALTER TABLE `item_campaign`
  ADD PRIMARY KEY (`item_id`,`campaign_id`),
  ADD KEY `item_campaign_campaign_id_foreign` (`campaign_id`);

--
-- Index pour la table `item_panoply`
--
ALTER TABLE `item_panoply`
  ADD PRIMARY KEY (`item_id`,`panoply_id`),
  ADD KEY `item_panoply_panoply_id_foreign` (`panoply_id`);

--
-- Index pour la table `item_resource`
--
ALTER TABLE `item_resource`
  ADD PRIMARY KEY (`item_id`,`resource_id`),
  ADD KEY `item_resource_resource_id_foreign` (`resource_id`);

--
-- Index pour la table `item_scenario`
--
ALTER TABLE `item_scenario`
  ADD PRIMARY KEY (`item_id`,`scenario_id`),
  ADD KEY `item_scenario_scenario_id_foreign` (`scenario_id`);

--
-- Index pour la table `item_shop`
--
ALTER TABLE `item_shop`
  ADD PRIMARY KEY (`item_id`,`shop_id`),
  ADD KEY `item_shop_shop_id_foreign` (`shop_id`);

--
-- Index pour la table `item_types`
--
ALTER TABLE `item_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `item_types_created_by_foreign` (`created_by`);

--
-- Index pour la table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Index pour la table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `monsters`
--
ALTER TABLE `monsters`
  ADD PRIMARY KEY (`id`),
  ADD KEY `monsters_creature_id_foreign` (`creature_id`),
  ADD KEY `monsters_monster_race_id_foreign` (`monster_race_id`);

--
-- Index pour la table `monster_campaign`
--
ALTER TABLE `monster_campaign`
  ADD PRIMARY KEY (`monster_id`,`campaign_id`),
  ADD KEY `monster_campaign_campaign_id_foreign` (`campaign_id`);

--
-- Index pour la table `monster_races`
--
ALTER TABLE `monster_races`
  ADD PRIMARY KEY (`id`),
  ADD KEY `monster_races_created_by_foreign` (`created_by`),
  ADD KEY `monster_races_id_super_race_foreign` (`id_super_race`);

--
-- Index pour la table `monster_scenario`
--
ALTER TABLE `monster_scenario`
  ADD PRIMARY KEY (`monster_id`,`scenario_id`),
  ADD KEY `monster_scenario_scenario_id_foreign` (`scenario_id`);

--
-- Index pour la table `npcs`
--
ALTER TABLE `npcs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `npcs_creature_id_foreign` (`creature_id`),
  ADD KEY `npcs_classe_id_foreign` (`classe_id`),
  ADD KEY `npcs_specialization_id_foreign` (`specialization_id`);

--
-- Index pour la table `npc_campaign`
--
ALTER TABLE `npc_campaign`
  ADD PRIMARY KEY (`npc_id`,`campaign_id`),
  ADD KEY `npc_campaign_campaign_id_foreign` (`campaign_id`);

--
-- Index pour la table `npc_scenario`
--
ALTER TABLE `npc_scenario`
  ADD PRIMARY KEY (`npc_id`,`scenario_id`),
  ADD KEY `npc_scenario_scenario_id_foreign` (`scenario_id`);

--
-- Index pour la table `pages`
--
ALTER TABLE `pages`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `pages_slug_unique` (`slug`),
  ADD KEY `pages_parent_id_foreign` (`parent_id`),
  ADD KEY `pages_created_by_foreign` (`created_by`);

--
-- Index pour la table `page_user`
--
ALTER TABLE `page_user`
  ADD PRIMARY KEY (`page_id`,`user_id`),
  ADD KEY `page_user_user_id_foreign` (`user_id`);

--
-- Index pour la table `panoplies`
--
ALTER TABLE `panoplies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `panoplies_created_by_foreign` (`created_by`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `resources`
--
ALTER TABLE `resources`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resources_resource_type_id_foreign` (`resource_type_id`),
  ADD KEY `resources_created_by_foreign` (`created_by`);

--
-- Index pour la table `resource_campaign`
--
ALTER TABLE `resource_campaign`
  ADD PRIMARY KEY (`resource_id`,`campaign_id`),
  ADD KEY `resource_campaign_campaign_id_foreign` (`campaign_id`);

--
-- Index pour la table `resource_scenario`
--
ALTER TABLE `resource_scenario`
  ADD PRIMARY KEY (`resource_id`,`scenario_id`),
  ADD KEY `resource_scenario_scenario_id_foreign` (`scenario_id`);

--
-- Index pour la table `resource_shop`
--
ALTER TABLE `resource_shop`
  ADD PRIMARY KEY (`resource_id`,`shop_id`),
  ADD KEY `resource_shop_shop_id_foreign` (`shop_id`);

--
-- Index pour la table `resource_types`
--
ALTER TABLE `resource_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `resource_types_created_by_foreign` (`created_by`);

--
-- Index pour la table `scenarios`
--
ALTER TABLE `scenarios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `scenarios_created_by_foreign` (`created_by`);

--
-- Index pour la table `scenario_link`
--
ALTER TABLE `scenario_link`
  ADD PRIMARY KEY (`id`),
  ADD KEY `scenario_link_scenario_id_foreign` (`scenario_id`),
  ADD KEY `scenario_link_next_scenario_id_foreign` (`next_scenario_id`);

--
-- Index pour la table `scenario_page`
--
ALTER TABLE `scenario_page`
  ADD PRIMARY KEY (`scenario_id`,`page_id`),
  ADD KEY `scenario_page_page_id_foreign` (`page_id`);

--
-- Index pour la table `scenario_panoply`
--
ALTER TABLE `scenario_panoply`
  ADD PRIMARY KEY (`scenario_id`,`panoply_id`),
  ADD KEY `scenario_panoply_panoply_id_foreign` (`panoply_id`);

--
-- Index pour la table `scenario_shop`
--
ALTER TABLE `scenario_shop`
  ADD PRIMARY KEY (`scenario_id`,`shop_id`),
  ADD KEY `scenario_shop_shop_id_foreign` (`shop_id`);

--
-- Index pour la table `scenario_spell`
--
ALTER TABLE `scenario_spell`
  ADD PRIMARY KEY (`scenario_id`,`spell_id`),
  ADD KEY `scenario_spell_spell_id_foreign` (`spell_id`);

--
-- Index pour la table `scenario_user`
--
ALTER TABLE `scenario_user`
  ADD PRIMARY KEY (`scenario_id`,`user_id`),
  ADD KEY `scenario_user_user_id_foreign` (`user_id`);

--
-- Index pour la table `sections`
--
ALTER TABLE `sections`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sections_page_id_foreign` (`page_id`),
  ADD KEY `sections_created_by_foreign` (`created_by`);

--
-- Index pour la table `section_user`
--
ALTER TABLE `section_user`
  ADD PRIMARY KEY (`section_id`,`user_id`),
  ADD KEY `section_user_user_id_foreign` (`user_id`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `shops`
--
ALTER TABLE `shops`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shops_created_by_foreign` (`created_by`),
  ADD KEY `shops_npc_id_foreign` (`npc_id`);

--
-- Index pour la table `specializations`
--
ALTER TABLE `specializations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `specializations_created_by_foreign` (`created_by`);

--
-- Index pour la table `spells`
--
ALTER TABLE `spells`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spells_created_by_foreign` (`created_by`);

--
-- Index pour la table `spell_invocation`
--
ALTER TABLE `spell_invocation`
  ADD PRIMARY KEY (`spell_id`,`monster_id`),
  ADD KEY `spell_invocation_monster_id_foreign` (`monster_id`);

--
-- Index pour la table `spell_type`
--
ALTER TABLE `spell_type`
  ADD PRIMARY KEY (`spell_id`,`spell_type_id`),
  ADD KEY `spell_type_spell_type_id_foreign` (`spell_type_id`);

--
-- Index pour la table `spell_types`
--
ALTER TABLE `spell_types`
  ADD PRIMARY KEY (`id`),
  ADD KEY `spell_types_created_by_foreign` (`created_by`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `attributes`
--
ALTER TABLE `attributes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `campaigns`
--
ALTER TABLE `campaigns`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `capabilities`
--
ALTER TABLE `capabilities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `consumables`
--
ALTER TABLE `consumables`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `consumable_types`
--
ALTER TABLE `consumable_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `creatures`
--
ALTER TABLE `creatures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `files`
--
ALTER TABLE `files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `items`
--
ALTER TABLE `items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `item_types`
--
ALTER TABLE `item_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT pour la table `monsters`
--
ALTER TABLE `monsters`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `monster_races`
--
ALTER TABLE `monster_races`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `npcs`
--
ALTER TABLE `npcs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `pages`
--
ALTER TABLE `pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `panoplies`
--
ALTER TABLE `panoplies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `resources`
--
ALTER TABLE `resources`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `resource_types`
--
ALTER TABLE `resource_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `scenarios`
--
ALTER TABLE `scenarios`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `scenario_link`
--
ALTER TABLE `scenario_link`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `sections`
--
ALTER TABLE `sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `shops`
--
ALTER TABLE `shops`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `specializations`
--
ALTER TABLE `specializations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `spells`
--
ALTER TABLE `spells`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `spell_types`
--
ALTER TABLE `spell_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `attributes`
--
ALTER TABLE `attributes`
  ADD CONSTRAINT `attributes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `attribute_creature`
--
ALTER TABLE `attribute_creature`
  ADD CONSTRAINT `attribute_creature_attribute_id_foreign` FOREIGN KEY (`attribute_id`) REFERENCES `attributes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `attribute_creature_creature_id_foreign` FOREIGN KEY (`creature_id`) REFERENCES `creatures` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `campaigns`
--
ALTER TABLE `campaigns`
  ADD CONSTRAINT `campaigns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `campaign_page`
--
ALTER TABLE `campaign_page`
  ADD CONSTRAINT `campaign_page_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `campaign_page_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `campaign_panoply`
--
ALTER TABLE `campaign_panoply`
  ADD CONSTRAINT `campaign_panoply_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `campaign_panoply_panoply_id_foreign` FOREIGN KEY (`panoply_id`) REFERENCES `panoplies` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `campaign_scenario`
--
ALTER TABLE `campaign_scenario`
  ADD CONSTRAINT `campaign_scenario_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `campaign_scenario_scenario_id_foreign` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `campaign_shop`
--
ALTER TABLE `campaign_shop`
  ADD CONSTRAINT `campaign_shop_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `campaign_shop_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `campaign_spell`
--
ALTER TABLE `campaign_spell`
  ADD CONSTRAINT `campaign_spell_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `campaign_spell_spell_id_foreign` FOREIGN KEY (`spell_id`) REFERENCES `spells` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `campaign_user`
--
ALTER TABLE `campaign_user`
  ADD CONSTRAINT `campaign_user_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `campaign_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `capabilities`
--
ALTER TABLE `capabilities`
  ADD CONSTRAINT `capabilities_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `capability_creature`
--
ALTER TABLE `capability_creature`
  ADD CONSTRAINT `capability_creature_capability_id_foreign` FOREIGN KEY (`capability_id`) REFERENCES `capabilities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `capability_creature_creature_id_foreign` FOREIGN KEY (`creature_id`) REFERENCES `creatures` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `capability_specialization`
--
ALTER TABLE `capability_specialization`
  ADD CONSTRAINT `capability_specialization_capability_id_foreign` FOREIGN KEY (`capability_id`) REFERENCES `capabilities` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `capability_specialization_specialization_id_foreign` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `consumables`
--
ALTER TABLE `consumables`
  ADD CONSTRAINT `consumables_consumable_type_id_foreign` FOREIGN KEY (`consumable_type_id`) REFERENCES `consumable_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consumables_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `consumable_campaign`
--
ALTER TABLE `consumable_campaign`
  ADD CONSTRAINT `consumable_campaign_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consumable_campaign_consumable_id_foreign` FOREIGN KEY (`consumable_id`) REFERENCES `consumables` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `consumable_creature`
--
ALTER TABLE `consumable_creature`
  ADD CONSTRAINT `consumable_creature_consumable_id_foreign` FOREIGN KEY (`consumable_id`) REFERENCES `consumables` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consumable_creature_creature_id_foreign` FOREIGN KEY (`creature_id`) REFERENCES `creatures` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `consumable_resource`
--
ALTER TABLE `consumable_resource`
  ADD CONSTRAINT `consumable_resource_consumable_id_foreign` FOREIGN KEY (`consumable_id`) REFERENCES `consumables` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consumable_resource_resource_id_foreign` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `consumable_scenario`
--
ALTER TABLE `consumable_scenario`
  ADD CONSTRAINT `consumable_scenario_consumable_id_foreign` FOREIGN KEY (`consumable_id`) REFERENCES `consumables` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consumable_scenario_scenario_id_foreign` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `consumable_shop`
--
ALTER TABLE `consumable_shop`
  ADD CONSTRAINT `consumable_shop_consumable_id_foreign` FOREIGN KEY (`consumable_id`) REFERENCES `consumables` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `consumable_shop_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `consumable_types`
--
ALTER TABLE `consumable_types`
  ADD CONSTRAINT `consumable_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `creatures`
--
ALTER TABLE `creatures`
  ADD CONSTRAINT `creatures_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `creature_item`
--
ALTER TABLE `creature_item`
  ADD CONSTRAINT `creature_item_creature_id_foreign` FOREIGN KEY (`creature_id`) REFERENCES `creatures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `creature_item_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `creature_resource`
--
ALTER TABLE `creature_resource`
  ADD CONSTRAINT `creature_resource_creature_id_foreign` FOREIGN KEY (`creature_id`) REFERENCES `creatures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `creature_resource_resource_id_foreign` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `creature_spell`
--
ALTER TABLE `creature_spell`
  ADD CONSTRAINT `creature_spell_creature_id_foreign` FOREIGN KEY (`creature_id`) REFERENCES `creatures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `creature_spell_spell_id_foreign` FOREIGN KEY (`spell_id`) REFERENCES `spells` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `file_campaign`
--
ALTER TABLE `file_campaign`
  ADD CONSTRAINT `file_campaign_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `file_campaign_file_id_foreign` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `file_scenario`
--
ALTER TABLE `file_scenario`
  ADD CONSTRAINT `file_scenario_file_id_foreign` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `file_scenario_scenario_id_foreign` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `file_section`
--
ALTER TABLE `file_section`
  ADD CONSTRAINT `file_section_file_id_foreign` FOREIGN KEY (`file_id`) REFERENCES `files` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `file_section_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `items`
--
ALTER TABLE `items`
  ADD CONSTRAINT `items_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `items_item_type_id_foreign` FOREIGN KEY (`item_type_id`) REFERENCES `item_types` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `item_campaign`
--
ALTER TABLE `item_campaign`
  ADD CONSTRAINT `item_campaign_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_campaign_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `item_panoply`
--
ALTER TABLE `item_panoply`
  ADD CONSTRAINT `item_panoply_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_panoply_panoply_id_foreign` FOREIGN KEY (`panoply_id`) REFERENCES `panoplies` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `item_resource`
--
ALTER TABLE `item_resource`
  ADD CONSTRAINT `item_resource_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_resource_resource_id_foreign` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `item_scenario`
--
ALTER TABLE `item_scenario`
  ADD CONSTRAINT `item_scenario_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_scenario_scenario_id_foreign` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `item_shop`
--
ALTER TABLE `item_shop`
  ADD CONSTRAINT `item_shop_item_id_foreign` FOREIGN KEY (`item_id`) REFERENCES `items` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `item_shop_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `item_types`
--
ALTER TABLE `item_types`
  ADD CONSTRAINT `item_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `monsters`
--
ALTER TABLE `monsters`
  ADD CONSTRAINT `monsters_creature_id_foreign` FOREIGN KEY (`creature_id`) REFERENCES `creatures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `monsters_monster_race_id_foreign` FOREIGN KEY (`monster_race_id`) REFERENCES `monster_races` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `monster_campaign`
--
ALTER TABLE `monster_campaign`
  ADD CONSTRAINT `monster_campaign_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `monster_campaign_monster_id_foreign` FOREIGN KEY (`monster_id`) REFERENCES `monsters` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `monster_races`
--
ALTER TABLE `monster_races`
  ADD CONSTRAINT `monster_races_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `monster_races_id_super_race_foreign` FOREIGN KEY (`id_super_race`) REFERENCES `monster_races` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `monster_scenario`
--
ALTER TABLE `monster_scenario`
  ADD CONSTRAINT `monster_scenario_monster_id_foreign` FOREIGN KEY (`monster_id`) REFERENCES `monsters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `monster_scenario_scenario_id_foreign` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `npcs`
--
ALTER TABLE `npcs`
  ADD CONSTRAINT `npcs_classe_id_foreign` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `npcs_creature_id_foreign` FOREIGN KEY (`creature_id`) REFERENCES `creatures` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `npcs_specialization_id_foreign` FOREIGN KEY (`specialization_id`) REFERENCES `specializations` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `npc_campaign`
--
ALTER TABLE `npc_campaign`
  ADD CONSTRAINT `npc_campaign_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `npc_campaign_npc_id_foreign` FOREIGN KEY (`npc_id`) REFERENCES `npcs` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `npc_scenario`
--
ALTER TABLE `npc_scenario`
  ADD CONSTRAINT `npc_scenario_npc_id_foreign` FOREIGN KEY (`npc_id`) REFERENCES `npcs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `npc_scenario_scenario_id_foreign` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `pages`
--
ALTER TABLE `pages`
  ADD CONSTRAINT `pages_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `pages_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `pages` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `page_user`
--
ALTER TABLE `page_user`
  ADD CONSTRAINT `page_user_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `page_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `panoplies`
--
ALTER TABLE `panoplies`
  ADD CONSTRAINT `panoplies_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `resources`
--
ALTER TABLE `resources`
  ADD CONSTRAINT `resources_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `resources_resource_type_id_foreign` FOREIGN KEY (`resource_type_id`) REFERENCES `resource_types` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `resource_campaign`
--
ALTER TABLE `resource_campaign`
  ADD CONSTRAINT `resource_campaign_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `campaigns` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resource_campaign_resource_id_foreign` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `resource_scenario`
--
ALTER TABLE `resource_scenario`
  ADD CONSTRAINT `resource_scenario_resource_id_foreign` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resource_scenario_scenario_id_foreign` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `resource_shop`
--
ALTER TABLE `resource_shop`
  ADD CONSTRAINT `resource_shop_resource_id_foreign` FOREIGN KEY (`resource_id`) REFERENCES `resources` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `resource_shop_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `resource_types`
--
ALTER TABLE `resource_types`
  ADD CONSTRAINT `resource_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `scenarios`
--
ALTER TABLE `scenarios`
  ADD CONSTRAINT `scenarios_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `scenario_link`
--
ALTER TABLE `scenario_link`
  ADD CONSTRAINT `scenario_link_next_scenario_id_foreign` FOREIGN KEY (`next_scenario_id`) REFERENCES `scenarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `scenario_link_scenario_id_foreign` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `scenario_page`
--
ALTER TABLE `scenario_page`
  ADD CONSTRAINT `scenario_page_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `scenario_page_scenario_id_foreign` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `scenario_panoply`
--
ALTER TABLE `scenario_panoply`
  ADD CONSTRAINT `scenario_panoply_panoply_id_foreign` FOREIGN KEY (`panoply_id`) REFERENCES `panoplies` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `scenario_panoply_scenario_id_foreign` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `scenario_shop`
--
ALTER TABLE `scenario_shop`
  ADD CONSTRAINT `scenario_shop_scenario_id_foreign` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `scenario_shop_shop_id_foreign` FOREIGN KEY (`shop_id`) REFERENCES `shops` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `scenario_spell`
--
ALTER TABLE `scenario_spell`
  ADD CONSTRAINT `scenario_spell_scenario_id_foreign` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `scenario_spell_spell_id_foreign` FOREIGN KEY (`spell_id`) REFERENCES `spells` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `scenario_user`
--
ALTER TABLE `scenario_user`
  ADD CONSTRAINT `scenario_user_scenario_id_foreign` FOREIGN KEY (`scenario_id`) REFERENCES `scenarios` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `scenario_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `sections`
--
ALTER TABLE `sections`
  ADD CONSTRAINT `sections_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `sections_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `pages` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `section_user`
--
ALTER TABLE `section_user`
  ADD CONSTRAINT `section_user_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `section_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `shops`
--
ALTER TABLE `shops`
  ADD CONSTRAINT `shops_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shops_npc_id_foreign` FOREIGN KEY (`npc_id`) REFERENCES `npcs` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `specializations`
--
ALTER TABLE `specializations`
  ADD CONSTRAINT `specializations_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `spells`
--
ALTER TABLE `spells`
  ADD CONSTRAINT `spells_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `spell_invocation`
--
ALTER TABLE `spell_invocation`
  ADD CONSTRAINT `spell_invocation_monster_id_foreign` FOREIGN KEY (`monster_id`) REFERENCES `monsters` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `spell_invocation_spell_id_foreign` FOREIGN KEY (`spell_id`) REFERENCES `spells` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `spell_type`
--
ALTER TABLE `spell_type`
  ADD CONSTRAINT `spell_type_spell_id_foreign` FOREIGN KEY (`spell_id`) REFERENCES `spells` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `spell_type_spell_type_id_foreign` FOREIGN KEY (`spell_type_id`) REFERENCES `spell_types` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `spell_types`
--
ALTER TABLE `spell_types`
  ADD CONSTRAINT `spell_types_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

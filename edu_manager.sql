-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1
-- Généré le : mer. 05 août 2026 à 18:22
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
-- Base de données : `edu_manager`
--

-- --------------------------------------------------------

--
-- Structure de la table `annee_academique`
--

CREATE TABLE `annee_academique` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `etablissement_id` bigint(20) UNSIGNED NOT NULL,
  `libelle` varchar(255) NOT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `annee_academique`
--

INSERT INTO `annee_academique` (`id`, `tenant_id`, `etablissement_id`, `libelle`, `date_debut`, `date_fin`, `statut`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-2027', '2026-07-03', '2027-07-03', 'active', '2026-07-03 11:57:41', '2026-07-03 11:57:41');

-- --------------------------------------------------------

--
-- Structure de la table `bulletins`
--

CREATE TABLE `bulletins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `eleve_id` bigint(20) UNSIGNED NOT NULL,
  `classe_id` bigint(20) UNSIGNED NOT NULL,
  `moyenne_generale` decimal(5,2) DEFAULT NULL,
  `rang` int(11) DEFAULT NULL,
  `appreciation` text DEFAULT NULL,
  `trimestre` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `etablissement_id` bigint(20) UNSIGNED DEFAULT NULL,
  `annee_academique_id` bigint(20) UNSIGNED DEFAULT NULL,
  `total_heures` decimal(8,2) NOT NULL DEFAULT 0.00,
  `absences` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `total_coefficients` decimal(8,2) NOT NULL DEFAULT 0.00,
  `total_points` decimal(10,2) NOT NULL DEFAULT 0.00,
  `resultat_classe` varchar(255) DEFAULT NULL,
  `decision` varchar(255) DEFAULT NULL,
  `observation_conseil` text DEFAULT NULL,
  `date` date DEFAULT NULL,
  `signature_professeur_principal` varchar(255) DEFAULT NULL,
  `signature_directeur` varchar(255) DEFAULT NULL,
  `distinctions` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`distinctions`)),
  `mention` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bulletins`
--

INSERT INTO `bulletins` (`id`, `tenant_id`, `eleve_id`, `classe_id`, `moyenne_generale`, `rang`, `appreciation`, `trimestre`, `created_at`, `updated_at`, `etablissement_id`, `annee_academique_id`, `total_heures`, `absences`, `total_coefficients`, `total_points`, `resultat_classe`, `decision`, `observation_conseil`, `date`, `signature_professeur_principal`, `signature_directeur`, `distinctions`, `mention`) VALUES
(1, 1, 1, 1, 12.55, 1, NULL, 't1', '2026-07-11 01:16:35', '2026-07-13 13:00:48', 1, 1, 0.00, 0, 8.00, 100.40, NULL, 'Admis(e)', 'Résultats corrects. Des efforts sont encore attendus.', '2026-07-11', 'Young', 'Amany ange marie grace', '[\"Felicitations\"]', 'Passable');

-- --------------------------------------------------------

--
-- Structure de la table `bulletin_discipline`
--

CREATE TABLE `bulletin_discipline` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bulletin_id` bigint(20) UNSIGNED NOT NULL,
  `matiere_id` bigint(20) UNSIGNED DEFAULT NULL,
  `discipline` varchar(255) NOT NULL,
  `interrogation` decimal(5,2) DEFAULT NULL,
  `devoir` decimal(5,2) DEFAULT NULL,
  `composition` decimal(5,2) DEFAULT NULL,
  `moyenne` decimal(6,2) DEFAULT NULL,
  `coefficient` decimal(6,2) NOT NULL DEFAULT 1.00,
  `moyenne_coefficient` decimal(8,2) DEFAULT NULL,
  `rang` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `mention` varchar(255) DEFAULT NULL,
  `professeur` varchar(255) DEFAULT NULL,
  `signature` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `bulletin_discipline`
--

INSERT INTO `bulletin_discipline` (`id`, `tenant_id`, `bulletin_id`, `matiere_id`, `discipline`, `interrogation`, `devoir`, `composition`, `moyenne`, `coefficient`, `moyenne_coefficient`, `rang`, `mention`, `professeur`, `signature`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'Mathématiques', NULL, NULL, NULL, 14.30, 4.00, 57.20, 0, 'Assez Bien', NULL, NULL, '2026-07-11 01:16:35', '2026-07-11 01:16:35'),
(2, 1, 1, 2, 'Physique-chimie', NULL, NULL, NULL, 10.80, 4.00, 43.20, 0, 'Passable', NULL, NULL, '2026-07-11 01:16:35', '2026-07-11 01:16:35');

-- --------------------------------------------------------

--
-- Structure de la table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('laravel-cache-aketchisalome11@mail.com|127.0.0.1', 'i:1;', 1784809019),
('laravel-cache-aketchisalome11@mail.com|127.0.0.1:timer', 'i:1784809019;', 1784809019);

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
-- Structure de la table `classes`
--

CREATE TABLE `classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `etablissement_id` bigint(20) UNSIGNED NOT NULL,
  `niveau_id` bigint(20) UNSIGNED NOT NULL,
  `filiere_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nom` varchar(255) NOT NULL,
  `capacite` int(11) NOT NULL DEFAULT 50,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `classes`
--

INSERT INTO `classes` (`id`, `tenant_id`, `etablissement_id`, `niveau_id`, `filiere_id`, `nom`, `capacite`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, NULL, 'Tle D1', 40, '2026-07-03 11:53:22', '2026-07-03 11:53:22');

-- --------------------------------------------------------

--
-- Structure de la table `classe_enseignant`
--

CREATE TABLE `classe_enseignant` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `classe_id` bigint(20) UNSIGNED NOT NULL,
  `enseignant_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `classe_enseignant`
--

INSERT INTO `classe_enseignant` (`id`, `classe_id`, `enseignant_id`, `created_at`, `updated_at`) VALUES
(5, 1, 7, '2026-07-23 12:20:43', '2026-07-23 12:20:43');

-- --------------------------------------------------------

--
-- Structure de la table `classe_serie`
--

CREATE TABLE `classe_serie` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `serie_id` bigint(20) UNSIGNED NOT NULL,
  `classe_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `classe_serie`
--

INSERT INTO `classe_serie` (`id`, `serie_id`, `classe_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2026-07-03 11:55:36', '2026-07-03 11:55:36');

-- --------------------------------------------------------

--
-- Structure de la table `depense`
--

CREATE TABLE `depense` (
  `id_depense` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `libel_depense` varchar(255) NOT NULL,
  `montant` int(11) NOT NULL,
  `categorie` varchar(255) DEFAULT NULL,
  `date_depense` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `eleves`
--

CREATE TABLE `eleves` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `etablissement_id` bigint(20) UNSIGNED NOT NULL,
  `classe_id` bigint(20) UNSIGNED DEFAULT NULL,
  `id_serie` bigint(20) UNSIGNED DEFAULT NULL,
  `niveau_id` bigint(20) UNSIGNED DEFAULT NULL,
  `parent_id` bigint(20) UNSIGNED DEFAULT NULL,
  `matricule` varchar(255) NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `sexe` enum('Masculin','Féminin') DEFAULT NULL,
  `date_naissance` date DEFAULT NULL,
  `lieu_naissance` varchar(255) DEFAULT NULL,
  `nationalite` varchar(255) DEFAULT NULL,
  `interne` tinyint(1) NOT NULL DEFAULT 0,
  `affecte` tinyint(1) NOT NULL DEFAULT 0,
  `ancien_etablissement` varchar(255) DEFAULT NULL,
  `photo` text DEFAULT NULL,
  `statut` enum('actif','suspendu','transfert') NOT NULL DEFAULT 'actif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `eleves`
--

INSERT INTO `eleves` (`id`, `tenant_id`, `etablissement_id`, `classe_id`, `id_serie`, `niveau_id`, `parent_id`, `matricule`, `nom`, `prenom`, `sexe`, `date_naissance`, `lieu_naissance`, `nationalite`, `interne`, `affecte`, `ancien_etablissement`, `photo`, `statut`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 1, 1, 3, '130976490A', 'Yapi', 'sara esther', 'Féminin', '2004-10-11', 'Cocody', 'Ivoirienne', 0, 0, NULL, 'eleves/FGHZV8MMUwtHbz9EYBBEJjxuWciTfdnfPiutwlDv.jpg', 'actif', '2026-07-03 11:55:16', '2026-07-04 09:30:46'),
(2, 1, 1, 1, 1, 1, 3, '13567488A', 'Amany', 'ange marie grace', 'Féminin', '2004-08-10', 'Cocody', 'Ivoirienne', 1, 1, NULL, 'eleves/HIk1fPJcFWEyZvBhB5bpoFVqzHZCEUCHKEfYJFVy.jpg', 'actif', '2026-07-13 12:59:25', '2026-07-13 12:59:25');

-- --------------------------------------------------------

--
-- Structure de la table `emploi_temps`
--

CREATE TABLE `emploi_temps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `etablissement_id` bigint(20) UNSIGNED DEFAULT NULL,
  `annee_academique_id` bigint(20) UNSIGNED DEFAULT NULL,
  `classe_id` bigint(20) UNSIGNED NOT NULL,
  `serie_id` bigint(20) UNSIGNED DEFAULT NULL,
  `matiere_id` bigint(20) UNSIGNED NOT NULL,
  `enseignant_id` bigint(20) UNSIGNED NOT NULL,
  `jour` enum('lundi','mardi','mercredi','jeudi','vendredi','samedi') NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `slot_key` varchar(64) DEFAULT NULL,
  `salle` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `emploi_temps`
--

INSERT INTO `emploi_temps` (`id`, `tenant_id`, `etablissement_id`, `annee_academique_id`, `classe_id`, `serie_id`, `matiere_id`, `enseignant_id`, `jour`, `heure_debut`, `heure_fin`, `slot_key`, `salle`, `created_at`, `updated_at`) VALUES
(17, 1, 1, 1, 1, 1, 1, 5, 'lundi', '07:00:00', '07:55:00', 'slot-1', NULL, '2026-07-22 20:51:45', '2026-07-22 20:51:45');

-- --------------------------------------------------------

--
-- Structure de la table `emploi_temps_slots`
--

CREATE TABLE `emploi_temps_slots` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `enseignant_id` bigint(20) UNSIGNED NOT NULL,
  `slot_key` varchar(64) NOT NULL,
  `heure_debut` time NOT NULL,
  `heure_fin` time NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `emploi_temps_slots`
--

INSERT INTO `emploi_temps_slots` (`id`, `tenant_id`, `enseignant_id`, `slot_key`, `heure_debut`, `heure_fin`, `created_at`, `updated_at`) VALUES
(73, 1, 5, 'slot-1', '07:00:00', '07:55:00', NULL, NULL),
(74, 1, 5, 'slot-2', '07:55:00', '08:50:00', NULL, NULL),
(75, 1, 5, 'slot-3', '08:50:00', '09:45:00', NULL, NULL),
(76, 1, 5, 'slot-4', '10:00:00', '10:55:00', NULL, NULL),
(77, 1, 5, 'slot-5', '10:55:00', '11:50:00', NULL, NULL),
(78, 1, 5, 'slot-6', '14:00:00', '15:00:00', NULL, NULL),
(79, 1, 5, 'slot-7', '15:00:00', '16:00:00', NULL, NULL),
(80, 1, 5, 'slot-8', '16:00:00', '17:00:00', NULL, NULL),
(81, 1, 5, 'slot-9', '17:00:00', '18:00:00', NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `enseignants`
--

CREATE TABLE `enseignants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `etablissement_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nom` varchar(255) NOT NULL,
  `prenoms` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `matricule` varchar(255) DEFAULT NULL,
  `nombre_annees_enseignement` int(10) UNSIGNED DEFAULT NULL,
  `sexe` enum('Masculin','Féminin') DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `matiere_id` bigint(20) UNSIGNED DEFAULT NULL,
  `specialite` varchar(255) DEFAULT NULL,
  `statut` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `enseignants`
--

INSERT INTO `enseignants` (`id`, `tenant_id`, `etablissement_id`, `user_id`, `nom`, `prenoms`, `email`, `telephone`, `matricule`, `nombre_annees_enseignement`, `sexe`, `photo`, `password`, `matiere_id`, `specialite`, `statut`, `created_at`, `updated_at`) VALUES
(7, 1, 1, 5, 'N\'takpé', 'corine', 'corine@gmail.com', '0709527862', '123OP133A', 1, 'Féminin', NULL, '$2y$12$gHSHt3tX0x/dyY63dwMZr.ZbB7QKMGaxyH79Tu04CvkaixbQfYn/q', 2, 'Physique-chimie', 'active', '2026-07-23 12:20:43', '2026-07-23 12:31:58');

-- --------------------------------------------------------

--
-- Structure de la table `enseignant_matiere`
--

CREATE TABLE `enseignant_matiere` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `enseignant_id` bigint(20) UNSIGNED NOT NULL,
  `matiere_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `enseignant_matiere`
--

INSERT INTO `enseignant_matiere` (`id`, `enseignant_id`, `matiere_id`, `created_at`, `updated_at`) VALUES
(14, 3, 2, NULL, NULL),
(15, 3, 1, NULL, NULL),
(18, 7, 2, '2026-07-23 12:20:43', '2026-07-23 12:20:43');

-- --------------------------------------------------------

--
-- Structure de la table `enseignant_serie`
--

CREATE TABLE `enseignant_serie` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `enseignant_id` bigint(20) UNSIGNED NOT NULL,
  `serie_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `enseignant_serie`
--

INSERT INTO `enseignant_serie` (`id`, `enseignant_id`, `serie_id`, `created_at`, `updated_at`) VALUES
(5, 7, 1, '2026-07-23 12:20:43', '2026-07-23 12:20:43');

-- --------------------------------------------------------

--
-- Structure de la table `etablissements`
--

CREATE TABLE `etablissements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `acronyme` varchar(100) DEFAULT NULL,
  `type_etablissement` enum('primaire','college','lycee','universite','grande_ecole') NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `logo` varchar(255) DEFAULT NULL,
  `statut` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `etablissements`
--

INSERT INTO `etablissements` (`id`, `tenant_id`, `nom`, `acronyme`, `type_etablissement`, `email`, `telephone`, `adresse`, `logo`, `statut`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Lycée moderne anyama', 'LMA', 'lycee', 'anyama@mail.com', '00000000', 'Anyama', 'logos/f9888403-16b7-4e50-8f2e-9e08c9d0bd30.jpeg', 'active', '2026-06-23 22:50:43', '2026-07-03 11:12:07', NULL);

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
-- Structure de la table `filieres`
--

CREATE TABLE `filieres` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `etablissement_id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
-- Structure de la table `matieres`
--

CREATE TABLE `matieres` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `coefficient` int(11) NOT NULL DEFAULT 1,
  `serie` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `matieres`
--

INSERT INTO `matieres` (`id`, `tenant_id`, `nom`, `coefficient`, `serie`, `created_at`, `updated_at`) VALUES
(1, 1, 'Mathématiques', 4, 1, '2026-06-23 23:13:36', '2026-06-23 23:13:36'),
(2, 1, 'Physique-chimie', 4, 1, '2026-06-23 23:16:56', '2026-06-23 23:16:56');

-- --------------------------------------------------------

--
-- Structure de la table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED NOT NULL,
  `receiver_id` bigint(20) UNSIGNED NOT NULL,
  `message` text NOT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2026_05_27_093302_create_permission_tables', 1),
(4, '2026_05_27_095008_create_all_tables', 1),
(5, '2026_05_28_002706_create_sessions_table', 1),
(6, '2026_05_28_100000_add_indices_to_etablissements', 1),
(7, '2026_05_28_213203_create_etablissements_table', 1),
(8, '2026_05_29_000000_create_clients_table', 1),
(9, '2026_05_30_000001_replace_entreprise_with_etablissement_id_on_clients_table', 1),
(10, '2026_05_30_051245_create_password_otps_table', 1),
(11, '2026_05_30_051537_update_password_otps', 1),
(12, '2026_05_31_132430_add_subscription_fields_to_subscriptions_table', 1),
(13, '2026_05_31_132446_update_subscriptions_columns', 1),
(14, '2026_05_31_142921_fix_subscriptions_plan_id_default', 1),
(15, '2026_05_31_200000_add_timestamps_to_subscriptions_table', 1),
(16, '2026_05_31_999998_add_tenant_id_to_clients_table', 1),
(17, '2026_06_01_000000_create_subscriptions_types', 1),
(18, '2026_06_01_999996_subscription_add_created_by_and_fcfa_view_columns', 1),
(19, '2026_06_01_999997_fix_subscriptions_dates_default', 1),
(20, '2026_06_01_999998_fix_subscriptions_plan_id_default', 1),
(21, '2026_06_01_999999_fix_subscriptions_tenant_id_default', 1),
(22, '2026_06_02_000000_migrate_clients_to_users', 1),
(23, '2026_06_02_000001_drop_old_client_tables', 1),
(24, '2026_06_02_140124_add_etablissement_id_and_client_fields_to_users', 1),
(25, '2026_06_04_000001_normalize_plans_and_subscriptions', 1),
(26, '2026_06_04_000002_fix_plans_columns', 1),
(27, '2026_06_04_000003_add_subscription_type_to_plans', 1),
(28, '2026_06_04_000004_create_payments_table', 1),
(29, '2026_06_05_084724_normalize_english_abonnements_columns', 1),
(30, '2026_06_05_084729_normalize_english_abonnements_columns_subscriptions', 1),
(31, '2026_06_05_084731_normalize_english_abonnements_columns_payments', 1),
(32, '2026_06_05_200000_add_status_columns_to_subscription_payments_flow', 1),
(33, '2026_06_06_181844_add_client_id_to_users_table', 1),
(34, '2026_06_06_210000_update_users_statut_enum', 1),
(35, '2026_06_20_000001_add_client_crud_relation_columns', 1),
(36, '2026_06_20_000002_create_depense_table', 1),
(37, '2026_06_20_000003_create_enseignants_tables_if_missing', 1),
(38, '2026_06_22_111123_add_ancien_etablissement_to_eleves_table', 1),
(39, '2026_06_22_184850_fix_sexe_eleves_enum', 1),
(40, '2026_06_23_000001_create_series_table', 1),
(41, '2026_06_23_000002_migrate_matiere_serie_text_to_series_id', 1),
(43, '2026_06_23_000003_add_remember_token_to_users_table', 2),
(44, '2026_06_24_000001_add_serie_column_to_matieres_table', 3),
(45, '2026_06_26_000002_create_bulletin_discipline_table', 4),
(46, '2026_06_26_000001_create_bulletins_table', 5),
(47, '2026_06_29_000001_link_series_to_classes_and_eleves', 5),
(48, '2026_06_30_000001_create_classe_serie_pivot', 5),
(49, '2026_07_03_000001_change_logo_to_nullable_string_on_etablissements_table', 5),
(50, '2026_07_04_092605_add_nationalite_to_eleves_table', 6),
(51, '2026_07_10_000001_rename_bulletin_appreciation_to_mention', 7),
(52, '2026_07_13_000001_add_boarding_and_assignment_fields_to_eleves_table', 8),
(53, '2026_07_03_000002_create_serie_matieres_table', 9),
(54, '2026_07_04_000001_ensure_classe_serie_pivot', 9),
(55, '2026_07_04_000002_ensure_serie_matieres_table', 9),
(56, '2026_07_14_000001_add_delivery_fields_to_notifications_table', 10),
(57, '2026_07_15_000001_create_classe_enseignant_table', 11),
(58, '2026_07_15_000002_add_context_to_emploi_temps_table', 11),
(59, '2026_07_16_000001_extend_enseignants_and_teacher_schedules', 12),
(60, '2026_07_18_000001_add_slot_key_to_emploi_temps_table', 13);

-- --------------------------------------------------------

--
-- Structure de la table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `niveaux`
--

CREATE TABLE `niveaux` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `etablissement_id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `niveaux`
--

INSERT INTO `niveaux` (`id`, `tenant_id`, `etablissement_id`, `nom`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Terminal', '2026-07-03 11:53:00', '2026-07-03 11:53:00');

-- --------------------------------------------------------

--
-- Structure de la table `notes`
--

CREATE TABLE `notes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `eleve_id` bigint(20) UNSIGNED NOT NULL,
  `classe_id` bigint(20) UNSIGNED NOT NULL,
  `matiere_id` bigint(20) UNSIGNED NOT NULL,
  `note` decimal(5,2) NOT NULL,
  `periode` varchar(100) DEFAULT NULL,
  `appreciation` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `sender_id` bigint(20) UNSIGNED DEFAULT NULL,
  `titre` varchar(255) DEFAULT NULL,
  `message` text DEFAULT NULL,
  `audience` varchar(255) DEFAULT NULL,
  `category` varchar(255) DEFAULT NULL,
  `priority` varchar(255) NOT NULL DEFAULT 'normal',
  `sent_at` timestamp NULL DEFAULT NULL,
  `statut` enum('unread','read') NOT NULL DEFAULT 'unread',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `notification_recipients`
--

CREATE TABLE `notification_recipients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `notification_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `password_otps`
--

CREATE TABLE `password_otps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `email` varchar(255) NOT NULL,
  `otp_code` varchar(64) NOT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
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
-- Structure de la table `payments`
--

CREATE TABLE `payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `subscription_id` bigint(20) UNSIGNED NOT NULL,
  `montant` int(11) NOT NULL,
  `methode_paiement` varchar(100) DEFAULT NULL,
  `reference_paiement` varchar(255) DEFAULT NULL,
  `date_paiement` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `amount` int(11) DEFAULT NULL,
  `payment_method` varchar(100) DEFAULT NULL,
  `status` enum('pending','paid','failed') NOT NULL DEFAULT 'paid',
  `statut` enum('pending','paid','failed') NOT NULL DEFAULT 'paid'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `plans`
--

CREATE TABLE `plans` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom` varchar(255) NOT NULL,
  `prix` int(11) NOT NULL,
  `subscription_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `max_ecoles` int(11) NOT NULL DEFAULT 1,
  `max_users` int(11) NOT NULL DEFAULT 10,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `duree` int(11) NOT NULL DEFAULT 12,
  `nombre_utilisateurs` int(11) NOT NULL DEFAULT 10,
  `nombre_enseignants` int(11) NOT NULL DEFAULT 10,
  `nombre_classes` int(11) NOT NULL DEFAULT 10,
  `statut` enum('active','inactive') NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `scolarites`
--

CREATE TABLE `scolarites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `eleve_id` bigint(20) UNSIGNED NOT NULL,
  `montant_total` int(11) NOT NULL DEFAULT 0,
  `montant_paye` int(11) NOT NULL DEFAULT 0,
  `reste` int(11) NOT NULL DEFAULT 0,
  `annee_scolaire` varchar(100) DEFAULT NULL,
  `statut` enum('paye','partiel','impaye') NOT NULL DEFAULT 'impaye',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `series`
--

CREATE TABLE `series` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `id_classe` bigint(20) UNSIGNED DEFAULT NULL,
  `nom_serie` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `series`
--

INSERT INTO `series` (`id`, `tenant_id`, `id_classe`, `nom_serie`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Série D', '2026-06-23 23:04:04', '2026-07-03 11:55:36');

-- --------------------------------------------------------

--
-- Structure de la table `serie_matieres`
--

CREATE TABLE `serie_matieres` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `serie_id` bigint(20) UNSIGNED NOT NULL,
  `matiere_id` bigint(20) UNSIGNED NOT NULL,
  `coefficient` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `serie_matieres`
--

INSERT INTO `serie_matieres` (`id`, `serie_id`, `matiere_id`, `coefficient`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 4, '2026-07-03 11:31:20', '2026-07-03 11:40:34'),
(2, 1, 2, 4, '2026-07-03 11:31:20', '2026-07-03 11:31:20');

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

--
-- Déchargement des données de la table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('cNskU0HMPsbIjxKG7bpmRpSOkCACBlUYC0Sfz6tw', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoibHZkYnpNM2dSZDI2ZHVlSFBjVnFZN3h1QjZaMFVES05VWUcwSlRUdiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ub3RpZmljYXRpb25zIjtzOjU6InJvdXRlIjtzOjE5OiJub3RpZmljYXRpb25zLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1784942601),
('s1bqgioxFjgbVR0V5nZM2xraUa3ckT5qtjfOr4zU', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/150.0.0.0 Safari/537.36 Edg/150.0.0.0', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiaEdTWEJma2tuN0NmY1JzeFBXWHdNems3NmlKTVM2bUtwZWdGOXgzQiI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9ub3RpZmljYXRpb25zIjtzOjU6InJvdXRlIjtzOjE5OiJub3RpZmljYXRpb25zLmluZGV4Ijt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1784942640);

-- --------------------------------------------------------

--
-- Structure de la table `subscriptions`
--

CREATE TABLE `subscriptions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `plan_id` bigint(20) UNSIGNED DEFAULT NULL,
  `date_debut` date DEFAULT NULL,
  `date_fin` date DEFAULT NULL,
  `statut` enum('active','expired','cancelled') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `type` varchar(255) NOT NULL,
  `price` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `amount` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `subscription_types`
--

CREATE TABLE `subscription_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `default_duration` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `subscription_types`
--

INSERT INTO `subscription_types` (`id`, `type`, `default_duration`, `status`, `created_at`, `updated_at`) VALUES
(1, 'Mensuel', NULL, 'active', '2026-07-15 12:40:07', '2026-07-15 12:40:07');

-- --------------------------------------------------------

--
-- Structure de la table `tenants`
--

CREATE TABLE `tenants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `nom_entreprise` varchar(255) NOT NULL,
  `nom_responsable` varchar(255) DEFAULT NULL,
  `prenom_responsable` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `adresse` text DEFAULT NULL,
  `statut` enum('active','suspended','blocked') NOT NULL DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `transactions`
--

CREATE TABLE `transactions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('income','expense') NOT NULL,
  `montant` int(11) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL DEFAULT 1,
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `etablissement_id` bigint(20) UNSIGNED DEFAULT NULL,
  `nom` varchar(255) NOT NULL,
  `prenom` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `telephone` varchar(50) DEFAULT NULL,
  `adresse` varchar(255) DEFAULT NULL,
  `ville` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `image` text DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `role` enum('Sadmin','Client','Personnel','Enseignant','Parent') NOT NULL,
  `statut` enum('actif','bloqué') NOT NULL DEFAULT 'actif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `tenant_id`, `client_id`, `etablissement_id`, `nom`, `prenom`, `email`, `email_verified_at`, `telephone`, `adresse`, `ville`, `password`, `image`, `photo`, `role`, `statut`, `created_at`, `updated_at`, `remember_token`) VALUES
(1, 1, NULL, NULL, 'Tra', 'bi dorgeles', 'sylvianneparisot8@gmail.com', NULL, '0709527852', 'Abobo pk18', 'Abidjan ', '$2y$12$iYlTNcP4KOBx0WR60kIKgO.dE4WO3JovlnVJhs925/rxkr2MstkY.', 'profile-images/1_kldU1qv1vJksXpYIOv3M.jpg', NULL, 'Sadmin', 'actif', '2026-06-23 22:43:52', '2026-07-15 10:24:25', 'yeVHMfvQlsy62lgIWqJfUPkkYnOwiVJp3L2zQ9AdkTX5TY7QRIjelWgJpxlp'),
(2, 1, NULL, 1, 'Amany', 'ange marie grace', 'dorgeles@mail.com', NULL, '0575096534', 'Yopougon toit rouge', 'Abidjan', '$2y$12$X.Oa6tBcmT0GY2cK7.0l1OEd.v6DaeuzkdtBZbc.4MfL6yi1wxt6m', 'profile-images/2_j9Z5r7D8uxMHGH2YtmsE.jpg', NULL, 'Client', 'actif', '2026-06-23 22:51:51', '2026-07-15 12:41:31', 'VF1nEb96g8P7U2hom6K3FH5KT3Vhd0V6W2pKFttIxS3ZjUTU1CkbVxKLg3jd'),
(3, 1, NULL, NULL, 'Yapi', 'Antoine', 'papa@mail.com', NULL, '0504241268', NULL, NULL, '$2y$12$i8GY2NFsg43TNhli5K9u3.sMqDnMsAvZ4z7i49xUtS0aKwM6yt1n.', NULL, NULL, 'Parent', 'actif', '2026-07-03 11:55:16', '2026-07-03 11:55:16', NULL),
(4, 1, 2, 1, 'Die', 'osée emmanuel', 'T@mail.com', NULL, '0707762368', NULL, NULL, '$2y$12$l2pB0rqUARY9DKwQDkXO.e2G7q3zzl9Ehh5Q.gP/AyrBPhAcJG/9y', NULL, NULL, 'Personnel', 'actif', '2026-07-23 11:10:44', '2026-07-23 11:25:57', NULL),
(5, 1, NULL, 1, 'N\'takpé', 'corine', 'corine@gmail.com', NULL, '0709527862', NULL, NULL, '$2y$12$1zfdYyVg/Z5nyQ7PnA5zN.vJQgXSsQ/StYBPP.1hzd/qo6gvZcS2C', 'profile-images/5_sHJTDuCnZjsioPfTlJYh.jpg', NULL, 'Enseignant', 'actif', '2026-07-23 12:31:58', '2026-07-23 14:52:39', NULL);

-- --------------------------------------------------------

--
-- Structure de la table `versements`
--

CREATE TABLE `versements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tenant_id` bigint(20) UNSIGNED NOT NULL,
  `scolarite_id` bigint(20) UNSIGNED NOT NULL,
  `montant` int(11) NOT NULL,
  `date_versement` date DEFAULT NULL,
  `methode` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `annee_academique`
--
ALTER TABLE `annee_academique`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `bulletins`
--
ALTER TABLE `bulletins`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `bulletin_discipline`
--
ALTER TABLE `bulletin_discipline`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bulletin_discipline_bulletin_id_discipline_index` (`bulletin_id`,`discipline`),
  ADD KEY `bulletin_discipline_matiere_id_foreign` (`matiere_id`);

--
-- Index pour la table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Index pour la table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Index pour la table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `classe_enseignant`
--
ALTER TABLE `classe_enseignant`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `classe_enseignant_classe_id_enseignant_id_unique` (`classe_id`,`enseignant_id`),
  ADD KEY `classe_enseignant_enseignant_id_foreign` (`enseignant_id`);

--
-- Index pour la table `classe_serie`
--
ALTER TABLE `classe_serie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `classe_serie_serie_id_classe_id_unique` (`serie_id`,`classe_id`),
  ADD KEY `classe_serie_classe_id_foreign` (`classe_id`);

--
-- Index pour la table `depense`
--
ALTER TABLE `depense`
  ADD PRIMARY KEY (`id_depense`),
  ADD KEY `depense_tenant_id_index` (`tenant_id`);

--
-- Index pour la table `eleves`
--
ALTER TABLE `eleves`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `eleves_matricule_unique` (`matricule`),
  ADD KEY `eleves_id_serie_foreign` (`id_serie`);

--
-- Index pour la table `emploi_temps`
--
ALTER TABLE `emploi_temps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `emploi_temps_tenant_id_classe_id_jour_index` (`tenant_id`,`classe_id`,`jour`),
  ADD KEY `emploi_temps_teacher_slot_index` (`tenant_id`,`enseignant_id`,`slot_key`);

--
-- Index pour la table `emploi_temps_slots`
--
ALTER TABLE `emploi_temps_slots`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `emploi_temps_slots_teacher_key_unique` (`tenant_id`,`enseignant_id`,`slot_key`);

--
-- Index pour la table `enseignants`
--
ALTER TABLE `enseignants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enseignants_matricule_unique` (`matricule`);

--
-- Index pour la table `enseignant_matiere`
--
ALTER TABLE `enseignant_matiere`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `enseignant_serie`
--
ALTER TABLE `enseignant_serie`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `enseignant_serie_enseignant_id_serie_id_unique` (`enseignant_id`,`serie_id`),
  ADD KEY `enseignant_serie_serie_id_foreign` (`serie_id`);

--
-- Index pour la table `etablissements`
--
ALTER TABLE `etablissements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `etablissements_tenant_nom_idx` (`tenant_id`,`nom`),
  ADD KEY `etablissements_tenant_email_idx` (`tenant_id`,`email`),
  ADD KEY `etablissements_tenant_acronyme_idx` (`tenant_id`,`acronyme`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `filieres`
--
ALTER TABLE `filieres`
  ADD PRIMARY KEY (`id`);

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
-- Index pour la table `matieres`
--
ALTER TABLE `matieres`
  ADD PRIMARY KEY (`id`),
  ADD KEY `matieres_serie_foreign` (`serie`);

--
-- Index pour la table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Index pour la table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Index pour la table `niveaux`
--
ALTER TABLE `niveaux`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `notes`
--
ALTER TABLE `notes`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_sender_id_foreign` (`sender_id`);

--
-- Index pour la table `notification_recipients`
--
ALTER TABLE `notification_recipients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `notification_recipients_notification_id_user_id_unique` (`notification_id`,`user_id`),
  ADD KEY `notification_recipients_user_id_read_at_index` (`user_id`,`read_at`);

--
-- Index pour la table `password_otps`
--
ALTER TABLE `password_otps`
  ADD PRIMARY KEY (`id`),
  ADD KEY `password_otps_email_index` (`email`),
  ADD KEY `password_otps_expires_at_index` (`expires_at`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Index pour la table `plans`
--
ALTER TABLE `plans`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plans_subscription_type_id_foreign` (`subscription_type_id`);

--
-- Index pour la table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Index pour la table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Index pour la table `scolarites`
--
ALTER TABLE `scolarites`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `series`
--
ALTER TABLE `series`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `series_tenant_id_nom_serie_unique` (`tenant_id`,`nom_serie`),
  ADD KEY `series_tenant_id_index` (`tenant_id`),
  ADD KEY `series_id_classe_foreign` (`id_classe`);

--
-- Index pour la table `serie_matieres`
--
ALTER TABLE `serie_matieres`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serie_matieres_serie_id_matiere_id_unique` (`serie_id`,`matiere_id`),
  ADD KEY `serie_matieres_matiere_id_foreign` (`matiere_id`);

--
-- Index pour la table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Index pour la table `subscriptions`
--
ALTER TABLE `subscriptions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscriptions_name_type_unique` (`name`,`type`);

--
-- Index pour la table `subscription_types`
--
ALTER TABLE `subscription_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `subscription_types_type_unique` (`type`);

--
-- Index pour la table `tenants`
--
ALTER TABLE `tenants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tenants_email_unique` (`email`);

--
-- Index pour la table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_etablissement_id_foreign` (`etablissement_id`);

--
-- Index pour la table `versements`
--
ALTER TABLE `versements`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `annee_academique`
--
ALTER TABLE `annee_academique`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `bulletins`
--
ALTER TABLE `bulletins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `bulletin_discipline`
--
ALTER TABLE `bulletin_discipline`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT pour la table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `classe_enseignant`
--
ALTER TABLE `classe_enseignant`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `classe_serie`
--
ALTER TABLE `classe_serie`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `depense`
--
ALTER TABLE `depense`
  MODIFY `id_depense` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `eleves`
--
ALTER TABLE `eleves`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT pour la table `emploi_temps`
--
ALTER TABLE `emploi_temps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `emploi_temps_slots`
--
ALTER TABLE `emploi_temps_slots`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=82;

--
-- AUTO_INCREMENT pour la table `enseignants`
--
ALTER TABLE `enseignants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT pour la table `enseignant_matiere`
--
ALTER TABLE `enseignant_matiere`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT pour la table `enseignant_serie`
--
ALTER TABLE `enseignant_serie`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `etablissements`
--
ALTER TABLE `etablissements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `filieres`
--
ALTER TABLE `filieres`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `matieres`
--
ALTER TABLE `matieres`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

--
-- AUTO_INCREMENT pour la table `niveaux`
--
ALTER TABLE `niveaux`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `notes`
--
ALTER TABLE `notes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT pour la table `notification_recipients`
--
ALTER TABLE `notification_recipients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT pour la table `password_otps`
--
ALTER TABLE `password_otps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `plans`
--
ALTER TABLE `plans`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `scolarites`
--
ALTER TABLE `scolarites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `series`
--
ALTER TABLE `series`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT pour la table `serie_matieres`
--
ALTER TABLE `serie_matieres`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `subscriptions`
--
ALTER TABLE `subscriptions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `subscription_types`
--
ALTER TABLE `subscription_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT pour la table `tenants`
--
ALTER TABLE `tenants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT pour la table `versements`
--
ALTER TABLE `versements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `bulletin_discipline`
--
ALTER TABLE `bulletin_discipline`
  ADD CONSTRAINT `bulletin_discipline_bulletin_id_foreign` FOREIGN KEY (`bulletin_id`) REFERENCES `bulletins` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bulletin_discipline_matiere_id_foreign` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `classe_enseignant`
--
ALTER TABLE `classe_enseignant`
  ADD CONSTRAINT `classe_enseignant_classe_id_foreign` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `classe_enseignant_enseignant_id_foreign` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `classe_serie`
--
ALTER TABLE `classe_serie`
  ADD CONSTRAINT `classe_serie_classe_id_foreign` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `classe_serie_serie_id_foreign` FOREIGN KEY (`serie_id`) REFERENCES `series` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `eleves`
--
ALTER TABLE `eleves`
  ADD CONSTRAINT `eleves_id_serie_foreign` FOREIGN KEY (`id_serie`) REFERENCES `series` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `enseignant_serie`
--
ALTER TABLE `enseignant_serie`
  ADD CONSTRAINT `enseignant_serie_enseignant_id_foreign` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `enseignant_serie_serie_id_foreign` FOREIGN KEY (`serie_id`) REFERENCES `series` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `matieres`
--
ALTER TABLE `matieres`
  ADD CONSTRAINT `matieres_serie_foreign` FOREIGN KEY (`serie`) REFERENCES `series` (`id`);

--
-- Contraintes pour la table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `notification_recipients`
--
ALTER TABLE `notification_recipients`
  ADD CONSTRAINT `notification_recipients_notification_id_foreign` FOREIGN KEY (`notification_id`) REFERENCES `notifications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notification_recipients_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `plans`
--
ALTER TABLE `plans`
  ADD CONSTRAINT `plans_subscription_type_id_foreign` FOREIGN KEY (`subscription_type_id`) REFERENCES `subscription_types` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `series`
--
ALTER TABLE `series`
  ADD CONSTRAINT `series_id_classe_foreign` FOREIGN KEY (`id_classe`) REFERENCES `classes` (`id`) ON DELETE SET NULL;

--
-- Contraintes pour la table `serie_matieres`
--
ALTER TABLE `serie_matieres`
  ADD CONSTRAINT `serie_matieres_matiere_id_foreign` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `serie_matieres_serie_id_foreign` FOREIGN KEY (`serie_id`) REFERENCES `series` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_etablissement_id_foreign` FOREIGN KEY (`etablissement_id`) REFERENCES `etablissements` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

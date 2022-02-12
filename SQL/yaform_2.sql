-- phpMyAdmin SQL Dump
-- version 5.1.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 12, 2022 at 01:13 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.0.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `yaform_2`
--

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submit_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

CREATE TABLE `forms` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `form_type` enum('0','1') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '0 is classic form and 1 is card form',
  `image_header` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `header` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `is_quiz` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 is not quiz 1 is quiz',
  `is_template` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 is not template 1 is template',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `logo` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `style_theme` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `font_family` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `accept_response` tinyint(1) NOT NULL DEFAULT 1 COMMENT '0 no 1 yes',
  `msg` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 is available, 1 => is deleted',
  `updated` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 is available, 1 => is updated',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `forms`
--

INSERT INTO `forms` (`id`, `user_id`, `form_type`, `image_header`, `header`, `is_quiz`, `is_template`, `description`, `logo`, `style_theme`, `font_family`, `accept_response`, `msg`, `deleted`, `updated`, `created_at`, `updated_at`) VALUES
(1, 1, '0', '', 'test template 1', 0, 0, 'test template 1', NULL, 'defalut', 'defalut-font', 1, 'success submit', 1, 0, '2022-01-09 11:58:04', '2022-01-09 09:58:49'),
(2, 1, '0', '', 'test template 1', 0, 0, 'test template 1', NULL, 'defalut', 'defalut-font', 1, 'success submit', 0, 1, '2022-01-09 11:58:24', '2022-01-09 10:04:18'),
(3, 1, '0', '', 'test template 1', 0, 0, 'test template 1', NULL, 'defalut', 'defalut-font', 1, 'success submit', 0, 0, '2022-01-09 12:04:18', '2022-01-09 12:04:18');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(98, '2014_10_12_000000_create_users_table', 1),
(99, '2014_10_12_100000_create_password_resets_table', 1),
(100, '2019_08_19_000000_create_failed_jobs_table', 1),
(101, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(102, '2022_01_05_175014_create_forms_table', 1),
(103, '2022_01_05_175959_create_social_media_links_table', 1),
(104, '2022_01_05_180752_create_questions_table', 1),
(105, '2022_01_05_182659_create_right_solutions_table', 1),
(106, '2022_01_05_182932_create_options_table', 1),
(107, '2022_01_05_183520_create_submits_table', 1),
(108, '2022_01_05_183532_create_answers_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `options`
--

CREATE TABLE `options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question_id` int(11) NOT NULL,
  `value` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `text` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `options`
--

INSERT INTO `options` (`id`, `question_id`, `value`, `text`, `created_at`, `updated_at`) VALUES
(1, 1, '10', 'option 1', '2022-01-09 09:58:04', '2022-01-09 09:58:04'),
(2, 1, '20', 'option 2', '2022-01-09 09:58:04', '2022-01-09 09:58:04'),
(3, 2, '10', 'option 1', '2022-01-09 09:58:24', '2022-01-09 09:58:24'),
(4, 2, '20', 'option 2', '2022-01-09 09:58:24', '2022-01-09 09:58:24'),
(5, 3, '10', 'option 1', '2022-01-09 10:04:18', '2022-01-09 10:04:18'),
(6, 3, '20', 'option 2', '2022-01-09 10:04:18', '2022-01-09 10:04:18');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` int(11) NOT NULL,
  `type` enum('0','1','2','3') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '0 => question, 1 => title, 2 => image, 3=> video',
  `description` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `question_type` enum('0','1','2','3','4','5','6','7','8','9','10') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '0 => Short answer, 1 => Paragraph, 2 => Multiple choice, 3=> Checkboxes, 4 => Dropdown, 5 => Date, 6 => Time, 7 => Phone number, 8 => Email, 9 => Name, 10 => Number',
  `required` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 not required, 1 is required',
  `focus` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 not focused, 1 is focused',
  `display_video` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 not display, 1 display',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `form_id`, `type`, `description`, `question_type`, `required`, `focus`, `display_video`, `created_at`, `updated_at`) VALUES
(1, 1, '0', 'Q1', '1', 1, 1, 1, '2022-01-09 11:58:04', '2022-01-09 11:58:04'),
(2, 2, '0', 'Q1', '1', 1, 1, 1, '2022-01-09 11:58:24', '2022-01-09 11:58:24'),
(3, 3, '0', 'Q1', '1', 1, 1, 1, '2022-01-09 12:04:18', '2022-01-09 12:04:18');

-- --------------------------------------------------------

--
-- Table structure for table `right_solutions`
--

CREATE TABLE `right_solutions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `question_id` int(11) NOT NULL,
  `solution` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `social_media_links`
--

CREATE TABLE `social_media_links` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` int(11) NOT NULL,
  `type` enum('0','1','2') COLLATE utf8mb4_unicode_ci NOT NULL COMMENT '0 => Facebook, 1 => Twitter, 2=> Instgram',
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `social_media_links`
--

INSERT INTO `social_media_links` (`id`, `form_id`, `type`, `url`, `created_at`, `updated_at`) VALUES
(1, 1, '0', 'facebook.com', '2022-01-09 09:58:04', '2022-01-09 09:58:04'),
(2, 1, '1', 'twitter.com', '2022-01-09 09:58:04', '2022-01-09 09:58:04'),
(3, 1, '2', 'instgram.com', '2022-01-09 09:58:04', '2022-01-09 09:58:04'),
(4, 2, '0', 'facebook.com', '2022-01-09 09:58:24', '2022-01-09 09:58:24'),
(5, 2, '1', 'twitter.com', '2022-01-09 09:58:24', '2022-01-09 09:58:24'),
(6, 2, '2', 'instgram.com', '2022-01-09 09:58:24', '2022-01-09 09:58:24'),
(7, 3, '0', 'facebook.com', '2022-01-09 10:04:18', '2022-01-09 10:04:18'),
(8, 3, '1', 'twitter.com', '2022-01-09 10:04:18', '2022-01-09 10:04:18'),
(9, 3, '2', 'instgram.com', '2022-01-09 10:04:18', '2022-01-09 10:04:18');

-- --------------------------------------------------------

--
-- Table structure for table `submits`
--

CREATE TABLE `submits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `form_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('individual','business','admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'individual',
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `num_of_employees` int(11) DEFAULT NULL,
  `img_src` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `url` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `country` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `business_category` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `year_dob` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `month_dob` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `day_dob` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `reset_password_code` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `type`, `name`, `phone`, `email`, `num_of_employees`, `img_src`, `url`, `country`, `business_category`, `year_dob`, `month_dob`, `day_dob`, `email_verified_at`, `password`, `reset_password_code`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'individual', 'eslam elbanna', '0123456789', 'solombana2000@gmail.com', 5, '', 'https://web.facebook.com/', 'tanta', 'programming', '2000', '01', '25', NULL, '$2y$10$.7lWXnoVpKqPHtULg0.kgehCMAFm1hELqV6jA00s2INeVmeo7yzlW', NULL, NULL, '2022-01-09 09:57:51', '2022-01-09 09:57:51');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `options`
--
ALTER TABLE `options`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `right_solutions`
--
ALTER TABLE `right_solutions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `social_media_links`
--
ALTER TABLE `social_media_links`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `submits`
--
ALTER TABLE `submits`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_phone_unique` (`phone`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `forms`
--
ALTER TABLE `forms`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=109;

--
-- AUTO_INCREMENT for table `options`
--
ALTER TABLE `options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `right_solutions`
--
ALTER TABLE `right_solutions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `social_media_links`
--
ALTER TABLE `social_media_links`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `submits`
--
ALTER TABLE `submits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2026 at 09:15 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sugar_nest`
--

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(80) NOT NULL,
  `unit_options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`unit_options`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`id`, `name`, `unit_options`, `created_at`) VALUES
(1, 'Cakes', '[\"cup\",\"gram\",\"kilogram\",\"lb\",\"piece\",\"tablespoon\",\"teaspoon\"]', '2026-10-09 05:55:35'),
(2, 'Cookies', '[\"cup\", \"tablespoon\", \"teaspoon\", \"gram\", \"kilogram\", \"piece\"]', '2026-10-09 05:55:35'),
(3, 'Breads', '[\"cup\", \"tablespoon\", \"teaspoon\", \"gram\", \"kilogram\", \"loaf\", \"piece\"]', '2026-10-09 05:55:35'),
(4, 'Pastries', '[\"cup\", \"tablespoon\", \"teaspoon\", \"gram\", \"kilogram\", \"piece\"]', '2026-10-09 05:55:35'),
(5, 'Desserts', '[\"cup\", \"tablespoon\", \"teaspoon\", \"gram\", \"kilogram\", \"piece\"]', '2026-10-09 05:55:35'),
(6, 'Beverages', '[\"can\",\"cup\",\"glass\",\"liter\",\"milliliter\",\"tablespoon\",\"teaspoon\"]', '2026-10-09 05:55:35'),
(7, 'Filipino Favorites', '[\"cup\", \"tablespoon\", \"teaspoon\", \"gram\", \"kilogram\", \"piece\", \"pack\"]', '2026-10-09 05:55:35');

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `recipe_id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `comment_text` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `comments`
--

INSERT INTO `comments` (`id`, `recipe_id`, `user_id`, `comment_text`, `created_at`, `updated_at`) VALUES
(2, 1, 1, 'Good!', '2026-10-09 06:51:31', '2026-10-09 06:51:31');

-- --------------------------------------------------------

--
-- Table structure for table `favorites`
--

CREATE TABLE `favorites` (
  `user_id` int(10) UNSIGNED NOT NULL,
  `recipe_id` int(10) UNSIGNED NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `favorites`
--

INSERT INTO `favorites` (`user_id`, `recipe_id`, `created_at`) VALUES
(1, 1, '2026-10-09 06:47:30');

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `id` int(10) UNSIGNED NOT NULL,
  `recipe_id` int(10) UNSIGNED NOT NULL,
  `quantity` varchar(50) NOT NULL,
  `unit` varchar(50) NOT NULL,
  `ingredient_name` varchar(150) NOT NULL,
  `ingredient_order` int(10) UNSIGNED NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`id`, `recipe_id`, `quantity`, `unit`, `ingredient_name`, `ingredient_order`) VALUES
(12, 1, '1', 'lb', 'Strawberries', 1),
(13, 1, '2', 'tablespoon', 'granulated sugar', 2),
(14, 1, '2', 'cup', 'all-purpose flour', 3),
(15, 1, '1/3', 'cup', 'granulated sugar (plus 1 tablespoon for sprinkling)', 4),
(16, 1, '1', 'tablespoon', 'baking powder', 5),
(17, 1, '1/2', 'teaspoon', 'salt', 6),
(18, 1, '6', 'tablespoon', 'cold unsalted butter, cubed', 7),
(19, 1, '3/4', 'cup', 'cold milk (plus 1 tablespoon extra for brushing; whole milk works best)', 8),
(20, 1, '1', 'cup', 'heavy whipping cream, cold', 9),
(21, 1, '2', 'tablespoon', 'powdered sugar or granulated sugar', 10),
(22, 1, '1/2', 'teaspoon', 'vanilla extract', 11);

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `category_id` int(10) UNSIGNED NOT NULL,
  `title` varchar(150) NOT NULL,
  `short_description` varchar(255) NOT NULL,
  `instructions` text NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `user_id`, `category_id`, `title`, `short_description`, `instructions`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Strawberry Shortcake', 'Here is a quick, classic recipe for homemade strawberry shortcake featuring tender, flaky shortcake biscuits, sweet juicy berries, and fresh whipped cream.', 'STRAWBERRY SHORTCAKE RECIPE\r\n\r\nPrep time: 20 mins | Bake time: 15 mins | Servings: 4–6\r\n\r\nCOOKING INSTRUCTIONS:\r\n\r\n1. Macerate the Strawberries:\r\nToss the sliced strawberries and 2 tablespoons of sugar together in a bowl. Set them aside at room temperature for 20 to 30 minutes so the sugar draws out their natural juices.\r\n\r\n2. Mix the Biscuit Dough:\r\nPreheat your oven to 400°F (200°C) and line a baking sheet with parchment paper. In a large bowl, whisk together the flour, 1/3 cup sugar, baking powder, and salt. Cut in the cold butter using a pastry cutter or your fingers until the mixture resembles coarse crumbs. Pour in the cold milk and stir gently just until a shaggy dough forms.\r\n\r\n3. Shape and Bake:\r\nTurn the dough out onto a lightly floured surface, gently press it into a 3/4-inch thick rectangle, and cut out rounds using a biscuit cutter (or cut into squares with a knife). Place them on the baking sheet, brush the tops with a little milk, and sprinkle with a bit of sugar. Bake for 12 to 15 minutes until golden brown, then let cool.\r\n\r\n4. Whip the Cream:\r\nWhile the biscuits bake, beat the cold heavy cream, sugar, and vanilla extract in a chilled bowl with a hand mixer until medium-soft peaks form.\r\n\r\n5. Assemble:\r\nSplit the warm or cooled biscuits in half horizontally. Spoon the juicy strawberries and their syrup onto the bottom half, add a generous dollop of whipped cream, and place the top biscuit on. Finish with a bit more cream and berries on top.', '2026-10-09 06:47:17', '2026-10-09 06:47:17');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `password_hash`, `created_at`) VALUES
(1, 'Alessandra', 'ali@test.com', '$2y$10$Ih4mBmS680o0wsjxsrUvE.IXhxNlM3f3wTsPgU4dSyXOMe0p6e6ki', '2026-10-09 06:09:54');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_comments_recipe_id` (`recipe_id`),
  ADD KEY `idx_comments_user_id` (`user_id`);

--
-- Indexes for table `favorites`
--
ALTER TABLE `favorites`
  ADD PRIMARY KEY (`user_id`,`recipe_id`),
  ADD KEY `idx_favorites_recipe_id` (`recipe_id`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_ingredients_recipe_id` (`recipe_id`);

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_recipes_user_id` (`user_id`),
  ADD KEY `idx_recipes_category_id` (`category_id`),
  ADD KEY `idx_recipes_created_at` (`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `fk_comments_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comments_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `favorites`
--
ALTER TABLE `favorites`
  ADD CONSTRAINT `fk_favorites_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_favorites_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD CONSTRAINT `fk_ingredients_recipe` FOREIGN KEY (`recipe_id`) REFERENCES `recipes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `fk_recipes_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_recipes_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

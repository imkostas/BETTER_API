-- phpMyAdmin SQL Dump
-- version 4.2.12deb2
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 03, 2015 at 08:09 PM
-- Server version: 5.5.43-0+deb8u1
-- PHP Version: 5.6.7-1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `Better`
--

-- --------------------------------------------------------

--
-- Table structure for table `country`
--

CREATE TABLE IF NOT EXISTS `country` (
`id` tinyint(1) unsigned NOT NULL,
  `name` varchar(64) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `country`
--

INSERT INTO `country` (`id`, `name`) VALUES
(1, 'UNITED STATES OF AMERICA');

-- --------------------------------------------------------

--
-- Table structure for table `favorite_hashtag`
--

CREATE TABLE IF NOT EXISTS `favorite_hashtag` (
`id` bigint(20) unsigned NOT NULL,
  `hashtag_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `favorite_post`
--

CREATE TABLE IF NOT EXISTS `favorite_post` (
`id` bigint(20) unsigned NOT NULL,
  `post_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `follow`
--

CREATE TABLE IF NOT EXISTS `follow` (
`id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `follower_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `is_deleted` tinyint(1) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hashtag`
--

CREATE TABLE IF NOT EXISTS `hashtag` (
`id` bigint(20) unsigned NOT NULL,
  `name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `misuse`
--

CREATE TABLE IF NOT EXISTS `misuse` (
  `post_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE IF NOT EXISTS `notification` (
  `user_id` bigint(20) unsigned NOT NULL,
  `voted_post` tinyint(1) unsigned NOT NULL,
  `favorited_post` tinyint(1) unsigned NOT NULL,
  `new_follower` tinyint(1) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `point`
--

CREATE TABLE IF NOT EXISTS `point` (
`id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `amount` smallint(5) unsigned NOT NULL,
  `type` tinyint(1) unsigned NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post`
--

CREATE TABLE IF NOT EXISTS `post` (
`id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `layout` tinyint(1) unsigned NOT NULL,
  `hotspot_one_x` smallint(5) unsigned NOT NULL,
  `hotspot_one_y` smallint(5) unsigned NOT NULL,
  `hotspot_two_x` smallint(5) unsigned NOT NULL,
  `hotspot_two_y` smallint(5) unsigned NOT NULL,
  `created_at` datetime NOT NULL,
  `is_blocked` tinyint(1) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `post_hashtag_relation`
--

CREATE TABLE IF NOT EXISTS `post_hashtag_relation` (
  `post_id` bigint(20) unsigned NOT NULL,
  `hashtag_id` bigint(20) unsigned NOT NULL,
  `position` tinyint(1) unsigned NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rank`
--

CREATE TABLE IF NOT EXISTS `rank` (
  `user_id` bigint(20) unsigned NOT NULL,
  `rank` tinyint(1) unsigned NOT NULL,
  `total_points` bigint(20) unsigned NOT NULL,
  `weekly_points` bigint(20) unsigned NOT NULL,
  `daily_points` bigint(20) unsigned NOT NULL,
  `badge_tastemaker` tinyint(1) unsigned NOT NULL,
  `badge_adventurer` tinyint(1) unsigned NOT NULL,
  `badge_admirer` tinyint(1) unsigned NOT NULL,
  `badge_role_model` tinyint(1) unsigned NOT NULL,
  `badge_celebrity` tinyint(1) unsigned NOT NULL,
  `badge_idol` tinyint(1) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
`id` bigint(20) unsigned NOT NULL,
  `username` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `email` varchar(64) COLLATE utf8_unicode_ci NOT NULL,
  `password` char(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `password_temp` char(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `facebook_id` bigint(20) unsigned DEFAULT NULL,
  `device` varchar(4096) COLLATE utf8_unicode_ci DEFAULT NULL,
  `device_type` tinyint(1) unsigned DEFAULT NULL,
  `gender` tinyint(1) unsigned NOT NULL,
  `birthday` date NOT NULL,
  `country_id` tinyint(1) unsigned NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `last_login_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vote`
--

CREATE TABLE IF NOT EXISTS `vote` (
`id` bigint(20) unsigned NOT NULL,
  `post_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `vote` tinyint(1) unsigned NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `country`
--
ALTER TABLE `country`
 ADD PRIMARY KEY (`id`) USING BTREE, ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `favorite_hashtag`
--
ALTER TABLE `favorite_hashtag`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `hashtag_id` (`hashtag_id`,`user_id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `favorite_post`
--
ALTER TABLE `favorite_post`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `post_id` (`post_id`,`user_id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `follow`
--
ALTER TABLE `follow`
 ADD PRIMARY KEY (`id`), ADD UNIQUE KEY `user_id` (`user_id`,`follower_id`), ADD KEY `follower_id` (`follower_id`);

--
-- Indexes for table `hashtag`
--
ALTER TABLE `hashtag`
 ADD PRIMARY KEY (`id`) USING BTREE, ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `misuse`
--
ALTER TABLE `misuse`
 ADD PRIMARY KEY (`post_id`,`user_id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
 ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `point`
--
ALTER TABLE `point`
 ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post`
--
ALTER TABLE `post`
 ADD PRIMARY KEY (`id`), ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `post_hashtag_relation`
--
ALTER TABLE `post_hashtag_relation`
 ADD PRIMARY KEY (`post_id`,`hashtag_id`), ADD KEY `hashtag_id` (`hashtag_id`);

--
-- Indexes for table `rank`
--
ALTER TABLE `rank`
 ADD PRIMARY KEY (`user_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
 ADD PRIMARY KEY (`id`), ADD KEY `country_id` (`country_id`);

--
-- Indexes for table `vote`
--
ALTER TABLE `vote`
 ADD PRIMARY KEY (`id`) USING BTREE, ADD UNIQUE KEY `post_id` (`post_id`,`user_id`), ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `country`
--
ALTER TABLE `country`
MODIFY `id` tinyint(1) unsigned NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2;
--
-- AUTO_INCREMENT for table `favorite_hashtag`
--
ALTER TABLE `favorite_hashtag`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `favorite_post`
--
ALTER TABLE `favorite_post`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `follow`
--
ALTER TABLE `follow`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `hashtag`
--
ALTER TABLE `hashtag`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `point`
--
ALTER TABLE `point`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `post`
--
ALTER TABLE `post`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- AUTO_INCREMENT for table `vote`
--
ALTER TABLE `vote`
MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- Constraints for dumped tables
--

--
-- Constraints for table `favorite_hashtag`
--
ALTER TABLE `favorite_hashtag`
ADD CONSTRAINT `favorite_hashtag_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `favorite_hashtag_hashtag_id` FOREIGN KEY (`hashtag_id`) REFERENCES `hashtag` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `favorite_post`
--
ALTER TABLE `favorite_post`
ADD CONSTRAINT `favorite_post_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `favorite_post_post_id` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `follow`
--
ALTER TABLE `follow`
ADD CONSTRAINT `follow_follower_id` FOREIGN KEY (`follower_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `follow_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `misuse`
--
ALTER TABLE `misuse`
ADD CONSTRAINT `misuse_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `misuse_post_id` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
ADD CONSTRAINT `notification_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `point`
--
ALTER TABLE `point`
ADD CONSTRAINT `point_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `post`
--
ALTER TABLE `post`
ADD CONSTRAINT `post_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `post_hashtag_relation`
--
ALTER TABLE `post_hashtag_relation`
ADD CONSTRAINT `post_hashtag_relation_hashtag_id` FOREIGN KEY (`hashtag_id`) REFERENCES `hashtag` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `post_hashtag_relation_post_id` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `rank`
--
ALTER TABLE `rank`
ADD CONSTRAINT `rank_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
ADD CONSTRAINT `user_country_id` FOREIGN KEY (`country_id`) REFERENCES `country` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `vote`
--
ALTER TABLE `vote`
ADD CONSTRAINT `vote_user_id` FOREIGN KEY (`user_id`) REFERENCES `user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
ADD CONSTRAINT `vote_post_id` FOREIGN KEY (`post_id`) REFERENCES `post` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

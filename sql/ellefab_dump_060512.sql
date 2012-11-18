-- phpMyAdmin SQL Dump
-- version 4.0.0-dev
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 05, 2012 at 11:09 AM
-- Server version: 5.1.54
-- PHP Version: 5.3.9-ZS5.6.0

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `ellefab0.2`
--

-- --------------------------------------------------------

--
-- Table structure for table `articlecomment`
--

CREATE TABLE IF NOT EXISTS `articlecomment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user` int(11) unsigned NOT NULL,
  `article` int(11) unsigned NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `articleid` (`article`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Featured Article Comments' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `bookmarkedresource`
--

CREATE TABLE IF NOT EXISTS `bookmarkedresource` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned NOT NULL,
  `resource` int(10) unsigned NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `resource_idx` (`resource`),
  KEY `user_idx` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `featuredarticles`
--

CREATE TABLE IF NOT EXISTS `featuredarticles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author` int(11) unsigned NOT NULL,
  `avatar_image` varchar(200) NOT NULL,
  `title_image` varchar(200) NOT NULL,
  `topic` int(11) unsigned NOT NULL,
  `title` varchar(500) NOT NULL,
  `date` date NOT NULL,
  `available` tinyint(4) NOT NULL,
  `content` longtext NOT NULL,
  `description` text NOT NULL,
  `uri` varchar(100) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uri` (`uri`),
  UNIQUE KEY `uri_2` (`uri`),
  KEY `topic` (`topic`),
  KEY `author` (`author`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Featured Articles Extension Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `featured_author`
--

CREATE TABLE IF NOT EXISTS `featured_author` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `followedarticle`
--

CREATE TABLE IF NOT EXISTS `followedarticle` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(11) unsigned NOT NULL,
  `article` int(11) unsigned NOT NULL,
  `date` date NOT NULL,
  PRIMARY KEY (`id`),
  KEY `article` (`article`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `followedstory`
--

CREATE TABLE IF NOT EXISTS `followedstory` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned NOT NULL,
  `story` int(10) unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `story_idx` (`story`),
  KEY `user_idx` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `followedtopic`
--

CREATE TABLE IF NOT EXISTS `followedtopic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(11) unsigned NOT NULL,
  `topic` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `topic` (`topic`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `friendrequest`
--

CREATE TABLE IF NOT EXISTS `friendrequest` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `requestor` int(11) unsigned NOT NULL,
  `requestee` int(11) unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `new` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `requestor` (`requestor`),
  KEY `requestee` (`requestee`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- Dumping data for table `friendrequest`
--

INSERT INTO `friendrequest` (`id`, `requestor`, `requestee`, `date`, `new`) VALUES
(3, 4, 7, '2012-05-14 00:47:22', 1);

-- --------------------------------------------------------

--
-- Table structure for table `friendship`
--

CREATE TABLE IF NOT EXISTS `friendship` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user` int(10) unsigned NOT NULL,
  `friend` int(10) unsigned NOT NULL,
  `status` enum('a','b') NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user` (`user`),
  KEY `friend` (`friend`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `friendship`
--

INSERT INTO `friendship` (`id`, `user`, `friend`, `status`, `created`) VALUES
(2, 7, 4, 'a', '2012-05-13 22:02:03'),
(3, 4, 7, 'a', '2012-05-13 22:02:03'),
(4, 6, 4, 'a', '2012-05-14 01:53:37'),
(5, 4, 6, 'a', '2012-05-14 01:53:37');

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE IF NOT EXISTS `messages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sender` int(11) unsigned NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `type` enum('n','f','r') NOT NULL,
  `content` mediumtext NOT NULL,
  `reference` int(11) DEFAULT NULL,
  `subject` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `sender` (`sender`),
  KEY `reference` (`reference`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Message Storage Table' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `model__profile_index`
--

CREATE TABLE IF NOT EXISTS `model__profile_index` (
  `keyword` varchar(200) NOT NULL DEFAULT '',
  `field` varchar(50) NOT NULL DEFAULT '',
  `position` bigint(20) NOT NULL DEFAULT '0',
  `id` int(10) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`keyword`,`field`,`position`,`id`),
  KEY `model__profile_index_id_profile_id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `model__profile_index`
--

INSERT INTO `model__profile_index` (`keyword`, `field`, `position`, `id`) VALUES
('jeffrey', 'fName', 0, 5),
('jsimmons', 'displayName', 0, 5),
('simmons', 'lName', 0, 5);

-- --------------------------------------------------------

--
-- Table structure for table `profile`
--

CREATE TABLE IF NOT EXISTS `profile` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `fName` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `lName` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `displayName` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `gender` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `interests` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `dob` date NOT NULL,
  `email` text CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `about` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `user` int(11) unsigned NOT NULL,
  `photo` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'Profile Photo File Name',
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

--
-- Dumping data for table `profile`
--

INSERT INTO `profile` (`id`, `fName`, `lName`, `displayName`, `gender`, `interests`, `dob`, `email`, `about`, `user`, `photo`) VALUES
(2, 'Jarrod', 'Placide-Raymond', 'jpraymond375', 'male', 'These are interests', '2010-02-10', 'jpraymond375@hotmail.com', 'This is about me', 4, ''),
(3, 'Edited Test', 'Account-Name', 'test', 'female', 'Test Interests', '1987-10-22', 'test@email.com', 'About Test', 6, '133660763931.png'),
(4, 'Test Second', 'Account', 'test', 'male', 'Test Interests', '1986-06-12', 'test2@email.com', 'About Test', 7, NULL),
(5, 'Jeffrey', 'Simmons', 'jsimmons', 'male', 'Test Interests 3', '1983-03-12', 'test3@email.com', 'About Test 3', 8, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `recipient`
--

CREATE TABLE IF NOT EXISTS `recipient` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `message` int(10) unsigned NOT NULL,
  `user` int(10) unsigned NOT NULL,
  `read` tinyint(4) NOT NULL DEFAULT '0',
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `message` (`message`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Message-Recipient Associations' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `resource`
--

CREATE TABLE IF NOT EXISTS `resource` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `notes` text NOT NULL,
  `type` int(11) unsigned NOT NULL,
  `location` int(11) unsigned DEFAULT NULL,
  `contact` text,
  `address` text,
  `national` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `type` (`type`),
  KEY `type_2` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Resource Collection' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `resourcetopic`
--

CREATE TABLE IF NOT EXISTS `resourcetopic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `topic` int(10) unsigned NOT NULL,
  `resource` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `topic` (`topic`),
  KEY `resource` (`resource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Topic-Resource Associations' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `resource_types`
--

CREATE TABLE IF NOT EXISTS `resource_types` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(100) CHARACTER SET latin1 NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `session`
--

CREATE TABLE IF NOT EXISTS `session` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `data` text NOT NULL,
  `accessed` bigint(20) unsigned NOT NULL,
  `userid` int(11) unsigned NOT NULL,
  `hostname` varchar(39) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userid` (`userid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `topic`
--

CREATE TABLE IF NOT EXISTS `topic` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` varchar(5000) NOT NULL,
  `active` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `active` (`active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(200) NOT NULL,
  `password` varbinary(100) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `enabled` tinyint(4) NOT NULL DEFAULT '1',
  `lastLogin` timestamp NULL DEFAULT NULL,
  `accessFailures` int(11) unsigned NOT NULL DEFAULT '0',
  `usergroup` int(11) unsigned NOT NULL,
  `verificationcode` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `verified` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Is Account Verified?',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `userType` (`usergroup`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='System User Table' AUTO_INCREMENT=9 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `password`, `created`, `modified`, `enabled`, `lastLogin`, `accessFailures`, `usergroup`, `verificationcode`, `verified`) VALUES
(4, 'jpraymond375@email.com', 'd8e44d1027a71884c97ea62fdb896f95bb92224946e5399c085bdac764b74dd1??.0%&-', '2012-05-01 02:58:58', '0000-00-00 00:00:00', 1, '2012-05-01 17:18:28', 2, 1, '', 1),
(6, 'test@email.com', 'da347420998cf32327a54e14af0e7599b3ed4f68d450b10fb8097b69100d43b1@:4).', '2012-05-01 20:10:42', '2012-05-01 20:10:42', 1, NULL, 0, 1, 'g97g4bsd+dqe/', 0),
(7, 'test2@email.com', '5119d0baa64dd26ccd35c499861f70197aabe1b25ce9510be3de2018e43f9a31\n.', '2012-05-11 07:20:04', '2012-05-11 07:20:04', 1, NULL, 0, 1, 'd6d6dg96g4bsd+dqe/', 0),
(8, 'test3@email.com', '31d3f7e60cb476ff64a0354ea1b7c1fc76d3aa6d6f6e03266a89ac8b0112fdab=$', '2012-05-20 08:20:30', '2012-05-20 08:20:30', 1, NULL, 0, 1, 'igsbdjsjfdvsfs453we/.3', 0);

-- --------------------------------------------------------

--
-- Table structure for table `usergroup`
--

CREATE TABLE IF NOT EXISTS `usergroup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

--
-- Dumping data for table `usergroup`
--

INSERT INTO `usergroup` (`id`, `name`) VALUES
(1, 'Member');

-- --------------------------------------------------------

--
-- Table structure for table `userstory`
--

CREATE TABLE IF NOT EXISTS `userstory` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text NOT NULL,
  `user` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user` (`user`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `userstory`
--

INSERT INTO `userstory` (`id`, `date`, `content`, `user`) VALUES
(1, '2012-06-05 07:13:39', 'This is the test user story', 4),
(2, '2012-06-05 07:23:24', 'This is the test user story', 4),
(3, '2012-06-05 07:23:28', 'This is the test user story', 4),
(4, '2012-06-05 07:23:29', 'This is the test user story', 4),
(5, '2012-06-05 07:23:31', 'This is the test user story', 4),
(6, '2012-06-05 07:23:33', 'This is the test user story', 4),
(7, '2012-06-05 07:23:36', 'This is the test user story', 4);

-- --------------------------------------------------------

--
-- Table structure for table `userstorycomment`
--

CREATE TABLE IF NOT EXISTS `userstorycomment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `content` text NOT NULL,
  `story` int(11) unsigned NOT NULL,
  `user` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `story` (`story`),
  KEY `user` (`user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='User Story Comments' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `userstorygallery`
--

CREATE TABLE IF NOT EXISTS `userstorygallery` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `photos` text NOT NULL,
  `total` int(10) unsigned NOT NULL DEFAULT '0',
  `userstory` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userstory_idx` (`userstory`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `userstorymedia`
--

CREATE TABLE IF NOT EXISTS `userstorymedia` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `photos` text,
  `totalphotos` int(10) unsigned NOT NULL DEFAULT '0',
  `videos` text,
  `totalvideos` int(10) unsigned NOT NULL DEFAULT '0',
  `userstory` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userstory_idx` (`userstory`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Dumping data for table `userstorymedia`
--

INSERT INTO `userstorymedia` (`id`, `photos`, `totalphotos`, `videos`, `totalvideos`, `userstory`) VALUES
(1, 'a:3:{i:0;s:11:"3837838.jpg";i:1;s:10:"hd8113.jpg";i:2;s:10:"2738y2.jpg";}', 3, NULL, 0, 1),
(2, 'a:3:{i:0;s:11:"3837838.jpg";i:1;s:10:"hd8113.jpg";i:2;s:10:"2738y2.jpg";}', 3, NULL, 0, 2),
(3, 'a:3:{i:0;s:11:"3837838.jpg";i:1;s:10:"hd8113.jpg";i:2;s:10:"2738y2.jpg";}', 3, NULL, 0, 3),
(4, 'a:3:{i:0;s:11:"3837838.jpg";i:1;s:10:"hd8113.jpg";i:2;s:10:"2738y2.jpg";}', 3, NULL, 0, 4),
(5, 'a:3:{i:0;s:11:"3837838.jpg";i:1;s:10:"hd8113.jpg";i:2;s:10:"2738y2.jpg";}', 3, NULL, 0, 5),
(6, 'a:3:{i:0;s:11:"3837838.jpg";i:1;s:10:"hd8113.jpg";i:2;s:10:"2738y2.jpg";}', 3, NULL, 0, 6),
(7, 'a:3:{i:0;s:11:"3837838.jpg";i:1;s:10:"hd8113.jpg";i:2;s:10:"2738y2.jpg";}', 3, NULL, 0, 7);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `articlecomment`
--
ALTER TABLE `articlecomment`
  ADD CONSTRAINT `articlecomment_ibfk_3` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `articlecomment_ibfk_4` FOREIGN KEY (`article`) REFERENCES `featuredarticles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `bookmarkedresource`
--
ALTER TABLE `bookmarkedresource`
  ADD CONSTRAINT `bookmarkedresource_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `bookmarkedresource_ibfk_2` FOREIGN KEY (`resource`) REFERENCES `resource` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `featuredarticles`
--
ALTER TABLE `featuredarticles`
  ADD CONSTRAINT `featuredarticles_ibfk_1` FOREIGN KEY (`author`) REFERENCES `featured_author` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  ADD CONSTRAINT `featuredarticles_ibfk_2` FOREIGN KEY (`topic`) REFERENCES `topic` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `followedarticle`
--
ALTER TABLE `followedarticle`
  ADD CONSTRAINT `followedarticle_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `followedarticle_ibfk_3` FOREIGN KEY (`article`) REFERENCES `featuredarticles` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `followedstory`
--
ALTER TABLE `followedstory`
  ADD CONSTRAINT `followedstory_story_userstory_id` FOREIGN KEY (`story`) REFERENCES `userstory` (`id`),
  ADD CONSTRAINT `followedstory_user_user_id` FOREIGN KEY (`user`) REFERENCES `user` (`id`);

--
-- Constraints for table `followedtopic`
--
ALTER TABLE `followedtopic`
  ADD CONSTRAINT `followedtopic_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `followedtopic_ibfk_2` FOREIGN KEY (`topic`) REFERENCES `topic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `friendrequest`
--
ALTER TABLE `friendrequest`
  ADD CONSTRAINT `friendrequest_ibfk_1` FOREIGN KEY (`requestor`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `friendrequest_ibfk_2` FOREIGN KEY (`requestee`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `friendship`
--
ALTER TABLE `friendship`
  ADD CONSTRAINT `friendship_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `friendship_ibfk_2` FOREIGN KEY (`friend`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_ibfk_1` FOREIGN KEY (`sender`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `model__profile_index`
--
ALTER TABLE `model__profile_index`
  ADD CONSTRAINT `model__profile_index_id_profile_id` FOREIGN KEY (`id`) REFERENCES `profile` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `profile`
--
ALTER TABLE `profile`
  ADD CONSTRAINT `profile_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `recipient`
--
ALTER TABLE `recipient`
  ADD CONSTRAINT `recipient_ibfk_1` FOREIGN KEY (`message`) REFERENCES `messages` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `recipient_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `resource`
--
ALTER TABLE `resource`
  ADD CONSTRAINT `resource_ibfk_1` FOREIGN KEY (`type`) REFERENCES `resource_types` (`id`) ON DELETE NO ACTION ON UPDATE CASCADE;

--
-- Constraints for table `resourcetopic`
--
ALTER TABLE `resourcetopic`
  ADD CONSTRAINT `resourcetopic_ibfk_1` FOREIGN KEY (`topic`) REFERENCES `topic` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `resourcetopic_ibfk_2` FOREIGN KEY (`resource`) REFERENCES `resource` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `user`
--
ALTER TABLE `user`
  ADD CONSTRAINT `user_ibfk_1` FOREIGN KEY (`usergroup`) REFERENCES `usergroup` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `userstory`
--
ALTER TABLE `userstory`
  ADD CONSTRAINT `userstory_ibfk_1` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `userstorycomment`
--
ALTER TABLE `userstorycomment`
  ADD CONSTRAINT `userstorycomment_ibfk_1` FOREIGN KEY (`story`) REFERENCES `userstory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `userstorycomment_ibfk_2` FOREIGN KEY (`user`) REFERENCES `user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `userstorygallery`
--
ALTER TABLE `userstorygallery`
  ADD CONSTRAINT `userstorygallery_ibfk_1` FOREIGN KEY (`userstory`) REFERENCES `userstory` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `userstorymedia`
--
ALTER TABLE `userstorymedia`
  ADD CONSTRAINT `userstorymedia_ibfk_1` FOREIGN KEY (`userstory`) REFERENCES `userstory` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

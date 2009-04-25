-- phpMyAdmin SQL Dump
-- version 2.11.9.3
-- http://www.phpmyadmin.net
--
-- Host: mysql.lncln.com
-- Generation Time: Apr 15, 2009 at 07:53 PM
-- Server version: 5.0.67
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `betalncln`
--

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE IF NOT EXISTS `albums` (
  `id` int(8) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(8) NOT NULL auto_increment,
  `name` varchar(30) NOT NULL,
  `index` int(1) NOT NULL,
  `numIndex` int(3) NOT NULL,
  `report` int(1) NOT NULL,
  `reportValue` int(2) NOT NULL default '0',
  `rate` int(1) NOT NULL,
  `rateValue` int(2) NOT NULL default '0',
  `obscene` int(1) NOT NULL,
  `refresh` int(1) NOT NULL,
  `delete` int(1) NOT NULL,
  `caption` int(1) NOT NULL,
  `tag` int(1) NOT NULL,
  `album` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `index`, `numIndex`, `report`, `reportValue`, `rate`, `rateValue`, `obscene`, `refresh`, `delete`, `caption`, `tag`, `album`) VALUES
(1, 'Anonymous', 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2, 'New User', 1, 5, 1, 3, 1, 1, 0, 0, 0, 0, 1, 0),
(3, 'Trusted User', 1, 20, 1, 5, 1, 3, 1, 0, 0, 1, 1, 1),
(4, 'Admin', 1, 0, 1, 5, 1, 5, 1, 1, 1, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `images`
--

CREATE TABLE IF NOT EXISTS `images` (
  `id` int(6) unsigned zerofill NOT NULL auto_increment,
  `name` char(40) NOT NULL,
  `caption` text NOT NULL,
  `postTime` int(12) NOT NULL default '0',
  `type` enum('jpg','png','gif') NOT NULL,
  `album` int(8) NOT NULL default '0',
  `queue` tinyint(1) NOT NULL default '1',
  `rating` int(4) NOT NULL default '0',
  `numComments` int(4) NOT NULL default '0',
  `obscene` tinyint(1) NOT NULL default '0',
  `report` int(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `index`, `numIndex`, `report`, `rate`, `obscene`, `refresh`, `delete`, `caption`, `tag`, `album`) VALUES
(1, 'Anonymous', 0, 0, 1, 0, 0, 0, 0, 0, 0, 0),
(2, 'New User', 1, 5, 3, 1, 0, 0, 0, 0, 1, 0),
(3, 'Trusted User', 1, 20, 5, 3, 1, 0, 0, 1, 1, 1),
(4, 'Admin', 1, 0, 5, 5, 1, 1, 1, 1, 1, 1);


-- --------------------------------------------------------

--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(8) NOT NULL auto_increment,
  `postTime` int(32) NOT NULL,
  `title` varchar(50) NOT NULL,
  `news` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `rating`
--

CREATE TABLE IF NOT EXISTS `rating` (
  `id` int(8) NOT NULL auto_increment,
  `picId` int(6) unsigned zerofill NOT NULL,
  `userId` int(8) NOT NULL,
  `upDown` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`) VALUES
(1, 'title', 'The Archive'),
(2, 'version', '0.11.0'),
(3, 'theme', 'bbl'),
(4, 'perpage', '12'),
(5, 'tbp', '10'),
(6, 'defaultGroup', '2'),
(7, 'register', '1');
 


-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(12) NOT NULL auto_increment,
  `picId` int(6) NOT NULL,
  `tag` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(8) NOT NULL auto_increment,
  `name` char(32) NOT NULL,
  `password` char(40) NOT NULL,
  `admin` tinyint(1) NOT NULL default '0',
  `toHome` int(1) NOT NULL default '1',
  `obscene` tinyint(1) NOT NULL default '1',
  `numImages` int(2) NOT NULL,
  `postTime` int(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`name`, `password`, `admin`, `toHome`, `obscene`, `numImages`, `postTime`) VALUES
('Anonymous', '', 0, 0, 0, 0, 0),
('admin', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 1, 1, 1, 0, 0);
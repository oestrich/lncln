-- phpMyAdmin SQL Dump
-- version 3.1.2deb1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Jun 25, 2009 at 02:41 PM
-- Server version: 5.0.75
-- PHP Version: 5.2.6-3ubuntu4.1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `lncln`
--

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE IF NOT EXISTS `albums` (
  `id` int(8) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `groups`
--

CREATE TABLE IF NOT EXISTS `groups` (
  `id` int(8) NOT NULL auto_increment,
  `name` varchar(30) NOT NULL,
  `upload` int(1) NOT NULL,
  `index` int(1) NOT NULL,
  `numIndex` int(3) NOT NULL,
  `report` int(1) NOT NULL,
  `reportValue` int(2) NOT NULL default '0',
  `ratings` int(1) NOT NULL,
  `ratingsValue` int(2) NOT NULL default '0',
  `obscene` int(1) NOT NULL,
  `refresh` int(1) NOT NULL,
  `delete` int(1) NOT NULL,
  `captions` int(1) NOT NULL,
  `tags` int(1) NOT NULL,
  `albums` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Dumping data for table `groups`
--

INSERT INTO `groups` (`id`, `name`, `upload`, `index`, `numIndex`, `report`, `reportValue`, `ratings`, `ratingsValue`, `obscene`, `refresh`, `delete`, `captions`, `tags`, `albums`) VALUES
(1, 'Anonymous', 1, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0),
(2, 'New User', 1, 1, 5, 1, 3, 1, 1, 0, 0, 0, 0, 1, 0),
(3, 'Trusted User', 1, 1, 20, 1, 5, 1, 3, 1, 0, 0, 1, 1, 1),
(4, 'Admin', 1, 1, 0, 1, 5, 1, 5, 1, 1, 1, 1, 1, 1);

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
  `obscene` tinyint(1) NOT NULL default '0',
  `report` int(2) NOT NULL default '0',
  `view` int(12) NOT NULL default '0',
  `tags` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `ratings`
--

CREATE TABLE IF NOT EXISTS `ratings` (
  `id` int(8) NOT NULL auto_increment,
  `picId` int(6) unsigned zerofill NOT NULL,
  `userId` int(8) NOT NULL,
  `rating` int(1) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE IF NOT EXISTS `settings` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `name`, `value`) VALUES
(1, 'title', 's:11:"The Archive";'),
(2, 'version', 's:6:"0.13.0";'),
(3, 'theme', 's:3:"bbl";'),
(4, 'perpage', 'i:3;'),
(5, 'tbp', 'i:10;'),
(6, 'default_group', 'i:2;'),
(7, 'register', 'i:1;'),
(8, 'default_rss_keyword', 's:4:"safe";');


-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE IF NOT EXISTS `tags` (
  `id` int(12) NOT NULL auto_increment,
  `picId` int(6) NOT NULL,
  `tag` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(8) NOT NULL auto_increment,
  `name` char(32) NOT NULL,
  `password` char(40) NOT NULL,
  `admin` tinyint(1) NOT NULL default '0',
  `group` int(3) NOT NULL,
  `obscene` tinyint(1) NOT NULL default '1',
  `numImages` int(2) NOT NULL default '0',
  `postTime` int(32) NOT NULL default '0',
  `uploadCount` int(8) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`name`, `password`, `admin`, `group`, `obscene`, `numImages`, `postTime`, `uploadCount`) VALUES
('Anonymous', '', 0, 1, 0, 1, 1245939661, 8),
('admin', 'fa0af50b8e6656579f92f36f997e60d9bdc1e4d2', 1, 4, 0, 1, 1245735445, 18);

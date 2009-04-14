-- phpMyAdmin SQL Dump
-- version 2.11.9.3
-- http://www.phpmyadmin.net
--
-- Host: mysql.boomboxlincoln.org
-- Generation Time: Feb 07, 2009 at 10:50 AM
-- Server version: 5.0.67
-- PHP Version: 5.2.6

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Database: `bblincoln`
--

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
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

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
  `toHome` int(1) NOT NULL default '1',
  `obscene` tinyint(1) NOT NULL default '1',
  `numImages` int(2) NOT NULL,
  `postTime` int(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

INSERT INTO `users` (`id`, `name`, `password`, `admin`, `obscene`, `numImages`, `postTime`) VALUES
(1, 'Anonymous', '', 0, 0, 0, 1, 1239058806),
(2, 'admin', '5baa61e4c9b93f3f0682250b6cf8331b7ee68fd8', 1, 1, 0, 0, 0);


--
-- Table structure for table `news`
--

CREATE TABLE IF NOT EXISTS `news` (
  `id` int(8) NOT NULL auto_increment,
  `postTime` int(32) NOT NULL,
  `news` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ;

-- --------------------------------------------------------

--
-- Table structure for table `albums`
--

CREATE TABLE IF NOT EXISTS `albums` (
  `id` int(8) NOT NULL auto_increment,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8;

ALTER TABLE `news` ADD `title` VARCHAR( 50 ) NOT NULL AFTER `postTime` ;

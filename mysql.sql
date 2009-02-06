-- phpMyAdmin SQL Dump
-- version 2.11.9.3
-- http://www.phpmyadmin.net
--
-- Host: mysql.boomboxlincoln.org
-- Generation Time: Jan 27, 2009 at 08:04 PM
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
  `queue` tinyint(1) NOT NULL default '1',
  `rating` int(4) NOT NULL default '0',
  `numComments` int(4) NOT NULL default '0',
  `obscene` tinyint(1) NOT NULL default '0',
  `report` int(2) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

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
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(8) NOT NULL auto_increment,
  `name` char(32) NOT NULL,
  `password` char(40) NOT NULL,
  `admin` tinyint(1) NOT NULL default '0',
  `obscene` tinyint(1) NOT NULL default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ;

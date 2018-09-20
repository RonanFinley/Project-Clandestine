-- phpMyAdmin SQL Dump
-- version 2.8.0.1
-- http://www.phpmyadmin.net
-- 
-- Host: custsql-ipg49.eigbox.net
-- Generation Time: Sep 20, 2018 at 04:27 PM
-- Server version: 5.6.37
-- PHP Version: 4.4.9
-- 
-- Database: `huntingtonpost`
-- 
CREATE DATABASE `huntingtonpost` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `huntingtonpost`;

-- --------------------------------------------------------

-- 
-- Table structure for table `articles`
-- 

CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext,
  `author` tinytext,
  `code` text,
  `link` tinytext,
  `preview` tinytext,
  `category` enum('Science','Technology','Engineering','Mathematics','Other','Intra') DEFAULT NULL,
  `date` date DEFAULT NULL,
  `popularity` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=latin1 AUTO_INCREMENT=67 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `drafts`
-- 

CREATE TABLE `drafts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` tinytext,
  `author` tinytext,
  `code` text,
  `preview` tinytext,
  `category` enum('Science','Technology','Engineering','Mathematics','') DEFAULT NULL,
  `date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=latin1 AUTO_INCREMENT=70 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `favs`
-- 

CREATE TABLE `favs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `articleID` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `logs`
-- 

CREATE TABLE `logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` tinytext NOT NULL,
  `action` tinytext NOT NULL,
  `time` date NOT NULL,
  `type` enum('general','modify','error','security','important','') NOT NULL DEFAULT 'general',
  `significance` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=278 DEFAULT CHARSET=latin1 AUTO_INCREMENT=278 ;

-- --------------------------------------------------------

-- 
-- Table structure for table `users`
-- 

CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` tinytext,
  `password` tinytext,
  `auth` enum('admin','writer','insider','user') DEFAULT 'user',
  `status` enum('available','suspended','deletion') DEFAULT 'available',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1 AUTO_INCREMENT=9 ;

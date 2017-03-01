<?php

	require 'config/DB.php';

	class Controller_migration
	{
		public function action_index()
		{
			$db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD);
			$q[0] = "CREATE DATABASE IMDB;";
			$q[1] = "USE IMDB;";
			$q[2] = "CREATE TABLE `films` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(50) NOT NULL,
			  `year` date DEFAULT NULL,
			  `format` enum('VHS','DVD','Blu-Ray') NOT NULL DEFAULT 'VHS',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `nameYear` (`name`,`year`)
			) DEFAULT CHARSET=utf8;";
			$q[3] = "CREATE TABLE `actors` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `name` varchar(40) NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `name` (`name`)
			)DEFAULT CHARSET=utf8;";
				$q[4] = "CREATE TABLE `actors_in_films` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `film_id` int(11) NOT NULL,
			  `actor_name` varchar(40) NOT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `unique_row` (`film_id`,`actor_name`),
			  KEY `film_id` (`film_id`),
			  KEY `actor_name` (`actor_name`),
			  CONSTRAINT `actors_in_films_ibfk_1` FOREIGN KEY (`film_id`) REFERENCES `films` (`id`),
			  CONSTRAINT `actors_in_films_ibfk_2` FOREIGN KEY (`actor_name`) REFERENCES `actors` (`name`) ON UPDATE CASCADE
			)DEFAULT CHARSET=utf8";
			foreach ($q as $query) {
				if($db->query($query) === false) 
					echo($this->db->error);
			}
			echo('<a href="/films">To site</a>');
		}
	}
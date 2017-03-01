<?php

require 'config/DB.php';

class Model
{
	protected $db;

	public function __construct()
	{
		$this->db = new mysqli(DB_HOST, DB_USER, DB_PASSWORD,DB_DATABASE);
		if ($this->db->connect_error) {
			trigger_error('Database connection failed: '  . $this->db->connect_error, E_USER_ERROR);
		}
		else
		{
			$this->db->query('SET NAMES utf8;');
		}
	}
}
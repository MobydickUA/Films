<?php

class Model_films extends Model
{
	private $format_enum = [
		'VHS' => 1,
		'DVD' => 2,
		'Blu-Ray' => 3
	];

	public function getData()
	{
		$res = [];
		$this->db->query("SET NAMES utf8");
		$tmp = $this->db->query("SELECT * FROM films ORDER BY name");		
		while($row = $tmp->fetch_assoc())
			array_push($res,$row);
		return $res;
	}

	public function searchByTitle($name)
	{
		$res = [];
		$tmp = $this->db->query("SELECT * FROM films WHERE name LIKE '%" . $this->db->real_escape_string($name) . "%' order by name;");
		while($row = $tmp->fetch_assoc())
			array_push($res,$row);
		return $res;
	}

	public function searchByName($name)
	{
		$query = $this->db->query("SELECT * FROM films WHERE id IN (SELECT film_id from actors_in_films where actor_name LIKE '%" . $this->db->real_escape_string($name) . "%' ); ");
		// die(var_dump($this->db));
		return $query->fetch_all(MYSQLI_ASSOC);
	}

	public function deleteFilm($id)
	{
		if(intval($id) > 0) {
			$name = $this->db->query("SELECT name FROM films WHERE id=$id");
			$this->db->query("DELETE FROM actors_in_films where film_id=$id;");
			$res = $this->db->query("DELETE FROM films where id=$id;");
			if($this->db->affected_rows)
				return $name->fetch_all(MYSQLI_ASSOC);
			else
				return 0;
		}
		else {
			return 0;
		}
	}

	public function searchById($id)
	{
		if(intval($id) == 0)
			return 0;
		// $sql = "SELECT films.id, films.name as title, films.year, films.format, actors.name from films RIGHT JOIN actors_in_films on films.id = actors_in_films.film_id RIGHT JOIN actors ON actors_in_films.actor_id = actors.id WHERE films.id = ?;";
		$sql = "SELECT films.id, films.name as title, films.year, films.format, actors_in_films.actor_name from films LEFT JOIN actors_in_films ON films.id = actors_in_films.film_id WHERE films.id=?";
		$query = $this->db->prepare($sql);
		$query->bind_param('i', $id);
		$a = $query->execute();
		$rs = $query->get_result();
		return $rs->fetch_all(MYSQLI_ASSOC);

	}

	public function parse($filename)
	{
		$films = $this->read($filename);
		$this->write($films);
	}

	private function read($filename)
	{
		$f = fopen($filename, "r");
		$films = [];
		$film = [];
		$film['actors'] = [];
		while (($line = fgets($f)) !== false) {
			$tmp = explode(":",$line);
			if(count($tmp) > 2)
				$tmp[1] = ltrim(rtrim($tmp[1] . ': ' . $tmp[2]));
			switch ($tmp[0]) {
				case 'Title':
					$film['name'] = ltrim(rtrim($tmp[1]));
					break;
				case 'Release Year':
					$film['year'] = trim($tmp['1']) . "-01-01";
					break;
				case 'Format':
					$film['format'] = trim($tmp[1]);
					break;
				case 'Stars':
					foreach($actors = explode(',',$tmp[1]) as $act)
						array_push($film['actors'], rtrim(ltrim($act)));
					break;
				default:
					array_push($films,$film);
					$film = [];
					$film['actors'] = [];
					break;
			}
		}
		fclose($f);
		unlink($filename);
		return $films;
	}

	private function write($films)
	{
		$sql = "INSERT IGNORE INTO actors(name) VALUES ";
		$sql2 = "INSERT IGNORE INTO films (name, year, format) VALUES ";
		$sql3 = "INSERT IGNORE INTO actors_in_films (film_id,actor_name) VALUES ";
		// $sql3 = "INSERT INTO actors_in_films (film_id,actor_id) VALUES ";		
		foreach ($films as $film) {
			if(count($film['actors']) == 0)
				continue;
			$sql2 .= "('" . htmlspecialchars($this->db->real_escape_string($film['name'])) . "','" . $film['year'] . "'," . $this->format_enum[$film['format']] . "),";
			foreach ($film['actors'] as $act) {
				$sql .= "('" . htmlspecialchars($this->db->real_escape_string($act)) . "'),";
				// $sql3 .= "((SELECT id FROM films WHERE name='" . $film['title'] . "' AND year='" . $film['year'] . "' LIMIT 1), (SELECT id FROM actors WHERE name='" . $act ."' LIMIT 1)),";
				$sql3 .= "((SELECT id FROM films WHERE name='" . $this->db->real_escape_string($film['name']) . "' AND year='" . $film['year'] . "' LIMIT 1), '" . htmlspecialchars($this->db->real_escape_string($act)) ."'),";
			}
		}
		$sql = rtrim($sql,',') . ";";
		$sql2 = rtrim($sql2,',') . ";";
		$sql3 = rtrim($sql3,',') . ";";
		$this->db->query($sql);
		$this->db->query($sql2);
		$this->db->query($sql3);
		$flag = false;
		if($this->db->query($sql) === false) {
			trigger_error('Wrong SQL: ' . $sql . ' Error: ' . $this->db->error, E_USER_ERROR);
			$flag = true;
		}
		if($this->db->query($sql2) === false) {
			trigger_error('Wrong SQL: ' . $sql2 . ' Error: ' . $this->db->error, E_USER_ERROR);
			$flag = true;
		}
		if($this->db->query($sql3) === false) {
			trigger_error('Wrong SQL: ' . $sql3 . ' Error: ' . $this->db->error, E_USER_ERROR);
			$flag = true;
		}
		if($flag)
			return 0;
		return 1;
	}

	public function prepareFilmData($film)
	{
		$tmp[0] = $film;
		$tmp[0]['year'] = $tmp[0]['year'] . "-01-01";
		$tmp[0]['actors'] = [];
		foreach (explode(",",$film['actors']) as $act) {
			array_push($tmp[0]['actors'], ltrim(rtrim($act)));
		}
		return $this->write($tmp);
	}
}
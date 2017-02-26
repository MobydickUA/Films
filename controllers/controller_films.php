<?php 

class Controller_films extends Controller
{
	function __construct()
	{
		$this->model = new Model_films();
	}

	function action_index()
	{	
		$data = $this->model->getData();
		$this->view('main_view',['data' => $data]);
	}

	function action_search_by_title()
	{	
		if(isset($_GET['film_name'])) {
			$data = $this->model->searchByTitle($_GET['film_name']);
			$this->view('main_view',['data' => $data]);
		}
		else {
			echo "error";
		}
	}

	public function action_search_by_name()
	{
		if(isset($_GET['name']))
		{
			$data = $this->model->searchByName($_GET['name']);
			$this->view('filmByName',['data' => $data]);
		}
		else
			$this->view('error');
	}

	public function action_load_file()
	{
		$this->view('upload_file');
	}

	public function action_add_film()
	{
		if(empty($_POST)) {
			$this->view('add_film_form');
		}
		else {
			$res = $this->model->prepareFilmData($_POST);
			if($res)
				$this->view('successUpload');
			else
				$this->view('error');
		}
	}

	public function action_delete()
	{
		if(isset($_GET['id'])) {
			$res = $this->model->deleteFilm($_GET['id']);
			if($res)
				$this->view('successDelete',['res' => $res]);
			else 
				$this->view('error');
		}
		else {
			$this->view('error');
		}
	}

	public function getFilm($id)
	{
		if(intval($id) == 0)
		{
			$this->view('error');
			return -1;
		}
		$data = $this->model->searchById($id);
		if($data)
			$this->view('film_info',['data' => $data]);
		else
			$this->view('error');
	}

	public function action_parse()
	{
		if(isset($_FILES))
		{
			if(explode('.',$_FILES['text']['name'])[1] == 'txt')
			{
				$this->model->parse($_FILES['text']['tmp_name']);
				$this->view('successUpload');
			}
			else
			{
				echo "File must be in .txt format";
			}
		}
		else
		{
			$this->view('error');
		}
	}
}
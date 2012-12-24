<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	
	class company_company extends Admin_Controller {
		
		//--------------------------------------------------------------------
		
		
		public function __construct()
		                                           {
			parent::__construct();
			
			$this->load->library('form_validation');
			$this->load->model('company_model', null, true);
			$this->lang->load('company');
			
		}
		
		//--------------------------------------------------------------------
		
		
		
		/*
			Method: index()
			
			Displays a list of form data.
		*/
		public function index()
		                                           {
			
		}
		
		//--------------------------------------------------------------------
		public function get_logo($path)
		                                           {
			$this->config->load('upload');
			$exts = explode("|",$this->config->item('allowed_types'));
			foreach ($exts as $ext)
			                                       {
				$img = LOGO_PATH.$path."logo.".$ext;
				if(file_exists("./".$img))
				return base_url().$img;
			}
			return '';
		}
		
		public function company_admin($company_id = 3)
		{		
			$company_data = $this->company_model->find_by('id', $company_id, 'and', 1);
			console::log($company_data['company_url'], true);
			Template::set('company_data', $company_data);
			Template::set_theme('Two');
			Template::render();			
		}
		public function video_list($company_id = 3)
		{
			//		Template::set('vid', $vid);
			//		Template::render();
			$this->load->model('video/video_model', null, true);
			$videos = $this->video_model->find_all_by('video_company_id', $company_id, 'and', 1);			
			if($videos !== false){
				//			print_r($videos, true);
				//			console::log(print_r($videos, true));
				Template::set('videos', $videos);
				Template::set_theme('Two');
				Template::render();
			}		
			else{
				//			console::log(print_r('error', true));	
			}
		}
		
		//fields:title,description,length,upload_time,view_count,average_rating
		public function _get_video_info($video_id)
		{
			$return=array();
			$this->load->model('video/video_model', null, true);
			$this->load->model('video_view_history/video_view_history_model', null, true);
			$this->load->model('reviews/reviews_model', null, true);
			$video_info = $this->video_model->find_by('id', $video_id, 'and', 1);
			if($video_info === false) return false;
			$return['id'] = $video_info['id'];
			$return['title'] = $video_info['video_title'];
			$return['description']= $video_info['video_description'];
			$return['length']= $video_info['video_length'];
			$return['upload_time']= $video_info['created_on'];
			$return['view_count'] = $this->video_view_history_model->get_view_count($video_id);
			$return['average_rating'] = $this->reviews_model->average_rating($video_id)===false?"n/a":$this->reviews_model->average_rating($video_id);
			// Console::log(print_r($return,true));
			return $return;
		}
		
		//fields:gender,birth_month,birth_year,race,education,occupation,zipcode,time,ip
		public function _get_view_history($video_id)
		{
			$return = array();
			$this->load->model('video_view_history/video_view_history_model', null, true);
			$this->load->model('user_info/user_info_model', null, true);
			$video_histories = $this->video_view_history_model->find_all_by('video_view_history_video_id', $video_id, 'and', 1);
			if($video_histories === false) return false;
			foreach($video_histories as $k=>$v)
			{
				$user_info = $this->user_info_model->get_user_info($v['video_view_history_user_id']);
				if($user_info!==false)
				{
					$return[$k]['gender'] = $user_info['user_info_gender']==1?'M':'F';
					$return[$k]['birth_month'] = $user_info['user_info_birth_month'];
					$return[$k]['birth_year'] = $user_info['user_info_birth_year'];
					$return[$k]['race'] = $user_info['user_info_race'];
					$return[$k]['education'] = $user_info['user_info_education'];
					$return[$k]['zipcode'] = $user_info['user_info_zipcode'];
					$return[$k]['occupation'] = $user_info['user_info_occupation_id'];
				}
				else
				{
					$return[$k]['gender'] = '';
					$return[$k]['birth_month'] = '';
					$return[$k]['birth_year'] = '';
					$return[$k]['race'] = '';
					$return[$k]['education'] = '';
					$return[$k]['zipcode'] = '';
					$return[$k]['occupation'] = '';
				}
				$return[$k]['time'] = date("Y-m-d h:j:s", $v['video_view_history_created_on']);
				$return[$k]['ip'] = $v['video_view_history_ip'];
			}
			// Console::log(print_r($return,true));
			return $return;
		}

		public function video_report($video_id)
		{
			Assets::add_js($this->load->view('inline_js/report.js.php',null,true),'inline');
			$video_info = $this->_get_video_info($video_id);
			$view_histories = $this->_get_view_history($video_id);
			Template::set('video_info', $video_info);
			Template::set('view_histories', $view_histories);
			Template::set_theme('Two');
			Template::render();
		}
		
		function video_report_prototype($video_id)
		{
			Assets::add_js($this->load->view('inline_js/report.js.php',null,true),'inline');
			$this->load->model('video_view_history/video_view_history_model', null, true);
			$video_histories = $this->video_view_history_model->find_all_by('video_view_history_video_id', $video_id, 'and', 1);
			if($video_histories === false) return false;
			$view_count = count($video_histories);
			for($i=0; $i<$view_count; $i++){
				$video_histories[$i]['video_view_history_created_on'] = date("Y-m-d h:j:s", $video_histories[$i]['video_view_history_created_on']);
			}
			
			if($video_id !== false){
				$this->load->model('video/video_model', null, true);
				$this->load->model('reviews/reviews_model', null, true);
				$this->load->model('question/question_model', null, true);
				$this->load->model('answer/answer_model', null, true);
				
				$average_rating = $this->reviews_model->average_rating($video_id);
				if($average_rating === false){
					$average_rating = "N/A";
				}
				$video_info = $this->video_model->find_by('id', $video_id, 'and', 1);			
				
				$video_ratings = $this->reviews_model->find_all_by('reviews_video_id', $video_id, 'and', 1);
				$review_count = count($video_ratings);
				console::log(print_r($review_count, true));	
				$question_count = 0;//set the initial value of question count
				if ($video_ratings !== false){
					for($i=0; $i<$review_count; $i++){
						//convert timestamp
						$video_ratings[$i]['reviews_last_update'] = date("Y-m-d h:j:s", $video_ratings[$i]['reviews_last_update']);
						//						console::log(print_r($video_ratings[$i]['reviews_last_update'], true));
						
						$answer_temp = json_decode($video_ratings[$i]['reviews_answers'], true);
						$answer_keys = array_values ($answer_temp);
						$question_keys = array_keys ($answer_temp);
						$question_count = count($question_keys);
						for($j=0; $j<$question_count; $j++){	
							$question = $this->question_model->find_by('id', $question_keys[$j], 'and', 1);
							$question_value = $question['question_content'];
							$answer_index = $question['question_answer_id'];
							$answer = $this->answer_model->find_by('id', $answer_index, 'and', 1);
							$answer_array = json_decode($answer['answer_content'], true);
							$answer_value = $answer_array[$answer_keys[$j]];
							//$QnA = $question_value.$answer_value;
							$video_ratings[$i]['Q'.$j] = $question_value;
							$video_ratings[$i]['A'.$j] = $answer_value; 
						}				 
					}
					
					Template::set('video_id', $video_id);		
					Template::set('video_info', $video_info);
					Template::set('question_count', $question_count);					
					Template::set('view_count', $view_count);
					Template::set('video_ratings', $video_ratings);	
					Template::set('average_rating', $average_rating);
					Template::set('video_histories', $video_histories);
					
				}
				Template::render();
			}
		}
		public function export_csv($export_type='review',$video_id)
		{
			$this->load->helper('download');
			$info = $this->_get_video_info($video_id);
			$results = $this->_get_view_history($video_id);
			if($results === false||$info===false) return false;
			$csv = '';
			//$infoheaderDisplayed = false;
			$csv.=$this->echocsv(array_keys($info));  
			$csv.=$this->echocsv($info);  
			$headerDisplayed = false;
			foreach ( $results as $data ) {
				// Add a header row if it hasn't been added yet
				if ( !$headerDisplayed ) {
					// Use the keys from $data as the titles
					$csv.=$this->echocsv(array_keys($data));  
					$headerDisplayed = true;
				}
				// Put the data into the stream
				$csv.=$this->echocsv($data);  
			}
			$name = $export_type.'_report_'.date("m-d-Y_His").'.csv';
			force_download($name, $csv);
		}
		public function echocsv($fields)
		{
			$return='';
			$separator = '';
			foreach ($fields as $field) {
				if (preg_match('/\\r|\\n|,|"/', $field)) {
					$field = '"' . str_replace('"', '""', $field) . '"';
				}
				$return.= $separator . $field;
				$separator = ',';
			}
			$return.= "\r\n";
			return $return;
		}
	}			
	
	
	

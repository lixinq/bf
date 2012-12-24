<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
	
	class Video_model extends BF_Model {
		
		protected $table		= "video";
		protected $key			= "id";
		protected $soft_deletes	= false;
		protected $date_format	= "datetime";
		protected $set_created	= true;
		protected $set_modified = true;
		protected $created_field = "created_on";
		protected $modified_field = "modified_on";
		
		public function get_company_name($id='')
		{
			if ($this->_function_check($id) === FALSE)
			{
				return FALSE;
			}
			$company_id = $this->get_field($id,'video_company_id');
			$query = $this->db->get_where('bf_company', array('id' => $company_id), 1);
			if ($query && $query->num_rows() > 0)
			{
				return $query->row()->company_name;
			}
			
			return FALSE;
		}
		
		public function get_company($vid)
		{
				$row=$this->find_by('id',$vid);
				if ($row ===false) return false;
 				$company=$this->load->model('company/company_model')->find_by('id',$row->video_company_id);
				if(strpos($company->company_url, 'http://')===false)
				{
				  $company->company_url='http://'. $company->company_url;
				}
				return $company;
		}
	}

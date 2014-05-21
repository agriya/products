<?php namespace Agriya\Products;

class ProductService
{
	function __construct()
	{
		$user = \Config::get('products::logged_user_id');
		$this->logged_user_id = $user();
    }

	public function addSectionName($section_name)
	{
		$section_details = UserProductSection::Select('id', 'user_id')->whereRaw('section_name = ?', array($section_name))->first();
		if(count($section_details) > 0)
		{
			if($section_details->user_id != $this->logged_user_id)
			{
				//throw exception
			}
			$user_section_id = $section_details->id;
		}
		else
		{
			$data_arr = array('user_id' => $user_id,
                          	  'section_name' => $section_name,
                          	  'status' => 'Yes',
                          	  'date_added' => \DB::raw('NOW()'),
                        );
            $user_section_id = UserProductSection::insertGetId($data_arr);
		}
	    return $user_section_id;
	}
}
<?php namespace Agriya\Products;

use DB;
class Category {

	protected $category_id;

	protected $fields_arr = array();

	public function __construct($category_id = '')
	{
		$this->category_id = $category_id;
	}

	public function getCategoryId()
	{
		return $this->category_id;
	}

	public function setCategoryName($val)
	{
		$this->fields_arr['category_name'] = $val;
	}

	public function setSlugUrl($val)
	{
		$this->fields_arr['seo_category_name'] = $val;
	}

	public function setCategoryDescription($val)
	{
		$this->fields_arr['category_description'] = $val;
	}

	public function setCategoryStatus($val)
	{
		$this->fields_arr['status'] = $val;
	}

	public function setMetaTitle($val)
	{
		$this->fields_arr['category_meta_title'] = $val;
	}

	public function setMetaDescription($val)
	{
		$this->fields_arr['category_meta_description'] = $val;
	}

	public function setMetaKeyword($val)
	{
		$this->fields_arr['category_meta_keyword'] = $val;
	}

	public function setParentCategoryId($val)
	{
		$this->fields_arr['parent_category_id'] = $val;
	}

	public function validateCategoryDetails($input_arr, $cat_id = 0, $parent_cat_id = 0)
	{
		$rules_arr = array(
				'category_name' => 'Required|unique:product_category,category_name,'.$cat_id.',id,parent_category_id,'.$parent_cat_id,
				'seo_category_name' => 'Required|IsValidSlugUrl:'.$input_arr['seo_category_name'].'|unique:product_category,seo_category_name,'.$cat_id.',id,parent_category_id,'.$parent_cat_id,
				'status' => 'Required'
		);

		$message = array('category_name.required' => 'Category name required',
						'category_name.unique' => 'Category already exists',
						'seo_category_name.required' => 'Slug url required',
						'seo_category_name.is_valid_slug_url' => 'Invalid slug url',
						'seo_category_name.unique' => 'Slug url already exists',
						'status.required' => 'Status required'
						);
		return array('rules' => $rules_arr, 'messages' => $message);
	}

	public function getNodeInfo($id)
	{
		$cat_info = ProductCategory::Select('category_left', 'category_right', 'category_level')->whereRaw('id = ?', array($id))->first();
		if(count($cat_info) > 0)
		{
			return array($cat_info['category_left'], $cat_info['category_right'], $cat_info['category_level']);
		}
		return false;
	}

	public function getCategoryLevel($parent_category_id = 0)
	{
		if($parent_category_id)
		{
			$cat_level_details = ProductCategory::Select('category_level')->whereRaw('id = ?', array($parent_category_id))->first();
			if(count($cat_level_details) > 0)
			{
				return $cat_level_details['category_level'] + 1;
			}
			return 1;
		}
		return 1;
	}

	public function save()
	{
		$cat_id = 0;
		$category_id = 0;
		if($this->category_id != '')
		{
			$cat_id = $this->category_id;
		}
		$parent_category_id = Products::getRootCategoryId();
		if(isset($this->fields_arr['parent_category_id']) && $this->fields_arr['parent_category_id'] > 0)
		{
			$parent_category_id = $this->fields_arr['parent_category_id'];
		}
		$this->fields_arr['parent_category_id'] = $parent_category_id;

		$validator_arr = $this->validateCategoryDetails($this->fields_arr, $cat_id, $parent_category_id);
		$validator = \Validator::make($this->fields_arr, $validator_arr['rules'], $validator_arr['messages']);
		if($validator->passes())
		{
			if($cat_id == 0)
			{
				if(count($this->fields_arr) > 0)
				{
					if (list($left_id, $right_id, $level) = $this->getNodeInfo($parent_category_id))
					{
						ProductCategory::where('category_right', '>=', $right_id)->update(array("category_left" => DB::raw('IF(category_left > '.$right_id. ',category_left + 2,category_left)'), "category_right" => DB::raw('IF(category_right >= '.$right_id. ',category_right + 2, category_right)')));

						$this->fields_arr['date_added'] = DB::raw('NOW()');
						$this->fields_arr['category_level'] = $this->getCategoryLevel($parent_category_id);
						$this->fields_arr['category_left'] = $right_id;
						$this->fields_arr['category_right'] = $right_id + 1;
						$this->fields_arr['available_sort_options'] = 'all';

						$category_id = ProductCategory::insertGetId($this->fields_arr);
					}
				}
			}
			else
			{
				if(count($this->fields_arr) > 0)
				{
					$category_id = $cat_id;
					$this->fields_arr['category_level'] = $this->getCategoryLevel($parent_category_id);
					ProductCategory::whereRaw('id = ?', array($category_id))->update($this->fields_arr);
				}
			}
			return json_encode(array('status' => 'success', 'category_id' => $category_id));
		}
		else
		{
			$error_msg = $validator->errors()->all();
			return json_encode(array('status' => 'error', 'error_messages' => $error_msg));
		}
	}
}
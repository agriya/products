<?php namespace Agriya\Products;

class Products {

	public $root_category_id = 0;

	public static function greeting()
	{
		return "What up dawg Products";
	}

	public static function initialize($product_id = '')
	{
		return new WebshopProduct($product_id);
	}

	public static function initializeCategory($category_id = '')
	{
		return new Category($category_id);
	}

	public static function initializeAttribute($attribute_id = '')
	{
		return new Attribute($attribute_id);
	}

	public static function getProductSections($user_id = 0)
	{
	  	$section_details = UserProductSection::where('status', '=', 'Yes');
		if($user_id > 0)
		{
			$section_details->where('user_id', '=', $user_id);
		}
		$section_arr = $section_details->get();
		return $section_arr;
	}

	public static function getTopCategories()
	{
		$category_details = ProductCategory::Select('id', 'category_name', 'category_level')->where('status', '=', 'active')->where('category_level', '=', 1)
							->orderBy('category_left', 'ASC')->get();
		return $category_details;
	}

	public static function getTopLevelCategoryIds($category_id)
	{
		$q = \DB::select('SELECT group_concat(parent.id ORDER BY parent.category_left) as category_ids from product_category AS node, product_category AS parent where node.category_left  BETWEEN parent.category_left AND parent.category_right AND node.id = ?', array($category_id));
		return $q[0]->category_ids;
	}

	public static function getCategoriesList($category_id = 0, $cat_status = '')
	{
		$sub_cat_arr = ProductCategory::Select('id', 'category_name', 'seo_category_name', 'category_level', 'category_left', 'category_right', 'parent_category_id', 'category_level', 'display_order');
		if($category_id > 0)
		{
			$sub_cat_arr = $sub_cat_arr->where('parent_category_id', '=', $category_id);
		}
		if($cat_status == '')
		{
			$sub_cat_arr = $sub_cat_arr->where('status', '=', 'active');
		}
		$sub_cat_arr = $sub_cat_arr->orderBy('category_left', 'ASC')->get();
		return $sub_cat_arr;
	}

	public static function initializeShops($product_id = '')
	{
		return new Shops($product_id);
	}

	public static function getRootCategoryId()
	{
		$root_cat = ProductCategory::Select('id')->whereRaw('category_level = 0 AND parent_category_id = 0')->first();
		if(count($root_cat) > 0)
		{
			$root_category_id = $root_cat['id'];
		}
		return $root_category_id;
	}

	public static function insertRootCategory()
	{
		$id = self::getRootCategoryId();
		if($id == 0)
		{
			$arr['seo_category_name'] = "Root";
			$arr['category_left'] = 1;
			$arr['category_right'] = 2;
			$arr['category_level'] = 0;
			$id = ProductCategory::insertGetId($arr);
		}
		return $id;
	}

	public static function getAttributeOptions($attribute_id)
	{
		$d_arr = ProductAttributeOptions::where('attribute_id', '=', $attribute_id)	->orderBy('id', 'ASC')->get(array('id', 'option_label', 'is_default_option'))->toArray();
		$data = array();
		foreach($d_arr AS $val)
		{
			$data[$val['id']] = $val['option_label'];
		}
		return $data;
	}

	public static function getProductCountForAllCategories()
	{
		$prod_cat_count_arr = array();
		$product_details = \DB::select('select parent.id AS category_id, COUNT(prod.id) product_count from product_category AS node, product_category AS parent,
										product AS prod where node.category_left BETWEEN parent.category_left AND parent.category_right
										AND node.id = prod.product_category_id AND prod.product_status != \'Deleted\' AND prod.product_status = \'Ok\'
										GROUP BY parent.id ORDER BY node.category_left');
		if (count($product_details)) {
			foreach($product_details as $product)
			{
				$prod_cat_count_arr[$product->category_id] = $product->product_count;
			}
		}
		return $prod_cat_count_arr;
	}

	public static function getCategoryName($cat_id)
	{
		$category_name = '';
		$cat_info = ProductCategory::Select('category_name')->whereRaw('id = ?', array($cat_id))->first();
		if(count($cat_info) > 0)
		{
			$category_name = $cat_info['category_name'];
		}
		return $category_name;
	}

	public static function getCategoryDetails($cat_id)
	{
		$category_details = array();
		$cat_details = ProductCategory::Select('category_name', 'seo_category_name', 'category_description', 'parent_category_id', 'status', 'id',
					'available_sort_options', 'is_featured_category', 'image_name', 'image_ext', 'image_width', 'image_height', 'category_meta_title', 'category_meta_keyword', 'category_meta_description')
					->whereRaw('id = ?', array($cat_id))->first();
		if(count($cat_details) > 0)
		{
			$category_details = $cat_details;
		}
		return $category_details;
	}

	public static function getCategoryDetailsBySlug($slug)
	{
		$cat_details = ProductCategory::where('seo_category_name', '=', $slug)->get();
		return $cat_details;
	}

	public static function getProductCountForCategory()
	{
		// Get sub category ids
		$this->sub_category_ids = $this->getSubCategoryIds($category_id);
		if(!$this->sub_category_ids)
			$this->sub_category_ids = $category_id;

		$sub_category_ids = explode(',', $this->sub_category_ids);
		$product_count = Product::whereIn('product_category_id', $sub_category_ids)->count();
	    return $product_count;
	}

	public static function getParentCategoryIds($category_id)
	{
		$parent_category_ids = 0;
		$root_category_id = self::getRootCategoryId();
		$cat_details = \DB::select('select parent.id AS parent_category_id from product_category node, product_category parent where
									node.category_left BETWEEN parent.category_left AND parent.category_right AND node.id = ? AND parent.id != ? AND parent.id != ?
									ORDER BY parent.category_left', array($category_id, $root_category_id, $category_id));
		if (count($cat_details) > 0)
		{
			foreach($cat_details as $cat)
			{
				$parent_category_ids = ($parent_category_ids)?($parent_category_ids . ',' .$cat->parent_category_id ):$cat->parent_category_id;
			}
		}
		return $parent_category_ids;
	}

	public static function getAttributesAssignedForCategory($category_id)
	{
		// get all parent category ids
		$parent_category_ids = self::getParentCategoryIds($category_id);

		$attr_details = \DB::select('SELECT A.attribute_id, A.category_id FROM product_category_attributes AS A, product_attributes AS B WHERE	A.attribute_id = B.id AND (A.category_id IN (' . $parent_category_ids .') OR A.category_id = ? ) ORDER BY A.display_order', array($category_id));
		return $attr_details;
	}

	public static function getProductAttributeDetails($attribute_id = '', $paginate = false, $per_page = '')
	{
		$option_fields = array('select', 'check', 'option', 'multiselectlist');
		$return_row = array();
		$attr_details = ProductAttributes::Select('id', 'attribute_question_type', 'validation_rules', 'default_value', 'status', 'is_searchable', 'show_in_list',
												'description', 'attribute_label');
		if($attribute_id != '')
		{
			$attr_details = $attr_details->whereRaw('id = ?', array($attribute_id));
		}
		$attr_details = $attr_details->orderBy('id', 'ASC');

		if($paginate == true)
		{
			$attr_details = $attr_details->paginate($per_page);
			return $attr_details;
		}
		else
		{
			$attr_details = $attr_details->get();
		}

		if(count($attr_details) > 0)
		{
			foreach($attr_details as $attr)
			{
				$return_row[$attr->id]['attribute_id'] = $attr->id;
				$return_row[$attr->id]['is_searchable'] = $attr->is_searchable;
				$return_row[$attr->id]['show_in_list'] = $attr->show_in_list;
				$return_row[$attr->id]['attribute_question_type'] = $attr->attribute_question_type;
				$return_row[$attr->id]['attribute_label'] = $attr->attribute_label;
				if(in_array($attr->attribute_question_type, $option_fields))
				{
					$attr_options = self::getProductAttributeOptions($attr->id);
					$return_row[$attr->id]['default_value'] = is_null($attr->default_value) ? '' :self::getAttributeDefaultOptionValue($attr->default_value);
				}
				else
				{
					$attr_options = array();
					$return_row[$attr->id]['default_value'] = is_null($attr->default_value) ? '' :$attr->default_value;
				}
				$return_row[$attr->id]['attribute_options'] = $attr_options;
				$return_row[$attr->id]['validation_rules'] = is_null($attr->validation_rules) ? '' :$attr->validation_rules ;
				$return_row[$attr->id]['status'] = $attr->status;
				$return_row[$attr->id]['description'] = $attr->description;
			}
		}
		return $return_row;
	}

	public static function getAttributeOptionDetails($attribute)
	{
		$attr_option_arr = array();
		$options = array('select', 'check', 'option', 'multiselectlist');
		if(in_array($attribute['attribute_question_type'], $options))
		{
			$attr_options = self::getProductAttributeOptions($attribute['id']);
			$default_value = is_null($attribute['default_value']) ? '' :self::getAttributeDefaultOptionValue($attribute['default_value']);
		}
		else
		{
			$attr_options = array();
			$default_value = is_null($attribute['default_value']) ? '' :$attribute['default_value'];
		}
		$attr_option_arr['attr_options'] = $attr_options;
		$attr_option_arr['default_value'] = $default_value;
		return $attr_option_arr;
	}

	public static function getProductAttributeOptions($attribute_id)
	{
		$attribute_options = array();
		$attr_option_details = ProductAttributeOptions::whereRaw('attribute_id = ?', array($attribute_id))->get(array('id', 'option_label', 'is_default_option'));
		if(count($attr_option_details) > 0)
		{
			foreach($attr_option_details as $attr_option)
			{
				$attribute_options[$attr_option->id]['option_label'] = $attr_option->option_label;
				$attribute_options[$attr_option->id]['is_default_option'] = $attr_option->is_default_option;
			}
		}
		return $attribute_options;
	}

	public static function getAttributeDefaultOptionValue($attribute_option_id)
	{
		$option_value = ProductAttributeOptions::whereRaw('id = ?', array($attribute_option_id))->pluck('option_label');
		return $option_value;
	}

	public static function updateAssignedAttributeDisplayOrder($attribute_id, $category_id, $display_order)
	{
		$data_arr['display_order'] = $display_order;
		ProductCategoryAttributes::whereRaw('attribute_id = ? AND category_id = ?', array($attribute_id, $category_id))->update($data_arr);
	}
}
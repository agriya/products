<?php namespace Agriya\Products;

class ProductNotFoundException extends \Exception {}
class InvalidProductIdException extends \Exception {}

use DB;
class WebshopProduct {

	protected $product_id;

	protected $fields_arr = array();

	protected $section_arr = array();

	protected $filter_section_id = '';

	protected $filter_product_status = '';

	protected $filter_product_code = '';

	protected $filter_product_name = '';

	protected $filter_product_category = '';

	protected $products_per_page = '';

	protected $filter_product_from_price = '';

	protected $filter_product_to_price = '';

	protected $order_by = '';

	protected $filter_keyword = '';

	protected $filter_product_id_from = '';

	protected $filter_product_id_to = '';

	protected $filter_featured_product = '';

	protected $sub_category_ids = array();

	public function __construct($product_id = '')
	{
		$this->product_id = $product_id;
	}

	public function getProductId()
	{
		return $this->product_id;
	}

	public function setProductId($val)
	{
		$this->product_id = $val;
	}

	public function setTitle($val)
	{
		$this->fields_arr['product_name'] = $val;
	}

	public function setDescription($val)
	{
		$this->fields_arr['product_description'] = $val;
	}

	public function setSupportContent($val)
	{
		$this->fields_arr['product_support_content'] = $val;
	}

	public function setSummary($val)
	{
		$this->fields_arr['product_highlight_text'] = $val;
	}

	public function setCategory($val)
	{
		$this->fields_arr['product_category_id'] = $val;
	}

	public function setSection($val)
	{
		$this->section_arr['section'] = $val;
	}

	public function setDemoUrl($val)
	{
		$this->fields_arr['demo_url'] = $val;
	}

	public function setDemoDetails($val)
	{
		$this->fields_arr['demo_details'] = $val;
	}

	public function setProductTags($val)
	{
		$this->fields_arr['product_tags'] = $val;
	}

	public function setMetaTitle($val)
	{
		$this->fields_arr['meta_title'] = $val;
	}

	public function setMetaDescription($val)
	{
		$this->fields_arr['meta_description'] = $val;
	}

	public function setMetaKeyword($val)
	{
		$this->fields_arr['meta_keyword'] = $val;
	}

	public function setIsFreeProduct($val)
	{
		$this->fields_arr['is_free_product'] = $val;
	}

	public function setProductPrice($val)
	{
		$this->fields_arr['product_price'] = $val;
	}

	public function setProductPriceCurrency($val)
	{
		$this->fields_arr['product_price_currency'] = $val;
	}

	public function setPriceAfterDiscount($val)
	{
		$this->fields_arr['product_discount_price'] = $val;
	}

	public function setDiscountPriceFromDate($val)
	{
		$this->fields_arr['product_discount_fromdate'] = $val;
	}

	public function setDiscountPriceToDate($val)
	{
		$this->fields_arr['product_discount_todate'] = $val;
	}

	public function setFilterSectionId($val)
	{
		$this->filter_section_id = $val;
	}

	public function setFilterProductStatus($val)
	{
		$this->filter_product_status = $val;
	}

	public function setFilterProductCode($val)
	{
		$this->filter_product_code = $val;
	}

	public function setFilterProductName($val)
	{
		$this->filter_product_name = $val;
	}

	public function setFilterProductCategory($val)
	{
		$this->filter_product_category = $val;
	}

	public function setProductUserId($val)
	{
		$this->fields_arr['product_user_id'] = $val;
	}

	public function setProductPagination($val)
	{
		$this->products_per_page = $val;
	}

	public function setDeliveryDays($val)
	{
		$this->fields_arr['delivery_days'] = $val;
	}

	public function setIsDownloadableProduct($val)
	{
		$this->fields_arr['is_downloadable_product'] = $val;
	}

	public function setFilterProductFromPrice($val)
	{
		$this->filter_product_from_price = $val;
	}

	public function setFilterProductToPrice($val)
	{
		$this->filter_product_to_price = $val;
	}

	public function setOrderByField($val)
	{
		$this->order_by = $val;
	}

	public function setFilterKeyword($val)
	{
		$this->filter_keyword = $val;
	}

	public function setFilterProductIdFrom($val)
	{
		$this->filter_product_id_from = $val;
	}

	public function setFilterProductIdTo($val)
	{
		$this->filter_product_id_to = $val;
	}

	public function setFilterFeaturedProduct($val)
	{
		$this->filter_featured_product = $val;
	}

	public function addSectionName($section_name)
	{
		$logged_user_id = 0;
		$user_section_id = 0;
		if(count($this->fields_arr) > 0 && isset($this->fields_arr['product_user_id']))
		{
			$logged_user_id = $this->fields_arr['product_user_id'];
		}
		if($logged_user_id > 0)
		{
			$section_details = UserProductSection::Select('id', 'user_id')->whereRaw('section_name = ?', array($section_name))->first();
			if(count($section_details) > 0)
			{
				if($section_details->user_id != $logged_user_id)
				{
					//throw exception
				}
				$user_section_id = $section_details->id;
			}
			else
			{
				$data_arr = array('user_id' => $logged_user_id,
	                          	  'section_name' => $section_name,
	                          	  'status' => 'Yes',
	                          	  'date_added' => DB::raw('NOW()'),
	                        );
	            $user_section_id = UserProductSection::insertGetId($data_arr);
			}
		}
		return $user_section_id;
	}

	public function addSection($section_name)
	{
		$input_arr['section_name'] = $section_name;
		$id = 0;
		$rules_arr = array('section_name' => 'Required|unique:user_product_section,section_name,'.$id);
		$message_arr = array('section_name.unique' => 'Section already exists');
		$validator = \Validator::make($input_arr, $rules_arr, $message_arr);
		if($validator->passes())
		{
			$logged_user_id = 0;
			if(count($this->fields_arr) > 0 && isset($this->fields_arr['product_user_id']))
			{
				$logged_user_id = $this->fields_arr['product_user_id'];
			}
			$data_arr = array('user_id' => $logged_user_id,
                          	  'section_name' => $section_name,
                          	  'status' => 'Yes',
                          	  'date_added' => DB::raw('NOW()'),
                        );
            $user_section_id = UserProductSection::insertGetId($data_arr);
            return json_encode(array('status' => 'success', 'user_section_id' => $user_section_id));
		}
		else
		{
			$errors = $validator->getMessageBag()->toArray();
			return json_encode(array('status' => 'error', 'error_messages' => $errors['section_name']));
		}
	}

	public function save()
	{
		$p_id = 0;
		if(count($this->section_arr) > 0 && isset($this->section_arr['section']))
		{
			if(is_numeric($this->section_arr['section']) && $this->section_arr['section'] > 0)
			{
				$user_section_id = $this->section_arr['section'];
			}
			else
			{
				$user_section_id = $this->addSectionName($this->section_arr['section']);
			}
			$this->fields_arr['user_section_id'] = $user_section_id;
		}

		$validator_arr = $this->validateProductDetails($this->fields_arr);

		$filter_rules_arr = array_intersect_key($validator_arr['rules'], $this->fields_arr);
		$filter_messages_arr = array_intersect_key($validator_arr['messages'], $this->fields_arr);

		$validator = \Validator::make($this->fields_arr, $filter_rules_arr, $filter_messages_arr);
		if($validator->passes())
		{
			if($this->product_id == '')
			{
				if(count($this->fields_arr) > 0)
				{
					$product_code = $product_code = CUtil::generateRandomUniqueCode('P', 'product', 'product_code');
					$this->fields_arr['product_code'] = $product_code;
					if(isset($this->fields_arr['product_name']))
					{
						$url_slug = \Str::slug($this->fields_arr['product_name']);
						$this->fields_arr['url_slug'] = $url_slug;
					}


					if(isset($this->fields_arr['is_free_product']) && $this->fields_arr['is_free_product'] == 'No')
					{
						if(isset($this->fields_arr['product_discount_price']) && $this->fields_arr['product_discount_price'] > 0)
						{
							if(isset($this->fields_arr['product_discount_fromdate']))
							{
								$from_date = str_replace('/', '-', $this->fields_arr['product_discount_fromdate']);
								$from_date = date('Y-m-d', strtotime($from_date));

								$this->fields_arr['product_discount_fromdate'] =  $from_date;
							}

							if(isset($this->fields_arr['product_discount_todate']))
							{
								$to_date = str_replace('/', '-', $this->fields_arr['product_discount_todate']);
								$to_date = date("Y-m-d", strtotime($to_date));

								$this->fields_arr['product_discount_todate'] =  $to_date;
							}
						}

						if(isset($this->fields_arr['product_price']) && $this->fields_arr['product_price'] > 0)
						{
							if(isset($this->fields_arr['product_price_currency']))
							{
								$product_price_currency = $this->fields_arr['product_price_currency'];
							}
							if($product_price_currency == '') {
								$product_price_currency = \Config::get('products::site_default_currency');
							}
							$this->fields_arr['product_price_currency'] = $product_price_currency;

						 	$this->fields_arr['product_price_usd'] = CUtil::convertBaseCurrencyToUSD($this->fields_arr['product_price'], $product_price_currency);

							if(isset($this->fields_arr['product_discount_price']) && $this->fields_arr['product_discount_price'] > 0)
							{
								$this->fields_arr['product_discount_price_usd'] = CUtil::convertBaseCurrencyToUSD($this->fields_arr['product_discount_price'], $product_price_currency);
							}
						}
					}

					$this->fields_arr['product_status'] = 'Draft';
					$this->fields_arr['product_added_date'] = DB::raw('NOW()');
					$this->fields_arr['last_updated_date'] = DB::raw('NOW()');

					$p_id = Product::insertGetId($this->fields_arr);

					//To add dumb data for product image
					$p_img_arr = array('product_id' => $p_id);
					$p_img_id = ProductImage::insertGetId($p_img_arr);
				}
			}
			else
			{
				if(count($this->fields_arr) > 0)
				{
					$p_id = $this->product_id;

					//To remove old category attribute values..
					if(isset($this->fields_arr['product_category_id']) && $this->fields_arr['product_category_id'] > 0)
					{
						$product_category_id = Product::whereRaw('id = ?', array($p_id))->pluck('product_category_id');
						if($product_category_id != $this->fields_arr['product_category_id'])
						{
							$this->removeProductCategoryAttribute();
						}
					}

					if(isset($this->fields_arr['is_free_product']) && $this->fields_arr['is_free_product'] == 'No')
					{
						if(isset($this->fields_arr['product_discount_price']) && $this->fields_arr['product_discount_price'] > 0)
						{
							if(isset($this->fields_arr['product_discount_fromdate']))
							{
								$from_date = str_replace('/', '-', $this->fields_arr['product_discount_fromdate']);
								$from_date = date('Y-m-d', strtotime($from_date));

								$this->fields_arr['product_discount_fromdate'] =  $from_date;
							}

							if(isset($this->fields_arr['product_discount_todate']))
							{
								$to_date = str_replace('/', '-', $this->fields_arr['product_discount_todate']);
								$to_date = date("Y-m-d", strtotime($to_date));

								$this->fields_arr['product_discount_todate'] =  $to_date;
							}
						}

						if(isset($this->fields_arr['product_price']) && $this->fields_arr['product_price'] > 0)
						{
							if(isset($this->fields_arr['product_price_currency']))
							{
								$product_price_currency = $this->fields_arr['product_price_currency'];
							}
							if($product_price_currency == '') {
								$product_price_currency = \Config::get('products::site_default_currency');
							}
							$this->fields_arr['product_price_currency'] = $product_price_currency;

						 	$this->fields_arr['product_price_usd'] = CUtil::convertBaseCurrencyToUSD($this->fields_arr['product_price'], $product_price_currency);

							if(isset($this->fields_arr['product_discount_price']) && $this->fields_arr['product_discount_price'] > 0)
							{
								$this->fields_arr['product_discount_price_usd'] = CUtil::convertBaseCurrencyToUSD($this->fields_arr['product_discount_price'], $product_price_currency);
							}
						}
					}
					$this->fields_arr['last_updated_date'] = DB::raw('NOW()');
					Product::whereRaw('id = ?', array($this->product_id))->update($this->fields_arr);
				}
			}
			return json_encode(array('status' => 'success', 'product_id' => $p_id));
		}
		else
		{
			$error_msg = $validator->errors()->all();
			return json_encode(array('status' => 'error', 'error_messages' => $error_msg));
		}
	}

	public function insertProductAttribute($attribute_id, $attribute_value)
	{
		$attr = new Attribute();
		if(is_numeric($this->product_id) && $this->product_id > 0)
	 	{
	 		$attribute_details = $attr->getAttributeDetails($attribute_id);
	 		if(count($attribute_details) > 0)
	 		{
	 			$rules_arr = $message_arr = array();
	 			$add_data = true;
	 			$error_message = '';
	 			$id = $attribute_id;
				$key = 'attribute_'.$id;
				$input_arr[$key] = $attribute_value;
				if($attribute_details['validation_rules'] != '')
				{
					$rule_str = str_replace('minlength-', 'min:', $attribute_details['validation_rules']);
					$rule_str = str_replace('maxlength-', 'max:', $rule_str);
					$rules_arr[$key] = $rule_str;
					$message_arr[$key.'.required'] = $attribute_details['attribute_label'].' required';
					$message_arr[$key.'.alpha'] = $attribute_details['attribute_label'].' should contain alphabets only';
					$message_arr[$key.'.numeric'] = $attribute_details['attribute_label'].' should contain numeric only';
				}
				if(count($rules_arr) > 0)
				{
					$validator = \Validator::make($input_arr, $rules_arr, $message_arr);
					if($validator->fails())
					{
						$add_data = false;
						$error_message = $validator->errors()->all();
					}
				}

	 			if($add_data)
	 			{
				 	$data_arr = array('product_id' => $this->product_id,
							  'attribute_id' => $attribute_id,
							  'attribute_value' => $attribute_value
							);
					ProductAttributesValues::insertGetId($data_arr);
					return json_encode(array('status' => 'success'));
				}
				else
				{
					return json_encode(array('status' => 'error', 'error_messages' => $error_message));
				}
			}
		}
	}

	public function insertAttributeOption($attribute_id, $attribute_options = array())
	{
		if(is_numeric($this->product_id) && $this->product_id > 0)
	 	{
	 		if(is_array($attribute_options))
	 		{
	 			foreach($attribute_options as $option)
	 			{
	 				//Get Attribute option id
	 				$attr_options = ProductAttributeOptions::Select('id')->whereRaw('option_label = ?', array($option))->first();
	 				if(count($attr_options) > 0)
	 				{
					 	$attr_options_id = $attr_options->id;
					 	$data_arr = array('product_id' => $this->product_id,
										  'attribute_id' => $attribute_id,
								          'attribute_options_id' => $attr_options_id
										  );
						ProductAttributesOptionValues::insertGetId($data_arr);
					}
				}
			}
	 	}
	}

	public function insertAttributeOptionByOptionId($attribute_id, $attribute_option_id)
	{
		$data_arr = array('product_id' => $this->product_id,
						'attribute_id' => $attribute_id,
						'attribute_options_id' => $attribute_option_id);
		ProductAttributesOptionValues::insertGetId($data_arr);
	}

	public function checkProductHasAttribute($category_id)
	{
		$category_ids = Products::getTopLevelCategoryIds($category_id);
		$cat_arr = explode(',', $category_ids);
		if(count($cat_arr) > 0)
		{
			$a_count = ProductCategoryAttributes::whereIn('category_id', $cat_arr)->count();
			if($a_count > 0)
			{
				return true;
			}
		}
		return false;
	}

	public function validateDownloadProduct($p_id)
	{
		if(\Config::get('products::download_files_is_mandatory'))
		{
			$count = ProductResource::whereRaw('product_id = ? AND resource_type = ?', array($p_id, 'Archive'))->count();
			return ($count == 0) ? false : true;
		}
		return true;
	}

	public function validateProductDetails($input_arr)
	{
		$rules_arr = $message_arr = array();

		$rules_arr += array('product_name' => 'Required|min:'.\Config::get("products::title_min_length").'|max:'.\Config::get("products::title_max_length"),
							'product_category_id' => 'Required',
							'product_tags' => 'Required',
							'product_highlight_text' => 'max:'.\Config::get("products::summary_max_length"),
							'demo_url' => 'url',
							'delivery_days' => 'numeric'
						);
		//To validate section, only if input from user form
		if(isset($input_arr['user_section_id'])  && $input_arr['user_section_id'] > 0)
		{
			$rules_arr['user_section_id'] = 'exists:user_product_section,id,user_id,'.$input_arr['product_user_id'];
		}

		if(isset($input_arr['is_free_product']))
		{
			$is_free_product = isset($input_arr['is_free_product'])? $input_arr['is_free_product']: 'No';
			if($is_free_product != 'Yes')
			{
				$rules_arr += array('product_price' => 'Required|IsValidPrice|numeric|Min:1',
								  'product_discount_price' => 'IsValidPrice|numeric|Max:'.$input_arr['product_price']
							 );
				if(isset($input_arr['product_discount_price']) && $input_arr['product_discount_price'] > 0)
				{
					$date_format = 'd/m/Y';
					if(isset($input_arr['product_discount_fromdate']))
					{
						$rules_arr['product_discount_fromdate'] = 'Required|date_format:VAR_DATE_FORMAT';
					}
					if(isset($input_arr['product_discount_todate']) && isset($input_arr['product_discount_fromdate']))
					{
						//check validation from database?..
						$from_date = str_replace('/', '-', $input_arr['product_discount_fromdate']);
						$from_date = date('Y-m-d', strtotime($from_date));

						$to_date = str_replace('/', '-', $input_arr['product_discount_todate']);
						$to_date = date('Y-m-d', strtotime($to_date));
						$rules_arr['product_discount_todate'] = 'Required|date_format:VAR_DATE_FORMAT|CustAfter:'.$from_date.','.$to_date;
						//To replace the datre format
						$rules_arr['product_discount_fromdate'] = str_replace('VAR_DATE_FORMAT', $date_format, $rules_arr['product_discount_fromdate']);
						$rules_arr['product_discount_todate'] = str_replace('VAR_DATE_FORMAT', $date_format, $rules_arr['product_discount_todate']);
					}
				}
				$message_arr += array('product_price.is_valid_price' => 'Invalid product price',
									'product_price.required' => 'Product price required',
									'product_discount_price.is_valid_price' => 'Invalid product discount price',
									'product_price.min' => 'Product price should be greater than zero',
									'product_discount_price.max' => 'Discount should be lesser than price',
									'product_discount_todate.cust_after' => 'Discount price To-date should be greater than from-date',
									'product_discount_fromdate.date_format' => 'Invalid date format (Product discount from date)',
									'product_discount_fromdate.required' => 'Product discount from date required',
									'product_discount_todate.date_format' => 'Invalid date format (Product discount to date)',
									'product_discount_todate.required' => 'Product discount to date required'
								);
			}
		}

		if(isset($input_arr['product_category_id']) && is_numeric($input_arr['product_category_id']))
		{
			$attr_arr = $this->getAttributesList($input_arr['product_category_id']);
			foreach($attr_arr AS $key => $val)
			{
				$id = $val['attribute_id'];
				$key = 'attribute_'.$id;
				if($val['validation_rules'] != '')
				{
					$rule_str = str_replace('minlength-', 'min:', $val['validation_rules']);
					$rule_str = str_replace('maxlength-', 'max:', $rule_str);
					$rules_arr[$key] = $rule_str;
					$message_arr[$key.'.required'] = $val['attribute_label'].' required';
					$message_arr[$key.'.alpha'] = $val['attribute_label'].' should contain alphabets only';
					$message_arr[$key.'.numeric'] = $val['attribute_label'].' should contain numeric only';
				}
			}
		}

		$message_arr += array('product_name.min' => 'Title length is too short. Minimum '. \Config::get("products::title_min_length") .' chars required.',
							'product_name.max' => 'Title length is too short. Maximum is '. \Config::get("products::title_max_length") .' chars.',
							'product_name.required' => 'Title required',
							'product_category_id.required' => 'Product Category required',
							'product_tags.required' => 'Product tags required',
							'product_highlight_text.max' => 'Summary length is too long. Maximum is '. \Config::get("products::summary_max_length") .' chars.',
							'demo_url.url' => 'Invalid Demo url',
							'user_section_id.exists' => 'Section is invalid',
							'delivery_days.numeric' => 'Delivery days should be numeric');
		return array('rules' => $rules_arr, 'messages' => $message_arr);
	}

	public function updateUserTotalProducts($user_id)
	{
		$p_count = $this->getTotalProduct($user_id);
		UsersShopDetails::where('user_id', '=', $user_id)->update( array('total_products' => $p_count));
	}

	public function getTotalProduct($user_id)
	{
		return Product::whereRaw('product_user_id = ? AND product_status = ?', array($user_id, 'Ok'))->count();
	}

	public function getProductCategoryAttributeValue($p_id, $product_category_id)
	{
		$input_arr = array();
		$attr_arr = $this->getAttributesList($product_category_id);
		foreach($attr_arr AS $key => $val)
		{
			$id = $val['attribute_id'];
			$key = 'attribute_'.$id;
			if($val['validation_rules'] != '')
			{
				$attr_type = $val['attribute_question_type'];
				switch($attr_type)
				{
					case 'text':
					case 'textarea':
						$input_arr[$key] = ProductAttributesValues::whereRaw('product_id = ? AND attribute_id = ?', array($p_id, $id))->pluck('attribute_value');
						break;

					case 'select':
					case 'option': // radio button
					case 'multiselectlist':
					case 'check': // checkbox
						$option_val = ProductAttributesOptionValues::whereRaw('product_id = ? AND attribute_id = ?', array($p_id, $id))->get( array('attribute_options_id'));
						foreach($option_val AS $option)
						{
							$input_arr[$key][] = $option->attribute_options_id;
						}
						break;
				}
			}
		}
		return $input_arr;
	}

	public function publish()
	{
		$allow_publish = false;
		$check_attributes = true;
		$download_product = true;
		$p_details = $this->getProductDetails();
		if(count($p_details) > 0)
		{
			if($p_details['product_discount_price'] > 0)
			{
				$p_details['product_discount_fromdate'] = date('d/m/Y', strtotime($p_details['product_discount_fromdate']));
				$p_details['product_discount_todate'] = date('d/m/Y', strtotime($p_details['product_discount_todate']));
			}
			if(isset($p_details['product_category_id'])) //No need to check for add product page
			{
				 $has_attr = $this->checkProductHasAttribute($p_details['product_category_id']);
				 if(!$has_attr)
				 {
				 	$check_attributes = false;
				 }
			}

			if(strtolower($p_details['is_downloadable_product']) == "yes" && !$this->validateDownloadProduct($this->product_id))
			{
				$download_product = false;
			}

			$input_arr = $p_details;
			if($check_attributes)
			{
				$input_arr += $this->getProductCategoryAttributeValue($this->product_id, $p_details['product_category_id']);
			}

			$validator_arr = $this->validateProductDetails($input_arr);
			$validator = \Validator::make($input_arr, $validator_arr['rules'], $validator_arr['messages']);
			if($validator->passes())
			{
				if(!$download_product)
				{
					return json_encode(array('status' => 'error', 'error_messages' => 'Add Downloadable product'));
				}
				//Can publish
				if($p_details['product_status'] != 'Ok' && \Config::get('products::product_auto_approve'))
				{
					$data_arr['product_status'] = 'Ok';
					$date_activated = Product::whereRaw('id = ?', array($this->product_id))->pluck('date_activated');
					if($date_activated == '0000-00-00 00:00:00')
					{
						$data_arr['date_activated'] = DB::raw('NOW()');
					}
					Product::whereRaw('id = ?', array($this->product_id))->update($data_arr);

					$this->updateUserTotalProducts($p_details['product_user_id']);

					$alert_message = 'Product is successfully published';
					return json_encode(array('status' => 'success', 'success_msg' => $alert_message));
				}
				else if($p_details['product_status'] != 'Ok' && $p_details['product_status'] != 'ToActivate')
				{
					$data_arr['product_status'] = 'ToActivate';
					Product::whereRaw('id = ?', array($this->product_id))->update($data_arr);
					$alert_message = 'Your product is submitted for approval!';
					return json_encode(array('status' => 'success', 'success_msg' => $alert_message));
				}
			}
			else
			{
				$error_msg = $validator->errors()->all();
				if(!$download_product)
				{
					$error_msg[] = 'Add Downloadable product';
				}
				return json_encode(array('status' => 'error', 'error_messages' => $error_msg));
			}
		}
		return json_encode(array('status' => 'error', 'error_messages' => 'Invalid Product Id'));
	}

	public function insertDownloadFile($file_name, $ext, $title)
	{
	    $data_arr = array('product_id' => $this->product_id,
	 		  		'resource_type' => 'Archive',
					'filename' => $file_name,
					'ext' => $ext,
					'title' => $title,
					'is_downloadable'=> 'Yes');
		ProductResource::insertGetId($data_arr);
	}

	public function insertPreviewFiles($file_name, $ext, $title, $server_url, $org_width, $org_height, $large_width, $large_height, $thumb_width, $thumb_height)
	{
		$data_arr = array('product_id' => $this->product_id,
	 		  		'resource_type' => 'Image',
					'filename' => $file_name,
					'ext' => $ext,
					'title' => $title,
					'width' => $org_width,
					'height' => $org_height,
					'l_width' => $large_width,
					'l_height' => $large_height,
					't_width' => $thumb_width,
					't_height' => $thumb_width,
					'server_url' => $server_url,
					'is_downloadable' => 'No'
					);
	   ProductResource::insertGetId($data_arr);
	}

	public function updateProductThumbImage($thumbnail_title, $thumbnail_img_name, $thumbnail_ext, $thumbnail_width, $thumbnail_height)
	{
		$data_arr = array('thumbnail_img' => $thumbnail_img_name,
						 'thumbnail_ext' =>	$thumbnail_ext,
						 'thumbnail_width' => $thumbnail_width,
						 'thumbnail_height' => $thumbnail_height,
						 'thumbnail_title' => $thumbnail_title);
		ProductImage::whereRaw('product_id = ?', array($this->product_id))->update($data_arr);
	}

	public function updateProductDefaultImage($default_title, $default_img_name, $default_ext, $default_width, $default_height, $org_width, $org_height)
	{
		$data_arr = array('default_img' => $default_img_name,
						'default_ext' => $default_ext,
						'default_width' => $default_width,
						'default_height' => $default_height,
						'default_title' => $default_title,
						'default_orig_img_width' => $org_width,
						'default_orig_img_height' => $org_height);
		ProductImage::whereRaw('product_id = ?', array($this->product_id))->update($data_arr);
	}

	public function changeStatus($product_status = 'Draft')
	{
	 	if(is_numeric($this->product_id) && $this->product_id > 0)
	 	{
	 		$update_arr['product_status'] = $product_status;
	 		if($product_status == 'Ok')
	 		{
	 			$update_arr['date_activated'] = DB::raw('NOW()');
			}
			else if($product_status == 'Draft')
			{
				$update_arr['last_updated_date'] = DB::raw('NOW()');
			}
			Product::whereRaw('id = ?', array($this->product_id))->update($update_arr);
		}
	}

	public function changeFeaturedStatus($featured_status)
	{
		if(is_numeric($this->product_id) && $this->product_id > 0)
	 	{
			Product::where('id', '=', $this->product_id)->update(array('is_featured_product' => $featured_status));
		}
	}

	public function getTotalProducts($user_id)
	{
		$product_cnt_qry = Product::whereRaw('product_user_id = ?', array($user_id));
		if($this->filter_product_status != '') {
			$product_cnt_qry = $product_cnt_qry->whereRaw('product_status = ?', array($this->filter_product_status));
		}
		$shop_count = $product_cnt_qry->count();
		return $shop_count;
	}

	/**
	 * Getting product list
	 *
	 * @author 		manikandan_133at10
	 * @return 		array
	 * @access 		public
	 */
	public function getProductsList($user_id = 0)
	{
		$product_details_arr = array();
		$product_details = Product::Select('product.id', 'product.product_code', 'product.product_name', 'product.product_description'
												, 'product.product_support_content', 'product.meta_title', 'product.meta_keyword'
												, 'product.meta_description', 'product.product_highlight_text', 'product.product_slogan'
												, 'product.product_price', 'product.product_price_usd', 'product.product_price_currency'
												, 'product.product_user_id', 'product.product_sold', 'product.product_added_date'
												, 'product.url_slug', 'product.demo_url', 'product.demo_details', 'product.product_category_id'
												, 'product.product_tags', 'product.total_views', 'product.is_featured_product'
												, 'product.is_user_featured_product', 'product.date_activated', 'product.product_discount_price'
												, 'product.product_discount_price_usd', 'product.product_discount_fromdate'
												, 'product.product_discount_todate', 'product.product_preview_type', 'product.is_free_product'
												, 'product.last_updated_date', 'product.total_downloads', 'product.product_moreinfo_url'
												, 'product.global_transaction_fee_used', 'product.site_transaction_fee_type', 'product.site_transaction_fee'
												, 'product.site_transaction_fee_percent', 'product.is_downloadable_product', 'product.user_section_id'
												, 'product.delivery_days', 'product.date_expires', 'product.default_orig_img_width'
												, 'product.default_orig_img_height', 'product.product_status'
												, DB::raw('IF( ( DATE( NOW() ) BETWEEN product.product_discount_fromdate AND product.product_discount_todate), 1, 0 ) AS have_discount'));
		$product_details = $product_details->join('product_category', 'product.product_category_id', '=', 'product_category.id');
		$product_details = $product_details->join('shop_details', 'product.product_user_id', '=', 'shop_details.user_id');

		if($user_id > 0) {
			$product_details = $product_details->whereRaw('product.product_user_id = ?', array($user_id));
		}
		if($this->filter_product_status != '') {
			$product_details = $product_details->whereRaw('product.product_status = ?', array($this->filter_product_status));
		}
		if($this->filter_section_id != '') {
			$product_details = $product_details->join('user_product_section', 'user_product_section.id', '=', 'product.user_section_id')->whereRaw("( user_product_section.id = ".$this->filter_section_id." )");
		}
		if($this->filter_product_code != '') {
			$product_details = $product_details->whereRaw('product.product_code = ?', array($this->filter_product_code));
		}
		if($this->filter_product_name != '') {
			$product_details = $product_details->where('product.product_name', 'LIKE', '%'.addslashes($this->filter_product_name).'%');
		}
		if($this->filter_product_category != '') {
			$cat_id_arr = $this->filter_product_category;
			if(!is_array($this->filter_product_category))
			{
				$cat_id_arr = $this->getSubCategoryIds($this->filter_product_category);
			}
			$product_details = $product_details->whereIn('product.product_category_id', $cat_id_arr);
		}

		if($this->filter_product_id_from != '') {
			$product_details = $product_details->where('product.id', '>=', $this->filter_product_id_from);
		}

		if($this->filter_product_id_to != '') {
			$product_details = $product_details->where('product.id', '<=', $this->filter_product_id_to);
		}

		if($this->filter_featured_product != '') {
			$product_details = $product_details->where('product.is_featured_product', '=', $this->filter_featured_product);
		}

		if($this->filter_product_from_price != '' OR $this->filter_product_to_price != '') {
			$start_price = $this->filter_product_from_price;
			$end_price = $this->filter_product_to_price;

			$condn_to_check_discount = '((DATE(NOW()) BETWEEN product.product_discount_fromdate AND product.product_discount_todate) AND product.product_discount_price)';
			if($start_price != '' AND $end_price != '')
			{
				$product_details = $product_details->whereRaw(DB::raw('(IF('.$condn_to_check_discount.','.
										'(product.product_discount_price_usd  BETWEEN '.$start_price.' AND '.$end_price.'),'.
										'(product.product_price_usd BETWEEN '.$start_price.' AND '.$end_price.')))'.
										' AND product.is_free_product = \'No\''));
			}
			elseif($start_price AND !$end_price)
			{
				$product_details = $product_details->whereRaw(DB::raw('(IF('.$condn_to_check_discount.','.
										'(product.product_discount_price_usd >= '.$start_price.'),'.
										'(product.product_price_usd >= '.$start_price.')))'.
										' AND product.is_free_product = \'No\''));
			}
			elseif(!$start_price AND $end_price)
			{
				$product_details = $product_details->whereRaw(DB::raw('(IF('.$condn_to_check_discount.','.
										'(product.product_discount_price_usd <= '.$end_price.'),'.
										'(product.product_price_usd <= '.$end_price.')))'.
										' AND product.is_free_product = \'No\''));
			}
		}

		if($this->filter_keyword != '') {
			if(is_array($this->filter_keyword))
			{
				$tags_condition = '';
				$tagsearch_list = $this->filter_keyword;

				if(!empty($tagsearch_list) and COUNT($tagsearch_list) > 0)
				{
					foreach($tagsearch_list as $tag_key => $tag_val)
					{
						if($tags_condition != "") {
							$tags_condition .= " OR ";
						}

						$tags_condition .= "((product.product_tags LIKE '%".addslashes($tag_val)."%') OR (product.product_name LIKE '%".addslashes($tag_val)."%')
											OR (product.product_description LIKE '%".addslashes($tag_val)."%') )";
					}
					if($tags_condition != '') {
						$product_details = $product_details->whereRaw(\DB::raw("(".$tags_condition.")"));
					}
				}
			}
		}
		$this->order_by_field = '';
		if($this->order_by != '') {
			if($this->order_by == 'id')	{
				$this->order_by_field = 'date_activated';
			}
			else if($this->order_by == 'product_sold') {
				$product_details = $product_details->whereRaw("(product.is_free_product = 'No' AND product.product_price_usd != 0 AND  product.product_price_usd != '' ) AND (product.product_sold > 0)");
			}
			else if($this->order_by == 'featured') {
				$this->order_by_field = 'date_activated';
				$product_details = $product_details->Where('product.is_featured_product', '=', 'Yes');
			}
			else if($this->order_by == 'is_free_product') {
				$product_details = $product_details->whereRaw(" ( product.is_free_product = 'Yes' OR  (product.product_price_usd = 0 OR product.product_price_usd = '' ) )");
			}
		}

		$product_details = $product_details->groupBy('product.id');
		if($this->order_by_field != '')	{
			$product_details = $product_details->orderBy($this->order_by_field, 'DESC');
		}
		if($this->products_per_page != '' && $this->products_per_page > 0)
			$product_details = $product_details->paginate($this->products_per_page);
		else
			$product_details = $product_details->get();

		/*if(count($product_details) > 0) {
			foreach($product_details as $key => $vlaues) {
				$product_details_arr[$key]['id'] = $vlaues->id;
				$product_details_arr[$key]['product_code'] = $vlaues->product_code;
				$product_details_arr[$key]['product_name'] = $vlaues->product_name;
				$product_details_arr[$key]['product_description'] = $vlaues->product_description;
				$product_details_arr[$key]['product_support_content'] = $vlaues->product_support_content;
				$product_details_arr[$key]['meta_title'] = $vlaues->meta_title;
				$product_details_arr[$key]['meta_keyword'] = $vlaues->meta_keyword;
				$product_details_arr[$key]['meta_description'] = $vlaues->meta_description;
				$product_details_arr[$key]['product_highlight_text'] = $vlaues->product_highlight_text;
				$product_details_arr[$key]['product_slogan'] = $vlaues->product_slogan;
				$product_details_arr[$key]['product_price'] = $vlaues->product_price;
				$product_details_arr[$key]['product_price_usd'] = $vlaues->product_price_usd;
				$product_details_arr[$key]['product_price_currency'] = $vlaues->product_price_currency;
				$product_details_arr[$key]['product_user_id'] = $vlaues->product_user_id;
				$product_details_arr[$key]['product_sold'] = $vlaues->product_sold;
				$product_details_arr[$key]['product_added_date'] = $vlaues->product_added_date;
				$product_details_arr[$key]['url_slug'] = $vlaues->url_slug;
				$product_details_arr[$key]['demo_url'] = $vlaues->demo_url;
				$product_details_arr[$key]['demo_details'] = $vlaues->demo_details;
				$product_details_arr[$key]['product_category_id'] = $vlaues->product_category_id;
				$product_details_arr[$key]['product_tags'] = $vlaues->product_tags;
				$product_details_arr[$key]['total_views'] = $vlaues->total_views;
				$product_details_arr[$key]['is_featured_product'] = $vlaues->is_featured_product;
				$product_details_arr[$key]['is_user_featured_product'] = $vlaues->is_user_featured_product;
				$product_details_arr[$key]['date_activated'] = $vlaues->date_activated;
				$product_details_arr[$key]['product_discount_price'] = $vlaues->product_discount_price;
				$product_details_arr[$key]['product_discount_price_usd'] = $vlaues->product_discount_price_usd;
				$product_details_arr[$key]['product_discount_fromdate'] = $vlaues->product_discount_fromdate;
				$product_details_arr[$key]['product_discount_todate'] = $vlaues->product_discount_todate;
				$product_details_arr[$key]['product_preview_type'] = $vlaues->product_preview_type;
				$product_details_arr[$key]['is_free_product'] = $vlaues->is_free_product;
				$product_details_arr[$key]['last_updated_date'] = $vlaues->last_updated_date;
				$product_details_arr[$key]['total_downloads'] = $vlaues->total_downloads;
				$product_details_arr[$key]['product_moreinfo_url'] = $vlaues->product_moreinfo_url;
				$product_details_arr[$key]['global_transaction_fee_used'] = $vlaues->global_transaction_fee_used;
				$product_details_arr[$key]['site_transaction_fee_type'] = $vlaues->site_transaction_fee_type;
				$product_details_arr[$key]['site_transaction_fee'] = $vlaues->site_transaction_fee;
				$product_details_arr[$key]['site_transaction_fee_percent'] = $vlaues->site_transaction_fee_percent;
				$product_details_arr[$key]['is_downloadable_product'] = $vlaues->is_downloadable_product;
				$product_details_arr[$key]['user_section_id'] = $vlaues->user_section_id;
				$product_details_arr[$key]['delivery_days'] = $vlaues->delivery_days;
				$product_details_arr[$key]['date_expires'] = $vlaues->date_expires;
				$product_details_arr[$key]['default_orig_img_width'] = $vlaues->default_orig_img_width;
				$product_details_arr[$key]['default_orig_img_height'] = $vlaues->default_orig_img_height;
				$product_details_arr[$key]['product_status'] = $vlaues->product_status;
			}
		}*/
		//echo '<pre>';print_r($product_details_arr);die;
		return $product_details;
	}

	public function getSubCategoryIds($category_id)
	{
		$sub_category_ids_arr = array(0);
		$sub_cat_details = DB::select('select node.id AS sub_category_id from product_category node, product_category parent where
				node.category_left BETWEEN parent.category_left AND parent.category_right AND parent.id = ? ORDER BY node.category_left', array($category_id));

		if(count($sub_cat_details) > 0)
		{
			$sub_category_ids_arr = array();
			foreach($sub_cat_details as $sub_cat)
			{
				$sub_category_ids_arr[] = $sub_cat->sub_category_id;
			}
		}
		return $sub_category_ids_arr;
	}

	public function getProductImage($product_id)
	{
		$p_id = 0;
		if($product_id != '' && $product_id > 0)
		{
			$p_id = $product_id;
		}
		$product_img_arr = array();
		$product_img = ProductImage::where('product_id', '=', $p_id)
										->get();
		if(count($product_img) > 0) {
			foreach($product_img as $key => $vlaues) {
				$product_img_arr['id'] = $vlaues->id;
				$product_img_arr['product_id'] = $vlaues->product_id;
				$product_img_arr['thumbnail_title'] = $vlaues->thumbnail_title;
				$product_img_arr['thumbnail_img'] = $vlaues->thumbnail_img;
				$product_img_arr['thumbnail_ext'] = $vlaues->	thumbnail_ext;
				$product_img_arr['thumbnail_width'] = $vlaues->thumbnail_width;
				$product_img_arr['thumbnail_height'] = $vlaues->thumbnail_height;
				$product_img_arr['default_title'] = $vlaues->default_title;
				$product_img_arr['default_img'] = $vlaues->default_img;
				$product_img_arr['default_ext'] = $vlaues->default_ext;
				$product_img_arr['default_width'] = $vlaues->default_width;
				$product_img_arr['default_height'] = $vlaues->default_height;
				$product_img_arr['default_orig_img_width'] = $vlaues->default_orig_img_width;
				$product_img_arr['default_orig_img_height'] = $vlaues->default_orig_img_height;
			}
		}
		return $product_img_arr;
	}


	public function getShopProductSectionDetails($owner_id)
	{
		$section_details_arr = array();
		$section_details = UserProductSection::whereRaw('user_product_section.user_id =  ? AND prd.product_user_id = ? AND prd.product_status = \'Ok\'', array($owner_id, $owner_id))
							->join('product AS prd', 'prd.user_section_id', '=', 'user_product_section.id')
							->get(array('user_product_section.id', 'user_product_section.section_name', 'user_product_section.status'
									, 'user_product_section.date_added', DB::raw('COUNT(prd.user_section_id) AS section_count')));
		if(count($section_details) > 0) {
			foreach($section_details as $key => $vlaues) {
				$section_details_arr['id'] = $vlaues->id;
				$section_details_arr['section_name'] = $vlaues->section_name;
				$section_details_arr['status'] = $vlaues->status;
				$section_details_arr['date_added'] = $vlaues->date_added;
				$section_details_arr['section_count'] = $vlaues->section_count;
			}
		}
		return $section_details;
	}

	public function getProductDetails($logged_user_id = 0)
	{
		$product_arr = array();
		if((is_numeric($this->product_id) && $this->product_id > 0 ) || $this->filter_product_code != '')
		{
			$product_details = Product::Select('product.id', 'product.product_code', 'product.product_name', 'product.product_description'
												, 'product.product_support_content', 'product.meta_title', 'product.meta_keyword'
												, 'product.meta_description', 'product.product_highlight_text', 'product.product_slogan'
												, 'product.product_price', 'product.product_price_usd', 'product.product_price_currency'
												, 'product.product_user_id', 'product.product_sold', 'product.product_added_date'
												, 'product.url_slug', 'product.demo_url', 'product.demo_details', 'product.product_category_id'
												, 'product.product_tags', 'product.total_views', 'product.is_featured_product'
												, 'product.is_user_featured_product', 'product.date_activated', 'product.product_discount_price'
												, 'product.product_discount_price_usd', 'product.product_discount_fromdate'
												, 'product.product_discount_todate', 'product.product_preview_type', 'product.is_free_product'
												, 'product.last_updated_date', 'product.total_downloads', 'product.product_moreinfo_url'
												, 'product.global_transaction_fee_used', 'product.site_transaction_fee_type', 'product.site_transaction_fee'
												, 'product.site_transaction_fee_percent', 'product.is_downloadable_product', 'product.user_section_id'
												, 'product.delivery_days', 'product.date_expires', 'product.default_orig_img_width'
												, 'product.default_orig_img_height', 'product.product_status'
												, DB::raw('IF( ( DATE( NOW() ) BETWEEN product.product_discount_fromdate AND product.product_discount_todate), 1, 0 ) AS have_discount'));

			if($this->filter_product_code != '') {
				$product_details = $product_details->whereRaw('product_code = ?', array($this->filter_product_code));
			}
			else {
				$product_details = $product_details->whereRaw('id = ?', array($this->product_id));
			}
			if($this->filter_product_status != '') {
				$product_details = $product_details->whereRaw('product_status = ?', array($this->filter_product_status));
			}
			if($logged_user_id > 0)
			{
				$product_details = $product_details->whereRaw('product_user_id = ?', array($logged_user_id));
			}
			$product_details = $product_details->first();
			if(count($product_details) > 0)
			{
				$product_arr = $product_details->toArray();
				return $product_arr;
			}
			throw new ProductNotFoundException('Invalid Product Id');
		}
		throw new InvalidProductIdException('Invalid Product Id');
	}

	public function removeProductCategoryAttribute()
	{
		//To delete product attributes values
		ProductAttributesValues::whereRaw("product_id = ?", array($this->product_id))->delete();
		//To delete product attributes options values
		ProductAttributesOptionValues::whereRaw("product_id = ?", array($this->product_id))->delete();
	}

	public function addProductComment($user_id, $notes, $added_by)
	{
		$data_arr = array('user_id' => $user_id,
                          'product_id' => $this->product_id,
                          'added_by' => $added_by,
                          'notes' => $notes,
                          'date_added' => DB::raw('NOW()'));
		ProductLog::insertGetId($data_arr);
	}

	public function saveProductImageTitle($type, $title)
	{
		if (strcmp($type, 'thumb') == 0)
		{
			ProductImage::whereRaw('product_id = ?', array($this->product_id))->update(array('thumbnail_title' => $title));
		}
		else
		{
			ProductImage::whereRaw('product_id = ?', array($this->product_id))->update(array('default_title' => $title));
		}
        return true;
	}

	public function removeProductThumbImage()
	{
        $data_arr = array('thumbnail_img' => '' ,
		 		  		'thumbnail_ext' => '' ,
		 		  		'thumbnail_width' => 0,
		 		  		'thumbnail_height' => 0,
						'thumbnail_title' => '' );
		ProductImage::whereRaw('product_id = ?', array($this->product_id))->update($data_arr);
        return true;
	}

	public function removeProductDefaultImage()
	{
        $data_arr = array('default_img' => '' ,
		 		  		 'default_ext' => '' ,
		 		  		 'default_width' => 0,
		 		  		 'default_height' => 0,
		 		  		 'default_orig_img_width' => 0,
		 		  		 'default_orig_img_height' => 0,
						 'default_title' => '' );
		ProductImage::whereRaw('product_id = ?', array($this->product_id))->update($data_arr);
        return true;
    }

    public function getAttributesList($category_id)
	{
		$data_arr = array();
		if(is_numeric($category_id) && $category_id > 0)
		{
			//get all the category_id up in tree and the corresponding attribute ids..
			$category_ids = Products::getTopLevelCategoryIds($category_id);

			$q = ' SELECT MCA.attribute_id, attribute_question_type, validation_rules, default_value, MA.status , attribute_label ' .
				 ' FROM product_attributes AS MA LEFT JOIN ' .
				 ' product_category_attributes AS MCA ON MA.id = MCA.attribute_id '.
				 ' WHERE MCA.category_id IN ('.$category_ids.') '.
				 ' ORDER BY display_order, MA.id';
			$recs_arr = DB::select($q);
			foreach($recs_arr AS $key => $val)
			{
				$dafault_value =  $val->default_value;
				//If product is avalilable, set the form field values by user entered data
				if($this->product_id != '' && $this->product_id > 0)
				{
					$dafault_value = $this->getAttributeValue($this->product_id, $val->attribute_id, $val->attribute_question_type, $dafault_value);
				}

				$data_arr[$val->attribute_id] = array('attribute_id' => $val->attribute_id,
													  'attribute_question_type' => $val->attribute_question_type,
													  'validation_rules' => $val->validation_rules,
													  'default_value' => $dafault_value,
													  'status' => $val->status,
													  'attribute_label' => $val->attribute_label
												);
			}
		}
		return $data_arr;
	}

	public function getAttributeValue($p_id, $attr_id, $attr_type, $dafault_value)
	{
		switch($attr_type)
		{
			case 'text':
			case 'textarea':
				$count = ProductAttributesValues::where('attribute_id', '=', $attr_id)->where('product_id', '=', $p_id)->count();
				if($count > 0)
				{
					return ProductAttributesValues::where('attribute_id', '=', $attr_id)->where('product_id', '=', $p_id)->pluck('attribute_value');
				}
				break;

			case 'select':
			case 'option': // radio button
			case 'multiselectlist':
			case 'check': // checkbox
				$count = ProductAttributesOptionValues::where('attribute_id', '=', $attr_id)->where('product_id', '=', $p_id)->count();
				if($count > 0)
				{
					$rtn_arr = array();
					$t_arr = ProductAttributesOptionValues::where('attribute_id', '=', $attr_id)->where('product_id', '=', $p_id)->get(array('attribute_options_id'))
								->toArray();
					foreach($t_arr AS $arr)
					{
						$rtn_arr[] = $arr['attribute_options_id'];
					}
					return $rtn_arr;
				}
				break;
		}
		return $dafault_value;
	}

	public function getProductNotes()
	{
		$p_id = 0;
		if($this->product_id != '' && $this->product_id > 0)
		{
			$p_id = $this->product_id;
		}
		return ProductLog::whereRaw('product_id = ?', array($p_id))->orderBy('id', 'DESC')->get();
	}

	public function populateProductResources($resource_type = '', $is_downloadable = 'No', $product_id = 0)
	{
		$resources_arr = array();

		$p_id = 0;
		if($this->product_id != '' && $this->product_id > 0)
		{
			$p_id = $this->product_id;
		}
		else if($product_id > 0){
			$p_id = $product_id;
		}
		if($p_id == 0)
		{
			return $resources_arr;
		}

		$d_arr = ProductResource::where('product_id', '=', $p_id)->where('resource_type', '=', $resource_type)->where('is_downloadable', '=', $is_downloadable)
				 ->orderBy('display_order', 'ASC')
				 ->get(array('id', 'resource_type', 'filename', 'ext', 'title', 'is_downloadable', 'width', 'height', 't_width', 't_height', 'l_width', 'l_height'))
				 ->toArray();
		if(count($d_arr) > 0)
		{
			foreach($d_arr AS $data)
			{
				$resources_arr[] = array(
					'resource_id' => $data['id'],
					'resource_type' => $data['resource_type'],
					'filename_thumb' => $data['filename'] . 'T.' . $data['ext'],
					'filename_large' => $data['filename'] . 'L.' . $data['ext'],
					'filename_original' => $data['filename'] . '.' . $data['ext'],
					'width' => $data['width'],
					'height' => $data['height'],
					't_width' => $data['t_width'],
					't_height' => $data['t_height'],
					'l_width' => $data['l_width'],
					'l_height' => $data['l_height'],
					'ext' => $data['ext'],
					'title' => $data['title'],
					'is_downloadable' => $data['is_downloadable']
				);
			}
		}
		return $resources_arr;
	}

	public function updateProductResourceTitle($resource_id, $title)
	{
		ProductResource::whereRaw('id = ?', array($resource_id))->update(array('title' => $title));
	    return true;
	}

	public function getProductResource($row_id)
	{
		$data_arr = ProductResource::where('id', '=', $row_id)->get(array('filename', 'resource_type', 'ext'))->toArray();
		return $data_arr;
	}

	public function deleteProductResource($row_id)
	{
		ProductResource::where('id', '=', $row_id)->delete();
	}

	public function updateProductResourceDisplayOrder($resource_id, $display_order)
	{
		ProductResource::whereRaw('id = ?', array($resource_id))->update(array('display_order' => $display_order));
	}

	public function getDownloadProductDetails()
	{
		$download_arr = DB::select('SELECT filename, ext, resource_type, title, product_user_id, is_free_product FROM product_resource AS PR, product AS P WHERE PR.product_id = '.$product_id.' AND PR.product_id = P.id AND is_downloadable = "Yes"');
		return $download_arr;
	}

	public function setProductPreviewType()
	{
		$product_preview_type = '';
		if($this->product_id != '' && $this->product_id > 0)
		{
			$product_preview_type = Product::where('id', '=', $this->product_id)->pluck('product_preview_type');
		}
		return $product_preview_type;
	}

	public function getProductResourceCount($resource_type)
	{
		$count = 0;
		if($this->product_id != '' && $this->product_id > 0)
		{
			$count = ProductResource::whereRaw('product_id = ? AND resource_type = ? ', array($this->product_id, $resource_type))->count();
		}
		return $count;
	}

	public function getUserLastProductNote($user_id)
	{
		return ProductLog::whereRaw('product_id = ? AND user_id = ?', array($this->product_id, $user_id))->orderBy('id', 'DESC')->pluck('notes');
	}

	public function getCategoryArr($category_id)
	{
		$cat_details = DB::select('SELECT parent.category_name, parent.id, parent.seo_category_name FROM product_category AS node, product_category AS parent WHERE node.category_left BETWEEN parent.category_left AND parent.category_right AND node.id = ? ORDER BY node.category_left;', array($category_id));
		//$cat_details = DB::select('select parent.category_name, parent.id, parent.seo_category_name FROM product_category AS node, product_category AS parent WHERE node.category_left BETWEEN parent.category_left AND parent.category_right AND node.id = ? ORDER BY parent.category_left', array($category_id));
		return $cat_details;
	}

	public function addProductViewCount($product_id)
	{
		//To increment the view count.
		Product::where('id', '=', $product_id)->increment('total_views');
	}

	public function updateLastUpdatedDate()
	{
	 	if(is_numeric($this->product_id) && $this->product_id > 0)
	 	{
			Product::whereRaw('id = ?', array($this->product_id))->update(array('last_updated_date' => DB::raw('NOW()')));
		}
	}

	public function getProductCountForCategory($category_id)
	{
		$this->sub_category_ids = $this->getSubCategoryIds($category_id);
		if(count($sub_category_ids) == 0)
			$this->sub_category_ids = $category_id;

		$product_count = Product::whereIn('product_category_id', $this->sub_category_ids)->count();
	    return $product_count;
	}

	public function isCategoryExists($category_id)
	{
		$cat_details = Products::getCategoryDetails($category_id);
		$category_count = count($cat_details);
	    return $category_count;
	}

	public function isCategoryProductExists($category_id)
	{
		$product = Products::initialize();
		$product_count = $product->getProductCountForCategory($category_id);
		return $product_count;
	}

	public function deleteCategory($category_id)
	{
		// check category exist or not
		if(!$this->isCategoryExists($category_id))
		{
			return json_encode(array('status' => 'error', 'error_msg' => 'Category not found!'));
		}

		// check products added for the selected category or its subcategories
		if($this->isCategoryProductExists($category_id))
		{
			return json_encode(array('status' => 'error', 'error_msg' => 'The category or sub categories are in use, so category cannot be deleted!'));
		}

		// delete category details in all assigned attributes & category image.
		$cat_details = ProductCategory::whereIn('id', $this->sub_category_ids)->get(array('id'));
		if(count($cat_details) > 0)
		{
			foreach($cat_details as $cat)
			{
				// Delete all attributes assigned to the selected category & its subcategories
				ProductCategoryAttributes::whereRaw('category_id = ?', array($cat->id))->delete();
			}
		}

		//store the values of the left and right of the category to be deleted
		//delete all those cateogries b/w the above 2
		// update the cateogies to the right of the deleted category  - reduce left and right bu width of the deleted category
		$cat_info = Products::getCategoryDetails($category_id);
		if(count($cat_info) > 0)
		{
			$category_left = $cat_info['category_left'];
			$category_right = $cat_info['category_right'];
			$width = $category_right - $category_left + 1;

			ProductCategory::whereRaw(DB::raw('category_left  between  '. $category_left.' AND '.$category_right))->delete();

			//To update category left
			ProductCategory::whereRaw(DB::raw('category_left >  '.$category_right))->update(array("category_left" => DB::raw('category_left - '. $width)));

			//To update category right
			ProductCategory::whereRaw(DB::raw('category_right >  '.$category_right))->update(array("category_right" => DB::raw('category_right - '. $width)));
		}
		return json_encode(array('status' => 'success', 'category_id' => $category_id));
	}

	public function isAttributeAlreadyAssigned($attribute_id, $category_id)
	{
		$category_attr_count = ProductCategoryAttributes::whereRaw('attribute_id = ? AND category_id = ?', array($attribute_id, $category_id))->count();
		return $category_attr_count;
	}

	public function assignAttributeForCategory($attribute_id, $category_id)
	{
		$attr = new Attribute();
		if(!$this->isCategoryExists($category_id))
		{
			return json_encode(array('status' => 'error', 'error_msg' => 'Category not found!'));
		}
		else if(!$attr->isAttributeExists($attribute_id))
		{
			return json_encode(array('status' => 'error', 'error_msg' => 'Attribute not found!'));
		}
		else if($this->isAttributeAlreadyAssigned($attribute_id, $category_id))
		{
			return json_encode(array('status' => 'error', 'error_msg' => 'Attribute alredy assigned for this category!'));
		}

		$input_arr['attribute_id'] = $attribute_id;
		$input_arr['category_id'] = $category_id;
		$input_arr['date_added'] = DB::raw('NOW()');
		$cat_attribute_id = ProductCategoryAttributes::insertGetId($input_arr);
		return json_encode(array('status' => 'success'));
	}

	public function removeAssignedAttributeForCategory($category_id, $attribute_id)
	{
		$attr = new Attribute();
		if(!$this->isCategoryExists($category_id))
		{
			return json_encode(array('status' => 'error', 'error_msg' => 'Category not found!'));
		}
		else if(!$attr->isAttributeExists($attribute_id))
		{
			return json_encode(array('status' => 'error', 'error_msg' => 'Attribute not found!'));
		}
		$affectedRows = ProductCategoryAttributes::whereRaw('attribute_id = ? AND category_id = ?', array($attribute_id, $category_id))->delete();
		if($affectedRows)
		{
			return json_encode(array('status' => 'success'));
		}
		else
		{
			return json_encode(array('status' => 'error', 'error_msg' => 'Error in removing attribute!'));
		}
	}
}
<?php namespace Agriya\Products;

use Validator;
use Response;
class Shops {

	protected $shop_id;

	protected $fields_arr = array();

	protected $detail_fields_arr = array();

	protected $filter_shop_id = '';

	protected $filter_shop_owner_id = '';

	protected $filter_shop_name = '';

	protected $filter_url_slug = '';

	protected $filter_shop_status = '';

	protected $filter_is_featured_shop = '';

	protected $shops_per_page = '';

	public function __construct()
	{
		//$this->shop_id = $shop_id;
		//$this->shopservice = new ShopService;
	}

	public function getShopId()
	{
		return $this->shop_id;
	}

	public function setShopId($val)
	{
		$this->fields_arr['id'] = $val;
	}

	public function setShopOwnerId($val)
	{
		$this->fields_arr['user_id'] = $val;
		$this->detail_fields_arr['user_id'] = $val;
	}

	public function setShopName($val)
	{
		$this->fields_arr['shop_name'] = $val;
	}

	public function setShopUrlSlug($val)
	{
		$this->fields_arr['url_slug'] = $val;
	}

	public function setShopSlogan($val)
	{
		$this->fields_arr['shop_slogan'] = $val;
	}

	public function setShopDescription($val)
	{
		$this->fields_arr['shop_desc'] = $val;
	}

	public function setShopAddress1($val)
	{
		$this->fields_arr['shop_address1'] = $val;
	}

	public function setShopAddress2($val)
	{
		$this->fields_arr['shop_address2'] = $val;
	}

	public function setShopCity($val)
	{
		$this->fields_arr['shop_city'] = $val;
	}

	public function setShopState($val)
	{
		$this->fields_arr['shop_state'] = $val;
	}

	public function setShopZipcode($val)
	{
		$this->fields_arr['shop_zipcode'] = $val;
	}

	public function setShopCountry($val)
	{
		$this->fields_arr['shop_country'] = $val;
	}

	public function setShopMessage($val)
	{
		$this->fields_arr['shop_message'] = $val;
	}

	public function setShopContactInfo($val)
	{
		$this->fields_arr['shop_contactinfo'] = $val;
	}

	public function setShopImageName($val)
	{
		$this->fields_arr['image_name'] = $val;
	}

	public function setShopImageExtension($val)
	{
		$this->fields_arr['image_ext'] = $val;
	}

	public function setShopImageServerUrl($val)
	{
		$this->fields_arr['image_server_url'] = $val;
	}

	public function setShopImageHeight($val)
	{
		$this->fields_arr['t_height'] = $val;
	}

	public function setShopImageWidth($val)
	{
		$this->fields_arr['t_width'] = $val;
	}

	public function setIsFeaturedShop($val)
	{
		$this->fields_arr['is_featured_shop'] = $val;
	}

	public function setIsShopOwner($val)
	{
		$this->detail_fields_arr['is_shop_owner'] = $val;
	}

	public function setShopStatus($val)
	{
		$this->detail_fields_arr['shop_status'] = $val;
	}

	public function setTotalProducts($val)
	{
		$this->detail_fields_arr['total_products'] = $val;
	}

	public function setPaypalEmailId($val)
	{
		$this->detail_fields_arr['paypal_id'] = $val;
	}

	//Filters
	public function setFilterShopId($val)
	{
		$this->filter_shop_id = $val;
	}

	public function setFilterShopOwnerId($val)
	{
		$this->filter_shop_owner_id = $val;
	}

	public function setFilterShopName($val)
	{
		$this->filter_shop_name = $val;
	}

	public function setFilterShopUrlSlug($val)
	{
		$this->filter_url_slug = $val;
	}

	public function setFilterShopStatus($val)
	{
		$this->filter_shop_status = $val;
	}

	public function setFilterIsFeaturedShop($val)
	{
		$this->filter_is_featured_shop = $val;
	}

	public function setShopPagination($val)
	{
		$this->shops_per_page = $val;
	}

	public function save()
	{
		//Validation start
		$rules = $message = array();
		$rules += array(
				'shop_name' => 'Required|Min:'.\Config::get('webshoppack::shopname_min_length').'|Max:'.\Config::get('webshoppack::shopname_max_length').'|unique:shop_details,shop_name,'.$this->fields_arr['user_id'].',user_id',
				'url_slug' => 'Required|unique:shop_details,url_slug,'.$this->fields_arr['user_id'].',user_id',
				'shop_slogan' => 'Min:'.\Config::get('webshoppack::shopslogan_min_length').'|Max:'.\Config::get('webshoppack::shopslogan_max_length'),
				'shop_desc' => 'Min:'.\Config::get('webshoppack::fieldlength_shop_description_min').'|Max:'.\Config::get('webshoppack::fieldlength_shop_description_max'),
				'shop_contactinfo' => 'Min:'.\Config::get('webshoppack::fieldlength_shop_contactinfo_min').'|Max:'.\Config::get('webshoppack::fieldlength_shop_contactinfo_max'),
		);
		$message = array('shop_name.unique' => trans('webshoppack::shopDetails.shopname_already_exists'),
						'url_slug.unique' => trans('webshoppack::shopDetails.shopurlslug_already_exists'),
						);

		$validator = Validator::make($this->fields_arr, $rules, $message);

		if($this->fields_arr['id'] == '')
		{
			if ($validator->fails()) {
				$errors = $validator->errors()->all();
				return Response::json(array('error' => $errors));
			}
			//Validation End
			$shop_id = ShopDetails::insertGetId($this->fields_arr);

			$shop_details_arr = array('user_id' => $this->fields_arr['user_id']
											, 'is_shop_owner' => 'Yes'
											, 'shop_status' => 1
											, 'total_products' => 0);
			$shop_detail_id = UsersShopDetails::insertGetId($shop_details_arr);
		}
		else {
			if ($validator->fails()) {
				$errors = $validator->errors()->all();
				return json_encode(array('status' => 'error', 'error_messages' => $errors));
			}
			//Validation End
			ShopDetails::whereRaw('id = ?', array($this->fields_arr['id']))->update($this->fields_arr);
			return json_encode(array('status' => 'success'));
		}
	}

	/**
	 * Getting shop details
	 *
	 * @author 		manikandan_133at10
	 * @return 		array
	 * @access 		public
	 */
	public function saveUsersShopDetails()
	{
		if($this->detail_fields_arr['user_id'] > 0) {
			$rules = $message = array();
			$rules += array(
				'paypal_id' => 'Required|email'
			);
			$validator = Validator::make($this->detail_fields_arr, $rules, $message);
			if ($validator->fails()) {
				$errors = $validator->errors()->all();
				return json_encode(array('status' => 'error', 'error_messages' => $errors));
			}
			else {
				//Check shop details record exists for the given shop owner
				$shop_rec_count = ShopDetails::whereRaw('user_id = ?', array($this->fields_arr['user_id']))->count();
				if($shop_rec_count == 0) {
					$shop_arr = array('user_id' => $this->fields_arr['user_id']);
					$shop_id = ShopDetails::insertGetId($shop_arr);
				}

				$rec_count = UsersShopDetails::whereRaw('user_id = ?', array($this->fields_arr['user_id']))->count();
				if($rec_count > 0) {
					UsersShopDetails::whereRaw('user_id = ?', array($this->detail_fields_arr['user_id']))->update($this->detail_fields_arr);
					return json_encode(array('status' => 'success'));
				}
				else {
					UsersShopDetails::insertGetId($this->detail_fields_arr);
					return json_encode(array('status' => 'success'));
				}
			}
		}
	}

	/**
	 * Getting shop details
	 *
	 * @author 		manikandan_133at10
	 * @return 		array
	 * @access 		public
	 */
	public function getShopDetails($user_id)
	{
		$shop_details_arr = array();
		$shop_details = ShopDetails::Select('id', 'user_id', 'shop_name', 'url_slug', 'shop_slogan', 'shop_desc'
												, 'shop_address1', 'shop_address2', 'shop_city', 'shop_state'
												, 'shop_zipcode', 'shop_country', 'shop_message', 'shop_contactinfo'
												, 'image_name', 'image_ext', 'image_server_url', 't_height', 't_width')
									->where('user_id', $user_id)
									->get();
		if(count($shop_details) > 0) {
			foreach($shop_details as $key => $vlaues) {
				$shop_details_arr['id'] = $vlaues->id;
				$shop_details_arr['user_id'] = $vlaues->user_id;
				$shop_details_arr['shop_name'] = $vlaues->shop_name;
				$shop_details_arr['url_slug'] = $vlaues->url_slug;
				$shop_details_arr['shop_slogan'] = $vlaues->shop_slogan;
				$shop_details_arr['shop_desc'] = $vlaues->shop_desc;
				$shop_details_arr['shop_address1'] = $vlaues->shop_address1;
				$shop_details_arr['shop_address2'] = $vlaues->shop_address2;
				$shop_details_arr['shop_city'] = $vlaues->shop_city;
				$shop_details_arr['shop_state'] = $vlaues->shop_state;
				$shop_details_arr['shop_zipcode'] = $vlaues->shop_zipcode;
				$shop_details_arr['shop_country'] = $vlaues->shop_country;
				$shop_details_arr['shop_message'] = $vlaues->shop_message;
				$shop_details_arr['shop_contactinfo'] = $vlaues->shop_contactinfo;
				$shop_details_arr['image_name'] = $vlaues->image_name;
				$shop_details_arr['image_ext'] = $vlaues->image_ext;
				$shop_details_arr['image_server_url'] = $vlaues->image_server_url;
				$shop_details_arr['t_height'] = $vlaues->t_height;
				$shop_details_arr['t_width'] = $vlaues->t_width;
			}
		}
		return $shop_details_arr;
	}

	/**
	 * Getting shop details
	 *
	 * @author 		manikandan_133at10
	 * @return 		array
	 * @access 		public
	 */
	public function getShopDetailsWithFilter()
	{
		$shop_details_arr = array();
		$shop_details = ShopDetails::Select('id', 'user_id', 'shop_name', 'url_slug', 'shop_slogan', 'shop_desc'
												, 'shop_address1', 'shop_address2', 'shop_city', 'shop_state'
												, 'shop_zipcode', 'shop_country', 'shop_message', 'shop_contactinfo'
												, 'image_name', 'image_ext', 'image_server_url', 't_height', 't_width'
												, 'is_featured_shop');
		if($this->filter_shop_id != '')
			$shop_details = $shop_details->whereRaw('id = ?', array($this->filter_shop_id));
		if($this->filter_shop_owner_id != '')
			$shop_details = $shop_details->whereRaw('user_id = ?', array($this->filter_shop_owner_id));
		if($this->filter_shop_name != '')
			$shop_details = $shop_details->whereRaw('shop_name = ?', array($this->filter_shop_name));
		if($this->filter_url_slug != '')
			$shop_details = $shop_details->whereRaw('url_slug = ?', array($this->filter_url_slug));

		$shop_details = $shop_details->get();

		if(count($shop_details) > 0) {
			foreach($shop_details as $key => $vlaues) {
				$shop_details_arr['id'] = $vlaues->id;
				$shop_details_arr['user_id'] = $vlaues->user_id;
				$shop_details_arr['shop_name'] = $vlaues->shop_name;
				$shop_details_arr['url_slug'] = $vlaues->url_slug;
				$shop_details_arr['shop_slogan'] = $vlaues->shop_slogan;
				$shop_details_arr['shop_desc'] = $vlaues->shop_desc;
				$shop_details_arr['shop_address1'] = $vlaues->shop_address1;
				$shop_details_arr['shop_address2'] = $vlaues->shop_address2;
				$shop_details_arr['shop_city'] = $vlaues->shop_city;
				$shop_details_arr['shop_state'] = $vlaues->shop_state;
				$shop_details_arr['shop_zipcode'] = $vlaues->shop_zipcode;
				$shop_details_arr['shop_country'] = $vlaues->shop_country;
				$shop_details_arr['shop_message'] = $vlaues->shop_message;
				$shop_details_arr['shop_contactinfo'] = $vlaues->shop_contactinfo;
				$shop_details_arr['image_name'] = $vlaues->image_name;
				$shop_details_arr['image_ext'] = $vlaues->image_ext;
				$shop_details_arr['image_server_url'] = $vlaues->image_server_url;
				$shop_details_arr['t_height'] = $vlaues->t_height;
				$shop_details_arr['t_width'] = $vlaues->t_width;
				$shop_details_arr['is_featured_shop'] = $vlaues->is_featured_shop;
			}
		}
		return $shop_details_arr;
	}

	/**
	 * Getting shop list
	 *
	 * @author 		manikandan_133at10
	 * @return 		array
	 * @access 		public
	 */
	public function getShopList()
	{
		$shop_details_arr = array();
		$shop_details = ShopDetails::Select('id', 'user_id', 'shop_name', 'url_slug', 'shop_slogan', 'shop_desc'
												, 'shop_address1', 'shop_address2', 'shop_city', 'shop_state'
												, 'shop_zipcode', 'shop_country', 'shop_message', 'shop_contactinfo'
												, 'image_name', 'image_ext', 'image_server_url', 't_height', 't_width'
												, 'is_featured_shop');

		if($this->filter_shop_id != '')
			$shop_details = $shop_details->whereRaw('id = ?', array($this->filter_shop_id));
		if($this->filter_shop_owner_id != '')
			$shop_details = $shop_details->whereRaw('user_id = ?', array($this->filter_shop_owner_id));
		if($this->filter_shop_name != '') {
			$name_arr = explode(" ",$this->filter_shop_name);
			if(count($name_arr) > 0) {
				foreach($name_arr AS $names) {
					$shop_details = $shop_details->whereRaw("( shop_details.shop_name LIKE '%".addslashes($names)."%')");
				}
			}
		}
		if($this->filter_url_slug != '')
			$shop_details = $shop_details->whereRaw('url_slug = ?', array($this->filter_url_slug));
		if($this->filter_is_featured_shop != '')
			$shop_details = $shop_details->whereRaw('is_featured_shop = ?', array($this->filter_is_featured_shop));

		if($this->shops_per_page != '' && $this->shops_per_page > 0)
			$shop_details = $shop_details->paginate($this->shops_per_page);
		else
			$shop_details = $shop_details->get();

		/*if(count($shop_details) > 0) {
			foreach($shop_details as $key => $vlaues) {
				$shop_details_arr[$key]['id'] = $vlaues->id;
				$shop_details_arr[$key]['user_id'] = $vlaues->user_id;
				$shop_details_arr[$key]['shop_name'] = $vlaues->shop_name;
				$shop_details_arr[$key]['url_slug'] = $vlaues->url_slug;
				$shop_details_arr[$key]['shop_slogan'] = $vlaues->shop_slogan;
				$shop_details_arr[$key]['shop_desc'] = $vlaues->shop_desc;
				$shop_details_arr[$key]['shop_address1'] = $vlaues->shop_address1;
				$shop_details_arr[$key]['shop_address2'] = $vlaues->shop_address2;
				$shop_details_arr[$key]['shop_city'] = $vlaues->shop_city;
				$shop_details_arr[$key]['shop_state'] = $vlaues->shop_state;
				$shop_details_arr[$key]['shop_zipcode'] = $vlaues->shop_zipcode;
				$shop_details_arr[$key]['shop_country'] = $vlaues->shop_country;
				$shop_details_arr[$key]['shop_message'] = $vlaues->shop_message;
				$shop_details_arr[$key]['shop_contactinfo'] = $vlaues->shop_contactinfo;
				$shop_details_arr[$key]['image_name'] = $vlaues->image_name;
				$shop_details_arr[$key]['image_ext'] = $vlaues->image_ext;
				$shop_details_arr[$key]['image_server_url'] = $vlaues->image_server_url;
				$shop_details_arr[$key]['t_height'] = $vlaues->t_height;
				$shop_details_arr[$key]['t_width'] = $vlaues->t_width;
				$shop_details_arr[$key]['is_featured_shop'] = $vlaues->is_featured_shop;
			}
		}*/
		return $shop_details;
	}

	/**
	 * Getting users shop details
	 *
	 * @author 		manikandan_133at10
	 * @return 		array
	 * @access 		public
	 */
	public function getUsersShopDetails($user_id)
	{
		$shop_details_arr = array();
		$shop_details = UsersShopDetails::Select('id', 'user_id', 'is_shop_owner', 'shop_status', 'total_products', 'paypal_id')
									->where('user_id', $user_id)
									->get();
		if(count($shop_details) > 0) {
			foreach($shop_details as $key => $vlaues) {
				$shop_details_arr['id'] = $vlaues->id;
				$shop_details_arr['user_id'] = $vlaues->user_id;
				$shop_details_arr['is_shop_owner'] = $vlaues->is_shop_owner;
				$shop_details_arr['shop_status'] = $vlaues->shop_status;
				$shop_details_arr['total_products'] = $vlaues->total_products;
				$shop_details_arr['paypal_id'] = $vlaues->paypal_id;
			}
		}
		return $shop_details_arr;
	}

	public function setShopFeaturedStatus($shop_id)
	{
	 	if(is_numeric($shop_id) && $shop_id > 0) {
			ShopDetails::whereRaw('id = ?', array($shop_id))->update(array('is_featured_shop' => $this->is_featured_shop));
		}
	}
}
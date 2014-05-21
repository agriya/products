<?php namespace Agriya\Products;
//Common Utils
class CUtil
{
	public static function generateRandomUniqueCode($prefix_code, $table_name, $field_name)
	{
		if($table_name == 'users')
			$unique_code = $prefix_code.mt_rand(10000000,99999999);
		else
			$unique_code = $prefix_code.mt_rand(100000,999999);
		$code_count = 	\DB::table($table_name)->whereRaw($field_name." = ? ", array($unique_code))->count();
		if($code_count > 0)
		{
			return CUtil::generateRandomUniqueCode($prefix_code, $table_name, $field_name);
		}
		else
		{
			return $unique_code;
		}
		return $unique_code;
	}

	/**
	 * CUtil::chkIsValidCurrency()
	 * added by periyasami_145at11
	 *
	 * @param mixed $currency_code
	 * @return
	 */
	public static function chkIsValidCurrency($currency_code)
	{
		$details = array();
		$selected_currency_code = CurrencyExchangeRate::whereRaw('currency_code= ? AND status = "Active" AND display_currency = "Yes" ', array($currency_code))->first();
		if(count($selected_currency_code))
		{
			$details['country'] = $selected_currency_code['country'];
			$details['currency_code'] = $selected_currency_code['currency_code'];
			$details['exchange_rate'] = $selected_currency_code['exchange_rate'];
			$details['currency_symbol'] = $selected_currency_code['currency_symbol'];
		}
		return $details;
	}

	public static function convertBaseCurrencyToUSD($amount, $base_currency = "", $exchange_rate_allow = false)
	{
		if($amount == "")
			$amount = "0";

		if(doubleval($amount) > 0)
		{
			$amt = $amount;
			if($base_currency != "USD")
			{
				$currency_details = CUtil::chkIsValidCurrency($base_currency);

				if(count($currency_details) > 0)
				{
					$exchange_rate = doubleval($currency_details['exchange_rate']);
					if($exchange_rate_allow)
					{
						$exchange_price = $exchange_rate * (doubleval(\Config::get("webshoppack::site_exchange_rate")) * 0.01);
						$exchange_rate = $exchange_rate - $exchange_price;
					}
					$amt = $amt / $exchange_rate;
				}
			}
			return $amt;
		}
		return $amount;
	}
}
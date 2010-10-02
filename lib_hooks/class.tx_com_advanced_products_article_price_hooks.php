<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c)  2006-2007 Ingo Schmitt <is@marketing-factory.de>
 *  All   rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * Part of the COMMERCE (Advanced Shopping System) extension.
 *
 * @author	  Sascha Egerer <seg@softvision.de>
 * @internal	 Maintainer Ingo Schmitt
 * @package	 TYPO3
 * @subpackage	 tx_commerce
 *
 * Implementation of an upcounting Odernumber
 */


class tx_com_advanced_products_article_price_hooks {

	/**
	 * uid from actual article price
	 * @access private
	 */
	var $price_uid;

	/**
	 * if the price is loaded from the database
	 * @access private
	 */
	var $prices_loaded = false;

	var $attribute_prices_uids = array();

	var $article_uid;
	var $product_uid;

	var $attribute_prices_uids_list = '';

	var $is_init = false;

	/**
	 * @param tx_commerce_article_price $articlePriceData
	 * @return void
	 */
	function postpricenet(&$articlePriceData) {

		$this->postinit($articlePriceData);

		if ((int) $articlePriceData->price_net == 0) {

			$attribute_prices_sum_net = 0;

			if ($this->attribute_prices_uids_list) {
				$attributesPriceRes = $GLOBALS['TYPO3_DB']->exec_SELECTQuery("price_net",
					'tx_commerce_attributes_prices',
						'uid IN (' . $this->attribute_prices_uids_list . ')');

				while ($attribute_price = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($attributesPriceRes)) {
					$attribute_prices_sum_net += $attribute_price['price_net'];
				}
			}

			$this->product_price_uid = $this->load_prices_uid(1, $articlePriceData);

			if ($this->product_price_uid) {
				$productPriceRes = $GLOBALS['TYPO3_DB']->exec_SELECTQuery("*",
					'tx_commerce_products_prices',
						'uid = ' . $this->product_price_uid);
				$productPrice = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($productPriceRes);
			}

			$articlePriceData->price_net = $productPrice['price_net'] + $attribute_prices_sum_net;

			//$articlePriceData->price_gross = 500;
		}

	}


	function postpricegross(&$articlePriceData) {

		$this->postinit($articlePriceData);

		if ((int) $articlePriceData->price_gross == 0) {

			$attribute_prices_sum_gross = 0;

			if ($this->attribute_prices_uids_list) {
				$attributesPriceRes = $GLOBALS['TYPO3_DB']->exec_SELECTQuery("price_gross",
					'tx_commerce_attributes_prices',
						'uid IN (' . $this->attribute_prices_uids_list . ')');

				while ($attribute_price = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($attributesPriceRes)) {
					$attribute_prices_sum_gross += $attribute_price['price_gross'];
				}
			}

			$this->product_price_uid = $this->load_prices_uid(1, $articlePriceData);

			if ($this->product_price_uid) {
				$productPriceRes = $GLOBALS['TYPO3_DB']->exec_SELECTQuery("*",
					'tx_commerce_products_prices',
						'uid = ' . $this->product_price_uid);
				$productPrice = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($productPriceRes);
			}

			$articlePriceData->price_gross = $productPrice['price_gross'] + $attribute_prices_sum_gross;

			//$articlePriceData->price_gross = 500;
		}

	}

	/**
	 * @param tx_commerce_article_price $articlePrice
	 * @return void
	 */
	public function postinit(&$articlePrice) {

		if (!$this->is_init && $articlePrice->uid > 0) {
			$articlePrice->add_field_to_fieldlist('uid_article');

			$articlePrice->load_data(false);

			$this->article_uid = $articlePrice->getField('uid_article');

			//Get the product uid of the article
			$this->product_uid = tx_commerce_belib::getProductOfArticle($this->article_uid, '');

			$article_attributes = tx_commerce_belib::getAttributesForArticle($this->article_uid);


			$article_attributes_values_uids = array();
			if (is_array($article_attributes)) {
				foreach ($article_attributes as $article_attribute) {
					$article_attributes_values_uids[] = $article_attribute['uid_valuelist'];
				}
			}
			if (is_array($article_attributes_values_uids)) {
				$article_attributes_values_uids = array_unique($article_attributes_values_uids);

				foreach ($article_attributes_values_uids as $article_attributes_attributes_uid) {
					if ($article_attributes_attributes_uid != 0) {
						$this->attribute_prices_uid = $article_attributes_attributes_uid;
						if ($loaded_prices = $this->load_prices_uid(2, $articlePrice)) {
							$this->attribute_prices_uids[$article_attributes_attributes_uid] = $loaded_prices;
						}
					}
				}
			}

			$this->attribute_prices_uids_list = implode(',', $this->attribute_prices_uids);

			$this->is_init = true;
		}
	}

	/**
	 * gets all prices form database related to this product
	 * @param uid= Article uid
	 * @param count = Number of Articles for price_scale_amount, default 1
	 * @return array of Price UID
	 */

	function get_prices($uid, $price_table_id, $count = 1, $orderField = 'price_net') {

		$orderField;

		if ($price_table_id == 1) {
			$price_table = 'tx_commerce_products_prices';
			$uid_field = 'uid_product';
		} else {
			$price_table = 'tx_commerce_attributes_prices';
			$uid_field = 'uid_attribute_value';
		}

		if ($uid > 0) {
			$price_uid_list = array();
			if (is_object($GLOBALS['TSFE']->sys_page)) {
				$proofSQL = $GLOBALS['TSFE']->sys_page->enableFields($price_table, $GLOBALS['TSFE']->showHiddenRecords);
			}
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,fe_group', $price_table, $uid_field . ' = ' . $uid . ' and price_scale_amount_start <= ' . $count . ' and price_scale_amount_end >= ' . $count . $proofSQL, '', $orderField);

			if ($GLOBALS['TYPO3_DB']->sql_num_rows($result) > 0) {
				while ($return_data = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
					$price_uid_list[$return_data['fe_group']][] = $return_data['uid'];
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($result);
				return $price_uid_list;
			} else {
				tx_commerce_db_alib::error("exec_SELECTquery('uid,fe_group',$price_table,\"$uid_field = $uid\"); returns no Result");
				return false;
			}
		} else {
			return false;
		}
	}

	/**
	 * Gets the price of this article and stores in private variable
	 * @param table = 1 for tx_commerce_products_prices, 2 for tx_commerce_attribute_prices
	 * @since 28.08.2005 Check Class valiable article_loaded for more performace
	 * @author Sascha Egerer
	 * @return the price_uid
	 */
	function load_prices_uid($price_table_id, $articlePriceData) {

		$this->postinit($articlePriceData);

		if ($price_table_id == 1) {
			$uid = $this->product_uid;
		} else {
			$uid = $this->attribute_prices_uid;
		}

		$arrayOfPrices = $this->get_prices($uid, $price_table_id);
		$this->prices_uids = $arrayOfPrices;


		if ($this->prices_uids) {
			// If we do have a Logged in usergroup walk thrue and check if there is a special price for this group
			if ((empty($GLOBALS['TSFE']->fe_user->groupData['uid']) == false) &&
					($GLOBALS['TSFE']->loginUser || count($GLOBALS['TSFE']->fe_user->groupData['uid']) > 0)) {

				$tempGroups = $GLOBALS['TSFE']->fe_user->groupData['uid'];
				while (list($k, $v) = each($tempGroups)) {
					$groups[] = $v;
				}

				$i = 0;
				while (!$this->prices_uids[$groups[$i]] && $groups[$i]) {
					$i++;
				}
				if ($groups[$i]) {
					$this->price = new tx_commerce_article_price($this->prices_uids[$groups[$i]][0]);
					$this->price->load_data();
					$this->price_uid = $this->prices_uids[$groups[$i]][0];
				} else {
					if ($this->prices_uids['-2']) {
						$this->price = new tx_commerce_article_price($this->prices_uids['-2'][0]);
						$this->price->load_data();
						$this->price_uid = $this->prices_uids['-2'][0];
					} else {
						$this->price = new tx_commerce_article_price($this->prices_uids[0][0]);
						if ($this->price) {
							$this->price->load_data();
							$this->price_uid = $this->prices_uids['0'][0];
						} else {
							return false;
						}
					}
				}
			} else {
				// No special Handling if no special usergroup is logged in

				if ($this->prices_uids['-1']) {
					$this->price = new tx_commerce_article_price($this->prices_uids['-1'][0]);
					$this->price->load_data();
					$this->price_uid = $this->prices_uids['-1'][0];
				} else {
					$this->price = new tx_commerce_article_price($this->prices_uids[0][0]);
					if ($this->price) {
						$this->price->load_data();
						$this->price_uid = $this->prices_uids['0'][0];
					} else {
						return false;
					}
				}
			}
			$this->prices_loaded = true;
			return $this->price_uid;

		} else {

			return false;
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']["ext/com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_price_hooks.php"]) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']["ext/com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_price_hooks.php"]);
}

?>
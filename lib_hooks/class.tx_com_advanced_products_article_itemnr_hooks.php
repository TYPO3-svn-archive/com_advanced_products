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
 *
 * @author      Sascha Egerer <seg@softvision.de>
 * @package 	TYPO3
 * @subpackage 	tx_commerce
 *
 */

class tx_com_advanced_products_article_itemnr_hooks {
	
	function post_loaddata(&$articleData) {
		#function postinit(&$articleData) {
		

		if ((!isset($articleData->ordernumber) || $articleData->ordernumber == '') && $articleData->uid != NULL) {
			
			/**
			 * We use the backend function because it returns more informations wich are required
			 * 
			 * return of the backend function:
			 * 
			 * Array (1)
			 *  *variable*
			 *  Array (8)	0	
			 *  String (3)	uid_local	 472
			 *  String (2)	uid_foreign	 23
			 *  String (0)	tablenames	  
			 *  String (1)	sorting	 2
			 *  String (0)	value_char	  
			 *  String (2)	uid_valuelist	 96
			 *  String (1)	uid_product	 0
			 *  String (4)	default_value	 0.00
			 * 
			 * 
			 * return of the article function get_article_attributes 
			 * 
			 * Array (1)
			 * *variable*
			 *  Array (2)	23	
			 *  String (16)	title	 Shinai - Gr��e
			 *  String (2)	value	 39
			 * 
			 */
			
			$article_attributes = tx_commerce_belib::getAttributesForArticle($articleData->uid);
			//$article_attributes = $articleData->get_article_attributes();
			

			$article_attributes_values_uids = array();
			
			if (is_array($article_attributes)) {
				foreach ( $article_attributes as $article_attribute ) {
					$article_attributes_values_uids[] = $article_attribute['uid_valuelist'];
				}
			}
			
			if (is_array($article_attributes_values_uids)) {
				$article_attributes_values_uids = array_unique($article_attributes_values_uids);
				
				foreach ( $article_attributes_values_uids as $article_attributes_attributes_uid ) {
					if ($article_attributes_attributes_uid != 0) {
						$attributeValueOrderNumberExtRes = $GLOBALS['TYPO3_DB']->exec_SELECTQuery("tx_commerce_attribute_values.ordernumber_ext,tx_commerce_attributes.ordernumber_ext_prio", 'tx_commerce_attribute_values,tx_commerce_attributes', 'tx_commerce_attribute_values.uid = ' . $article_attributes_attributes_uid . ' AND tx_commerce_attribute_values.attributes_uid = tx_commerce_attributes.uid', NULL);
						
						if ($attributeValueOrderNumberExt = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($attributeValueOrderNumberExtRes)) {
							$ordernumber_ext[$attributeValueOrderNumberExt['ordernumber_ext_prio']] = $attributeValueOrderNumberExt['ordernumber_ext'];
						}
					}
				}
			}
			
			if (is_array($ordernumber_ext)) {
				krsort($ordernumber_ext);
				$ordernumber_ext = implode('', $ordernumber_ext);
			}
			
			//$productData = tx_commerce_belib::getProductOfArticle($articleData->uid,'ordernumber');
			$product = $articleData->get_parent_product();
			$product->init($product->uid);
			$product->add_field_to_fieldlist('ordernumber');
			$product->load_data();
			
			//set the new ordernumber
			$articleData->article_generated_ordernumber = $product->ordernumber . $ordernumber_ext;
			$articleData->ordernumber = $articleData->article_generated_ordernumber;
		
		}
	}
	
	function additionalMarkerArticle(&$articleMarker, &$article, $pi_base_obj) {
		
		if ($article->article_generated_ordernumber != '') {
			//$article->article_ordernumber = $article->article_generated_ordernumber;
			$articleMarker['ARTICLE_ORDERNUMBER'] = $article->article_generated_ordernumber;
			
			//DEPRECATED
			$articleMarker['ARTICLE_GENERATED_ORDERNUMBER'] = $article->article_generated_ordernumber;
		
		} else {
			$articleMarker['ARTICLE_GENERATED_ORDERNUMBER'] = $articleMarker['ARTICLE_ORDERNUMBER'];
		}
		
		return $articleMarker;
	}
	
	function additionalMarkerProductList(&$markerArray, &$myItem, $parent_this) {
		
		$markerArray = $this->additionalMarkerArticle($markerArray, $myItem->article, $parent_this);
		
		return $markerArray;
	}
	
	function processMarkerLineView(&$markerArray, &$myItem, $parent_this) {
		
		if ($myItem->article->article_generated_ordernumber)
			$markerArray['###BASKET_ITEM_ORDERNUMBER###'] = $myItem->article->article_generated_ordernumber;
		
		return $markerArray;
	}
	
	function additionalAttributeMarker(&$markerArrayItem, &$parent_this, $key) {
		
		$attributePrice = false;
		
		$cap_article_price = t3lib_div::makeInstance('tx_com_advanced_products_article_price_hooks');
		
		$cap_article_price->attribute_prices_uid = $key;
		$price_uid = $cap_article_price->load_prices_uid(2);
		
		if ($price_uid) {
			$attributePriceRes = $GLOBALS['TYPO3_DB']->exec_SELECTQuery("*", 'tx_commerce_attributes_prices', 'uid = ' . $price_uid);
			$attributePrice = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($attributePriceRes);
			
			$attributePrice = tx_moneylib::format($attributePrice['price_gross'], $parent_this->currency);
		}
		
		if ($attributePrice) {
			$markerArrayItem['###SELECT_ATTRIBUTES_PRICE###'] = $parent_this->cObj->stdWrap($attributePrice, $parent_this->conf[$parent_this->handle . '.']['products.']['fields.']['attributes_price.']);
		} else {
			$markerArrayItem['###SELECT_ATTRIBUTES_PRICE###'] = '';
		}
		return $markerArrayItem;
	}

	function postinit(&$articleData) {
		/* GET THE TAX FROM THE PRODUCT AND SET IT TO THE ARTICLE TAX */

		if(empty($articleData->tax)) {
			//$productData = tx_commerce_belib::getProductOfArticle($articleData->uid);
			$productData = $articleData->get_parent_product();

			$product=t3lib_div::makeInstance('tx_commerce_product');
			$product->init($productData->uid);
			$product->add_field_to_fieldlist('tax');
			$product->load_data();
			$articleData->tax = $product->tax;
		}

	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']["ext/com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_itemnr_hooks.php"]) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']["ext/com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_itemnr_hooks.php"]);
}

?>
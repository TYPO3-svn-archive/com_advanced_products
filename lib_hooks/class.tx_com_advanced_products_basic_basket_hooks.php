<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007 Sascha Egerer <seg@softvision.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
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

class tx_com_advanced_products_basic_basket_hooks {
    
    function postAddArticle($itemId,$article_uid,$quantity,$priceid,&$parent_this) {
		
    	if(empty($parent_this->basket_items[$itemId]->article->tax) || $parent_this->basket_items[$itemId]->article->tax == 0) {
			//$productData = tx_commerce_belib::getProductOfArticle($articleData->uid);
			$productData = $parent_this->basket_items[$itemId]->article->get_parent_product();

			$product=t3lib_div::makeInstance('tx_commerce_product');
			$product->init($productData->uid);
			$product->add_field_to_fieldlist('tax');
			$product->load_data();
			$parent_this->basket_items[$itemId]->article->tax = $product->tax;
		}

    	
/*    	
    	if(stripos($itemId,"-") !== FALSE) {
    		$article_infos = explode('-',$itemId);
    		//entfernt den ersten wert da dies der hauptartikel ist
    		$bundle_articles = array_slice($article_infos,1);

    		foreach ($bundle_articles as $bundle_article_uid) {
   				$article = t3lib_div::makeInstance('tx_commerce_article');
				//we only need the ordernumber
				$article->init($bundle_article_uid, $lang = 0);
				$article->load_data();

				if($article->get_ordernumber() != '') {
					$main_article_ordernumber = $parent_this->basket_items[$itemId]->getOrderNumber();
					
					$parent_this->basket_items[$itemId]->article->ordernumber = $main_article_ordernumber . '-' . $article->get_ordernumber();
					$parent_this->basket_items[$itemId]->article->article_generated_ordernumber = $main_article_ordernumber . '-' . $article->get_ordernumber();
				}
    		}
    	}
    	*/

    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']["ext/com_free_input_attr/lib_hooks/class.tx_com_free_input_attr_basket_hooks.php"]) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']["ext/com_free_input_attr/lib_hooks/class.tx_com_free_input_attr_basket_hooks.php"]);
}

?>
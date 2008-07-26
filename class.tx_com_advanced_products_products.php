<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 - 2006 Thomas Hempel (thomas@work.de)
*  reuse by Sascha Egerer (seg@softvision.de)
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
 * This class provides several methods for creating articles from within a product. It provides
 * the user fields and creates the entries in the database.
 *
 * @package TYPO3
 * @subpackage tx_commerce
 *
 * @author Thomas Hempel <thomas@work.de> - reuse by Sascha Egerer <seg@softvision.de>
 *
 */

class tx_commerce_products_products {
	var $uid = 0;
	var $pid = 0;

	/**
	 * Constructor
	 */
	function tx_commerce_products_products() {
		$this->returnUrl = htmlspecialchars(urlencode(t3lib_div::_GP('returnUrl')));
	}

	/**
	 * Creates a checkbox that has to be toggled for creating a new price for an article.
	 * The handling for creating the new price is inside the tcehooks
	 */
	function createNewPriceCB($PA, $fObj) {
		$content .= '<div id="typo3-newRecordLink">';
		$content .= '<input type="checkbox" name="data[tx_commerce_products][' .$PA['row']['uid'] .'][create_new_price]" />';
		$content .= $GLOBALS['LANG']->sL('LLL:EXT:commerce/locallang_be.php:articles.add_article_price', 1);

		$content .= '</div>';
		return $content;
	}
	/**
	 * Creates ...
	 */
	function createNewScalePricesCount($PA, $fObj) {
	
		$content = '<input style="width: 77px;" class="formField1" maxlength="20" type="input" name="data[tx_commerce_products][' .$PA['row']['uid'] .'][create_new_scale_prices_count]" />';


		return $content;
	}	
	/**
	 * Creates ...
	 */
	function createNewScalePricesSteps($PA, $fObj) {


		$content = '<input style="width: 77px;" class="formField1" maxlength="20"type="input" name="data[tx_commerce_products][' .$PA['row']['uid'] .'][create_new_scale_prices_steps]" />';	


		return $content;
	}	
	/**
	 * Creates ...
	 */
	function createNewScalePricesStartAmount($PA, $fObj) {
	
		$content = '<input style="width: 77px;" class="formField1" maxlength="20" type="input" name="data[tx_commerce_products][' .$PA['row']['uid'] .'][create_new_scale_prices_startamount]" />';


		return $content;
	}	


	/**
	 * Creates a delete button that is assigned to a price. If the button is pressed the price will be deleted from the article
	 */
	function deletePriceButton($PA, $fObj)	{
			// get the return URL.This is need to fit all possible combinations of GET vars
		$returnUrl = explode('/', $fObj->returnUrl);
		$returnUrl = $returnUrl[(count($returnUrl) -1)];

			// get the UID of the price
		$name = explode('caption_', $PA['itemFormElName']);
		$name = explode(']', $name[1]);
		$pUid = $name[0];

			// build the link code
		$result = '<a href="#" onclick="deleteRecord(\'tx_commerce_products_prices\', ' .$pUid .', \'' .$returnUrl .'\');">';
		$result .= '<img src="../typo3/gfx/garbage.gif" border="0" />';
		$result .=  $GLOBALS['LANG']->sL('LLL:EXT:commerce/locallang_be.php:articles.del_article_price', 1).'</a>';

		return $result;
	}
	
	/**
	 * Returns a hidden field with the name and value of the current form element
	 */
	function productUid($PA, $fObj) {
		$content.='<input type="hidden" name="'.$PA['itemFormElName'].'" value="'.htmlspecialchars($PA['itemFormElValue']).'">';
 		return $content;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']["ext/com_advanced_products/class.tx_com_advanced_products_products.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']["ext/com_advanced_products/class.tx_com_advanced_products_products.php"]);
}
?>
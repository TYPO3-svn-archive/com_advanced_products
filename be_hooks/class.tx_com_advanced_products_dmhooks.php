<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2005 - 2006 Thomas Hempel (thomas@work.de)
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
 * This class contains some hooks for processing formdata.
 * Hook for saving order data and order_articles.
 *
 * @package TYPO3
 * @subpackage tx_commerce
 *
 * @author		 Thomas Hempel <thomas@work.de>
 * @author		 Ingo Schmitt <is@marketing-factory.de>
 * @author		 Sascha Egerer <seg@softvision.de>
 *
 */
require_once(t3lib_extmgm::extPath('commerce') . 'lib/class.tx_commerce_belib.php');
require_once(t3lib_extmgm::extPath('graytree') . 'lib/class.tx_graytree_folder_db.php');

class tx_com_advanced_products_dmhooks {
	var $belib;
	var $catList = NULL;

	/**
	 * This is just a constructor to instanciate the backend library
	 *
	 * @author Thomas Hempel <thomas@work.de>
	 */
	function tx_com_advanced_products_dmhooks() {
		$this->belib = t3lib_div::makeInstance('tx_commerce_belib');
	}

	/**
	 * When all operations in the database where made from TYPO3 side, we have to make some special
	 * entries for the shop. Because we don't use the built in routines to save relations between
	 * tables, we have to do this on our own. We make it manually because we save some additonal information
	 * in the relation tables like values, correlation types and such stuff.
	 * The hole save stuff is done by the "saveAllCorrelations" method.
	 * After the relations are stored in the database, we have to call the dynaflex extension to modify
	 * the TCA that it fit's the current situation of saved database entries. We call it here because the TCA
	 * is allready built and so the calls in the tca.php of commerce won't be executed between now and the point
	 * where the backendform is rendered.
	 *
	 * @param	[type]		$status: ...
	 * @param	[type]		$table: ...
	 * @param	[type]		$id: ...
	 * @param	[type]		$fieldArray: ...
	 * @param	[type]		$pObj: ...
	 * @return	[type]		...
	 * @author Thomas Hempel <thomas@work.de>
	 * @author Sascha Egerer <seg@softvision.de>
	 */
	function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, $pObj) {

		// get the UID of the created record if it was just created
		if (strtolower(substr($id, 0, 3)) == 'new') {
			$id = $pObj->substNEWwithIDs[$id];
		}

		t3lib_BEfunc::getSetUpdateSignal('updatePageTree');
	}


	/**
	 * save Price-Flexform with given Attribute_Value-UID
	 *
	 * @author	Joerg Sprung <jsp@marketing-factory>
	 * @param	integer	$priceUid		ID of Price-Dataset save as flexform
	 * @param	integer	$articleUid		ID of article which the flexform is for
	 * @param	array	$priceDataArray	Priceinformation for the article
	 * @return	boolean	Status of method
	 * @see tx_commerce_belib
	 */
	function savePriceFlexformWithAttributeValue($priceUid, $attributeValueUid, $priceDataArray) {

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'prices',
			'tx_commerce_attribute_values',
				'uid=' . $attributeValueUid
		);

		$prices = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if (strlen($prices['prices']) > 0) {
			$data = t3lib_div::xml2array($prices['prices']);
		} else {
			$data = array('data' => array('sDEF' => array('lDEF')));
		}

		$data['data']['sDEF']['lDEF']['price_net_' . $priceUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['price_net'] / 100)));
		$data['data']['sDEF']['lDEF']['price_gross_' . $priceUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['price_gross'] / 100)));
		$data['data']['sDEF']['lDEF']['hidden_' . $priceUid] = array('vDEF' => $priceDataArray['hidden']);
		$data['data']['sDEF']['lDEF']['starttime_' . $priceUid] = array('vDEF' => $priceDataArray['starttime']);
		$data['data']['sDEF']['lDEF']['endtime_' . $priceUid] = array('vDEF' => $priceDataArray['endtime']);
		$data['data']['sDEF']['lDEF']['fe_group_' . $priceUid] = array('vDEF' => $priceDataArray['fe_group']);
		$data['data']['sDEF']['lDEF']['purchase_price_' . $priceUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['purchase_price'] / 100)));
		$data['data']['sDEF']['lDEF']['price_scale_amount_start_' . $priceUid] = array('vDEF' => $priceDataArray['price_scale_amount_start']);
		$data['data']['sDEF']['lDEF']['price_scale_amount_end_' . $priceUid] = array('vDEF' => $priceDataArray['price_scale_amount_end']);

		$xml = t3lib_div::array2xml($data, '', 0, 'T3FlexForms');

		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'tx_commerce_attribute_values',
				'uid=' . $attributeValueUid,
			array('prices' => $xml)
		);

		return (bool) $res;

	}


	/**
	 * save Price-Flexform with given Attribute_Value-UID
	 *
	 * @author	Joerg Sprung <jsp@marketing-factory>
	 * @param	integer	$priceUid		ID of Price-Dataset save as flexform
	 * @param	integer	$articleUid		ID of article which the flexform is for
	 * @param	array	$priceDataArray	Priceinformation for the article
	 * @return	boolean	Status of method
	 * @see tx_commerce_belib
	 */
	function savePriceFlexformWithProduct($priceUid, $productUid, $priceDataArray) {

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'prices',
			'tx_commerce_products',
				'uid=' . $productUid
		);

		$prices = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if (strlen($prices['prices']) > 0) {
			$data = t3lib_div::xml2array($prices['prices']);
		} else {
			$data = array('data' => array('sDEF' => array('lDEF')));
		}

		$data['data']['sDEF']['lDEF']['price_net_' . $priceUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['price_net'] / 100)));
		$data['data']['sDEF']['lDEF']['price_gross_' . $priceUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['price_gross'] / 100)));
		$data['data']['sDEF']['lDEF']['hidden_' . $priceUid] = array('vDEF' => $priceDataArray['hidden']);
		$data['data']['sDEF']['lDEF']['starttime_' . $priceUid] = array('vDEF' => $priceDataArray['starttime']);
		$data['data']['sDEF']['lDEF']['endtime_' . $priceUid] = array('vDEF' => $priceDataArray['endtime']);
		$data['data']['sDEF']['lDEF']['fe_group_' . $priceUid] = array('vDEF' => $priceDataArray['fe_group']);
		$data['data']['sDEF']['lDEF']['purchase_price_' . $priceUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['purchase_price'] / 100)));
		$data['data']['sDEF']['lDEF']['price_scale_amount_start_' . $priceUid] = array('vDEF' => $priceDataArray['price_scale_amount_start']);
		$data['data']['sDEF']['lDEF']['price_scale_amount_end_' . $priceUid] = array('vDEF' => $priceDataArray['price_scale_amount_end']);

		$xml = t3lib_div::array2xml($data, '', 0, 'T3FlexForms');

		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'tx_commerce_products',
				'uid=' . $productUid,
			array('prices' => $xml)
		);

		return (bool) $res;

	}


	/**
	 * update Flexform XML from Database
	 *
	 * @author	  Christian Sander <cs2@marketing-factory>
	 * @param	   integer $articleUid	ID of article
	 * @return	  boolean Status of method
	 * @see tx_commerce_belib
	 */

	function updateAttributeValuePriceXMLFromDatabase($attributeValueUid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'tx_commerce_attributes_prices',
				'deleted=0 AND uid_article=' . $attributeValueUid
		);
		$data = array('data' => array('sDEF' => array('lDEF')));
		while ($priceDataArray = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$attributeValueUid = $priceDataArray["uid"];
			$data['data']['sDEF']['lDEF']['price_net_' . $attributeValueUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['price_net'] / 100)));
			$data['data']['sDEF']['lDEF']['price_gross_' . $attributeValueUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['price_gross'] / 100)));
			$data['data']['sDEF']['lDEF']['hidden_' . $attributeValueUid] = array('vDEF' => $priceDataArray['hidden']);
			$data['data']['sDEF']['lDEF']['starttime_' . $attributeValueUid] = array('vDEF' => $priceDataArray['starttime']);
			$data['data']['sDEF']['lDEF']['endtime_' . $attributeValueUid] = array('vDEF' => $priceDataArray['endtime']);
			$data['data']['sDEF']['lDEF']['fe_group_' . $attributeValueUid] = array('vDEF' => $priceDataArray['fe_group']);
			$data['data']['sDEF']['lDEF']['purchase_price_' . $attributeValueUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['purchase_price'] / 100)));
			$data['data']['sDEF']['lDEF']['price_scale_amount_start_' . $attributeValueUid] = array('vDEF' => $priceDataArray['price_scale_amount_start']);
			$data['data']['sDEF']['lDEF']['price_scale_amount_end_' . $attributeValueUid] = array('vDEF' => $priceDataArray['price_scale_amount_end']);
		}

		$xml = t3lib_div::array2xml($data, '', 0, 'T3FlexForms');

		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'tx_commerce_attribute_values',
				'uid=' . $attributeValueUid,
			array('prices' => $xml)
		);

		return (bool) $res;
	}


	/**
	 * update Flexform XML from Database
	 *
	 * @author	  Christian Sander <cs2@marketing-factory>
	 * @param	   integer $articleUid	ID of article
	 * @return	  boolean Status of method
	 * @see tx_commerce_belib
	 */

	function updateProductsPriceXMLFromDatabase($productUid) {

		$res = false;

		if ($productUid) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				'tx_commerce_products_prices',
					'deleted=0 AND uid_product=' . $productUid
			);
			$data = array('data' => array('sDEF' => array('lDEF')));
			while ($priceDataArray = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$attributeValueUid = $priceDataArray["uid"];
				$data['data']['sDEF']['lDEF']['price_net_' . $attributeValueUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['price_net'] / 100)));
				$data['data']['sDEF']['lDEF']['price_gross_' . $attributeValueUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['price_gross'] / 100)));
				$data['data']['sDEF']['lDEF']['hidden_' . $attributeValueUid] = array('vDEF' => $priceDataArray['hidden']);
				$data['data']['sDEF']['lDEF']['starttime_' . $attributeValueUid] = array('vDEF' => $priceDataArray['starttime']);
				$data['data']['sDEF']['lDEF']['endtime_' . $attributeValueUid] = array('vDEF' => $priceDataArray['endtime']);
				$data['data']['sDEF']['lDEF']['fe_group_' . $attributeValueUid] = array('vDEF' => $priceDataArray['fe_group']);
				$data['data']['sDEF']['lDEF']['purchase_price_' . $attributeValueUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['purchase_price'] / 100)));
				$data['data']['sDEF']['lDEF']['price_scale_amount_start_' . $attributeValueUid] = array('vDEF' => $priceDataArray['price_scale_amount_start']);
				$data['data']['sDEF']['lDEF']['price_scale_amount_end_' . $attributeValueUid] = array('vDEF' => $priceDataArray['price_scale_amount_end']);
			}

			$xml = t3lib_div::array2xml($data, '', 0, 'T3FlexForms');

			$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'tx_commerce_products',
					'uid=' . $productUid,
				array('prices' => $xml)
			);
		}

		return (bool) $res;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']["ext/com_advanced_products/be_hooks/class.tx_com_advanced_products_dmhooks.php"]) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']["ext/com_advanced_products/be_hooks/class.tx_com_advanced_products_dmhooks.php"]);
}
?>

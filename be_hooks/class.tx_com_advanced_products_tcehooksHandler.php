<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Carsten Lausen
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

class tx_com_advanced_products_tcehooksHandler {

	/**
	* At this place we process prices, before they are written to the database. We use this for tax calculation
	*
	* @param array $incomingFieldArray: The values from the form, by reference
	* @param string $table: The table we are working on
	* @param int $id: The uid we are working on
	* @param mixed $pObj: The caller
	*/
	function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, $pObj) {
		
		if($table=='tx_commerce_products_prices' || $table=='tx_commerce_attributes_prices') {
			//Get the whole price, not only the tce-form fields
			if($table=='tx_commerce_products_prices') {
				foreach($pObj->datamap['tx_commerce_products'] as $v){
					$uids = explode(',',$v['prices']);
					if(in_array($id, $uids)) {
						$this->calculateTax($incomingFieldArray, doubleval($v['tax']));
					}
				}
			
			} elseif($table=='tx_commerce_attributes_prices')  {
				foreach($pObj->datamap['tx_commerce_attribute_values'] as $v){
					$uids = explode(',',$v['prices']);
					if(in_array($id, $uids)) {
						$this->calculateTax($incomingFieldArray, doubleval($v['tax']));
					}
				}
			}
			foreach($incomingFieldArray as $key => $value){
				if ($key == 'price_net' || $key == 'price_gross' || $key == 'purchase_price')   {
					if (is_numeric($value)){
						//first convert the float value to a string - this is required because of a php "bug"
						//details on http://forge.typo3.org/issues/show/2986
						//and http://de.php.net/manual/en/function.intval.php
						$incomingFieldArray[$key] = intval(strval($value *100));
					}
				}
			}
		}
	}
	
	function calculateTax(&$fieldArray, $tax) {
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['commerce']);
		if($extConf['genprices']==0) {
			return;
		} else {
			if($extConf['genprices']==2 || !isset($fieldArray['price_gross']) || $fieldArray['price_gross']==='' || strlen($fieldArray['price_gross'])==0 || doubleval($fieldArray['price_gross'])===0.0) {
				$fieldArray['price_gross']=round(($fieldArray['price_net']*100)*(100+$tax)/100)/100;
			}
			if($extConf['genprices']==3 || !isset($fieldArray['price_net']) || $fieldArray['price_net']==='' || strlen($fieldArray['price_net'])==0 || doubleval($fieldArray['price_net'])===0.0) {
				$fieldArray['price_net']=round(($fieldArray['price_gross']*100)/(100+$tax)*100)/100;
			}
		}
	}

	/**
	* processDatamap_postProcessFieldArray()
	* this function is called by the Hook in tce from class.t3lib_tcemain.php after processing insert & update database operations
	*
	* @param string $status: update or new
	* @param string $table: database table
	* @param string $id: database table
	* @param array $fieldArray: reference to the incoming fields
	* @param object $pObj: page Object reference
	*/
	
	function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$pObj){
		if($table=='tx_commerce_products_prices' || $table=='tx_commerce_attributes_prices') {
			// ugly hack since typo3 makes ugly checks
			foreach($fieldArray as $key => $value){
				if ($key == 'price_net' || $key == 'price_gross' || $key == 'purchase_price')   {
					$fieldArray[$key] = intval($value);
				}
			}
		}
	}
	
	/**
	* processDatamap_afterDatabaseOperations()
	* this function is called by the Hook in tce from class.t3lib_tcemain.php after processing insert & update database operations
	*
	* @param string $status: update or new
	* @param string $table: database table
	* @param string $id: database table
	* @param array $fieldArray: reference to the incoming fields
	* @param object $pObj: page Object reference
	*/
	//function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, &$pObj){

	//}


	/**
	* processCmdmap_preProcess()
	* this function is called by the Hook in tce from class.t3lib_tcemain.php before processing commands
	*
	* @param string $command: reference to command: move,copy,version,delete or undelete
	* @param string $table: database table
	* @param string $id: database record uid
	* @param array $value: reference to command parameter array
	* @param object $pObj: page Object reference
	*/
//	function processCmdmap_preProcess(&$command, $table, $id, &$value, &$pObj){
//	}


}
if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/com_advanced_products/be_hooks/class.tx_com_advanced_products_tcehooksHandler.php']) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/com_advanced_products/be_hooks/class.tx_com_advanced_products_tcehooksHandler.php']);
}
?>
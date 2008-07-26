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
 * @author 		Thomas Hempel <thomas@work.de>
 * @author 		Ingo Schmitt <is@marketing-factory.de>
 * @author 		Sascha Egerer <seg@softvision.de>
 *
 */
require_once(t3lib_extmgm::extPath('commerce') .'lib/class.tx_commerce_belib.php');
require_once(t3lib_extmgm::extPath('graytree').'lib/class.tx_graytree_folder_db.php');

class tx_com_advanced_products_dmhooks	{
	var $belib;
	var $catList = NULL;

	/**
	 * This is just a constructor to instanciate the backend library
	 *
	 * @author Thomas Hempel <thomas@work.de>
	 */
	function tx_com_advanced_products_dmhooks()	{
		$this->belib = t3lib_div::makeInstance('tx_commerce_belib');
	}

	/**
	 * This hook is processed BEFORE a datamap is processed (save, update etc.)
	 * We use this to check if a product or category is inheriting any attributes from
	 * other categories (parents or similiar). It also removes invalid attributes from the
	 * fieldArray which is saved in the database after this method.
	 * So, if we change it here, the method "processDatamap_afterDatabaseOperations" will work
	 * with the data we maybe have modified here.
	 *
	 * @param	array		$incomingFieldArray: the array of fields that where changed in BE (passed by reference)
	 * @param	string		$table: the table the data will be stored in
	 * @param	integer		$id: The uid of the dataset we're working on
	 * @param	object		$pObj: The instance of the BE Form
	 * @return	void
	 * @author Thomas Hempel <thomas@work.de>
	 * @since 6.10.2005
	 * @author Ingo Schmitt <is@marketing-factory.de>
	 * @author 		Sascha Egerer <seg@softvision.de>
	 * 	Calculation of missing price
	 */
	function processDatamap_preProcessFieldArray(&$incomingFieldArray, $table, $id, $pObj)	{
		//debug(array($incomingFieldArray, $table, $id), 'processDatamap_preProcessFieldArray');
		// check if we have to do something with a really fancy if-statement :-D

		if (
		!(
		(($table == 'tx_commerce_attribute_values' || $table == 'tx_commerce_products') &&
		(isset($incomingFieldArray['prices']) ||
		isset($incomingFieldArray['create_new_price'])))
		) ||
		// don't try ro save anything, if the dataset was just created
		strtolower(substr($id, 0, 3)) == 'new'
		) return;

		switch ($table)	{

			case 'tx_commerce_attribute_values':
			case 'tx_commerce_products':
				// update attribute values prices
				if (isset($incomingFieldArray['prices']))	{
					$prices = $incomingFieldArray['prices']['data']['sDEF']['lDEF'];
					$pricesData = array();
					foreach ($prices as $pKey => $keyData)	{
						if ($keyData)	{
							$value = $keyData['vDEF'];
							$pUid = $this->belib->getUidFromKey($pKey, $keyData);

							unset($keyData[(count($keyData) -1)]);
							$key = implode('_', $keyData);


							if ($key == 'price_net' || $key == 'price_gross' || $key == 'purchase_price')	{
								$value = $value *100;
							}

							/**
							 * Price from tax calculation
							 * @since 06.10.2005
							 * @author Ingo Schmitt <is@marketing-factory.de>
							 */

							if (isset($incomingFieldArray['tax']))	{
								$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['commerce']);


								switch ($extConf['genprices']){
									case 0:
										/**
										 * Do nothing;
										 */
										break;
									case 2:
										/**
										 * Calculare from net 
										 */
										if ($key == 'price_gross') {
											$price_net_value=$incomingFieldArray['prices']['data']['sDEF']['lDEF']['price_net_'.$pUid]['vDEF'];
											$value=round(($price_net_value*100)*(100+$incomingFieldArray['tax'])/100);
											$incomingFieldArray['prices']['data']['sDEF']['lDEF']['price_gross_'.$pUid]['vDEF']=$value/100;
										}
										break;
									case 3:
										/**
										 * Calculate from gross
										 */
										if ($key == 'price_net') {
											$price_gross_value=$incomingFieldArray['prices']['data']['sDEF']['lDEF']['price_gross_'.$pUid]['vDEF'];
											$value=round(($price_gross_value*100)/(100+$incomingFieldArray['tax'])*100);
											$incomingFieldArray['prices']['data']['sDEF']['lDEF']['price_net_'.$pUid]['vDEF']=$value/100;
										}
										break;
									case 1:
									default:

										if (($key == 'price_net') && (empty($value) || $value==0))	{
											$price_gross_value=$incomingFieldArray['prices']['data']['sDEF']['lDEF']['price_gross_'.$pUid]['vDEF'];
											$value=round(($price_gross_value*100)/(100+$incomingFieldArray['tax'])*100);
											$incomingFieldArray['prices']['data']['sDEF']['lDEF']['price_net_'.$pUid]['vDEF']=$value/100;
										} elseif (($key == 'price_gross') && (empty($value) || $value==0))	{
											$price_net_value=$incomingFieldArray['prices']['data']['sDEF']['lDEF']['price_net_'.$pUid]['vDEF'];
											$value=round(($price_net_value*100)*(100+$incomingFieldArray['tax'])/100);
											$incomingFieldArray['prices']['data']['sDEF']['lDEF']['price_gross_'.$pUid]['vDEF']=$value/100;
										}
										break;
								}
							}

							if ($value > '')	{
								$pricesData[$pUid][$key] = $value;
							}
						}
					}
					$error=false;
					/**
					 * @TODO Do Localisation in Output
					 */
					/**
					 * Do some Checks with the data,
					 */
					$minPrice =0;

					foreach ($pricesData as $onePrice) {
						if ($onePrice['price_scale_amount_start']>0 && ($minPrice==0 || $minPrice>$onePrice['price_scale_amount_start'])) {
							$minPrice = $onePrice['price_scale_amount_start'];
						}

						if ($onePrice['price_scale_amount_start'] >$onePrice['price_scale_amount_end']) {
							$pObj->log($table,$id,2,0,1,"Price Scale Amount Start was greater than price scale amount end",1,array($table));
							$error=true;
						}
					}
					if ($minPrice >1) {
						$pObj->log($table,$id,2,0,1,"Minimum Price Sacale amount was more than 1",1,array($table));
					}
					if ($error) {
						// Unset Array to change no value

						$incomingFieldArray = array();
					}

					if (is_array($pricesData) && $error===false)	{
						foreach ($pricesData as $pUid => $pArray)	{
							unset($pArray["create_new_scale_prices_fe"]);
							if (count($pArray)==0) continue;

							if($table == 'tx_commerce_attribute_values') {
								$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
								'tx_commerce_attributes_prices',
								'uid=' .$pUid,
								$pArray
								);
							} elseif ($table == 'tx_commerce_products') {
								$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
								'tx_commerce_products_prices',
								'uid=' .$pUid,
								$pArray
								);
							}

						}
					}
				}

				// create a new price if the checkbox was toggled get pid of article
				if ($incomingFieldArray['create_new_price'] == 'on')	{
					// somehow hook is used two times sometime. So switch off new creating.
					$incomingFieldArray['create_new_price'] = 'off';


					list($modPid,$defaultFolder,$folderList) = tx_graytree_folder_db::initFolders('Commerce', 'commerce');
					list($prodPid,$defaultFolder,$folderList) = tx_graytree_folder_db::initFolders('Products', 'commerce',$modPid);


					$aPid = $prodPid;

					// set some status vars
					$time = time();

					if($table == 'tx_commerce_attribute_values') {
						// create the price
						$GLOBALS['TYPO3_DB']->exec_INSERTquery(
						'tx_commerce_attributes_prices',
						array(
						'pid' => $aPid,
						'tstamp' => $time,
						'crdate' => $time,
						'cruser_id' => $GLOBALS['BE_USER']->user['uid'],
						'uid_attribute_value' => $id,
						)
						);
					} elseif ($table == 'tx_commerce_products') {
						// create the price
						$GLOBALS['TYPO3_DB']->exec_INSERTquery(
						'tx_commerce_products_prices',
						array(
						'pid' => $aPid,
						'tstamp' => $time,
						'crdate' => $time,
						'cruser_id' => $GLOBALS['BE_USER']->user['uid'],
						'uid_product' => $id,
						)
						);
					}
				}


				// create new scale prices if all fields were filled out correcly

				// $create_new_scale_prices_count:	how many steps? (e.g. 3)
				// $create_new_scale_prices_steps:	how big is the step from amount to the next? (e.g. 5)
				// $create_new_scale_prices_startamount:	what is the first amount? (e.g. 10)

				// example values above will create 3 prices with amounts 10-14, 15-19 and 20-24
				$create_new_scale_prices_count=is_numeric($incomingFieldArray['create_new_scale_prices_count'])?intval($incomingFieldArray['create_new_scale_prices_count']):0;
				$create_new_scale_prices_steps=is_numeric($incomingFieldArray['create_new_scale_prices_steps'])?intval($incomingFieldArray['create_new_scale_prices_steps']):0;
				$create_new_scale_prices_startamount=is_numeric($incomingFieldArray['create_new_scale_prices_startamount'])?intval($incomingFieldArray['create_new_scale_prices_startamount']):0;

				if ($create_new_scale_prices_count>0 && $create_new_scale_prices_steps>0 && $create_new_scale_prices_startamount>0)	{
					// somehow hook is used two times sometime. So switch off new creating.
					$incomingFieldArray['create_new_scale_prices_count'] = 0;

					// get pid
					list($modPid,$defaultFolder,$folderList) = tx_graytree_folder_db::initFolders('Commerce', 'commerce');
					list($prodPid,$defaultFolder,$folderList) = tx_graytree_folder_db::initFolders('Products', 'commerce',$modPid);

					$aPid = $prodPid;

					// set some status vars
					$time = time();
					$myScaleAmountStart=$create_new_scale_prices_startamount;
					$myScaleAmountEnd=$create_new_scale_prices_startamount+$create_new_scale_prices_steps-1;

					if($table == 'tx_commerce_attribute_values') {
						// create the different prices
						for($myScaleCounter=1;$myScaleCounter<=$create_new_scale_prices_count;$myScaleCounter++){

							$insertArr=array(
							'pid' => $aPid,
							'tstamp' => $time,
							'crdate' => $time,
							'uid_attribute_value' => $id,
							'fe_group'=> $incomingFieldArray['create_new_scale_prices_fe_group'],
							'price_scale_amount_start'=>$myScaleAmountStart,
							'price_scale_amount_end'=>$myScaleAmountEnd,
							);
							#t3lib_div::debug($insertArr);
							$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_commerce_attributes_prices',$insertArr);

							// TODO: update artciles XML

							$myScaleAmountStart+=$create_new_scale_prices_steps;
							$myScaleAmountEnd+=$create_new_scale_prices_steps;
						}
						$this->updateAttributeValuePriceXMLFromDatabase($id);
					} elseif ($table == 'tx_commerce_products') {
						// create the different prices
						for($myScaleCounter=1;$myScaleCounter<=$create_new_scale_prices_count;$myScaleCounter++){

							$insertArr=array(
							'pid' => $aPid,
							'tstamp' => $time,
							'crdate' => $time,
							'uid_product' => $id,
							'fe_group'=> $incomingFieldArray['create_new_scale_prices_fe_group'],
							'price_scale_amount_start'=>$myScaleAmountStart,
							'price_scale_amount_end'=>$myScaleAmountEnd,
							);
							#t3lib_div::debug($insertArr);
							$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_commerce_products_prices',$insertArr);

							// TODO: update artciles XML

							$myScaleAmountStart+=$create_new_scale_prices_steps;
							$myScaleAmountEnd+=$create_new_scale_prices_steps;
						}
						$this->updateProductsPriceXMLFromDatabase($id);
					}

				}
				break;

		}

	}

	function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, $pObj)	{
		switch (strtolower((string)$table))	{
			case 'tx_commerce_attributes_prices':

				$fieldArray['price_net'] = $fieldArray['price_net'] *100;
				$fieldArray['price_gross'] = $fieldArray['price_gross'] *100;
				$fieldArray['purchase_price'] = $fieldArray['purchase_price'] *100;
				break;
			case 'tx_commerce_products_prices':

				$fieldArray['price_net'] = $fieldArray['price_net'] *100;
				$fieldArray['price_gross'] = $fieldArray['price_gross'] *100;
				$fieldArray['purchase_price'] = $fieldArray['purchase_price'] *100;
				break;
		}
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
	function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, $pObj)	{

		// get the UID of the created record if it was just created
		if (strtolower(substr($id, 0, 3)) == 'new')	{
			$id = $pObj->substNEWwithIDs[$id];
		}
		

		switch (trim(strtolower((string)$table)))	{
			case 'tx_commerce_attribute_values':
				//if this is set to require_once it does not work because the file is already included in ext_tabels.php
				require(t3lib_extMgm::extPath('com_advanced_products') .'ext_df_attribute_values_config.php');
				$dynaFlexConf = $attributeValuesDynaFlexConf;
				$dynaFlexConf['workingTable'] = 'tx_commerce_attribute_values';
				break;
			case 'tx_commerce_products':
				//if this is set to require_once it does not work because the file is already included in ext_tabels.php
				require(t3lib_extMgm::extPath('com_advanced_products') .'ext_df_products_config.php');
				$dynaFlexConf = $productDynaFlexConf;
				$dynaFlexConf['workingTable'] = 'tx_commerce_products';
				break;
			case 'tx_commerce_attributes_prices':
				if (!isset($fieldArray['uid_attribute_value']))	{
					$uidAttributeValueRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'uid_attribute_values',
					'tx_commerce_attributes_prices',
					'uid=' .$id
					);
					$uidAttributeValue = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($uidAttributeValueRes);
					$uidAttributeValue = $uidAttributeValue['uid_attribute_value'];
				} else {
					$uidAttributeValue = $fieldArray['uid_attribute_value'];
				}


				$this->savePriceFlexformWithAttributeValue( $id , $uidAttributeValue , $fieldArray );

				break;
			case 'tx_commerce_products_prices':

				if (!isset($fieldArray['uid_product']))	{
					$uidProductRes = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'uid_product',
					'tx_commerce_products_prices',
					'uid=' .$id
					);
					$uidProduct = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($uidProductRes);
					$uidProduct = $uidProduct['uid_product'];
				} else {
					$uidProduct = $fieldArray['uid_product'];
				}


				$this->savePriceFlexformWithProduct( $id , $uidProduct , $fieldArray );

				break;
		}

		// update the page tree
		t3lib_BEfunc::getSetUpdateSignal('updatePageTree');

		if (t3lib_extMgm::isLoaded('dynaflex') && !empty($dynaFlexConf))	{
			$dynaFlexConf[0]['uid'] = $id;
			$dynaFlexConf[1]['uid'] = $id;

			require_once(t3lib_extMgm::extPath('dynaflex') .'class.dynaflex.php');
			$dynaflex = new dynaflex($GLOBALS['TCA'], $dynaFlexConf);
			$GLOBALS['TCA'] = $dynaflex->getDynamicTCA();
		}
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
	function savePriceFlexformWithAttributeValue( $priceUid , $attributeValueUid, $priceDataArray) {

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
		'prices',
		'tx_commerce_attribute_values',
		'uid=' .$attributeValueUid
		);

		$prices = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if (strlen($prices['prices']) > 0)	{
			$data = t3lib_div::xml2array($prices['prices']);
		} else {
			$data = array('data' => array('sDEF' => array('lDEF')));
		}

		$data['data']['sDEF']['lDEF']['price_net_' .$priceUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['price_net'] /100)));
		$data['data']['sDEF']['lDEF']['price_gross_' .$priceUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['price_gross'] /100)));
		$data['data']['sDEF']['lDEF']['hidden_' .$priceUid] = array('vDEF' => $priceDataArray['hidden']);
		$data['data']['sDEF']['lDEF']['starttime_' .$priceUid] = array('vDEF' => $priceDataArray['starttime']);
		$data['data']['sDEF']['lDEF']['endtime_' .$priceUid] = array('vDEF' => $priceDataArray['endtime']);
		$data['data']['sDEF']['lDEF']['fe_group_' .$priceUid] = array('vDEF' => $priceDataArray['fe_group']);
		$data['data']['sDEF']['lDEF']['purchase_price_' .$priceUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['purchase_price'] /100)));
		$data['data']['sDEF']['lDEF']['price_scale_amount_start_' .$priceUid] = array('vDEF' => $priceDataArray['price_scale_amount_start']);
		$data['data']['sDEF']['lDEF']['price_scale_amount_end_' .$priceUid] = array('vDEF' => $priceDataArray['price_scale_amount_end']);

		$xml = t3lib_div::array2xml($data, '', 0, 'T3FlexForms');

		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
		'tx_commerce_attribute_values',
		'uid=' .$attributeValueUid,
		array('prices' => $xml)
		);

		return (bool)$res;

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
	function savePriceFlexformWithProduct( $priceUid , $productUid, $priceDataArray) {

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
		'prices',
		'tx_commerce_products',
		'uid=' .$productUid
		);

		$prices = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if (strlen($prices['prices']) > 0)	{
			$data = t3lib_div::xml2array($prices['prices']);
		} else {
			$data = array('data' => array('sDEF' => array('lDEF')));
		}

		$data['data']['sDEF']['lDEF']['price_net_' .$priceUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['price_net'] /100)));
		$data['data']['sDEF']['lDEF']['price_gross_' .$priceUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['price_gross'] /100)));
		$data['data']['sDEF']['lDEF']['hidden_' .$priceUid] = array('vDEF' => $priceDataArray['hidden']);
		$data['data']['sDEF']['lDEF']['starttime_' .$priceUid] = array('vDEF' => $priceDataArray['starttime']);
		$data['data']['sDEF']['lDEF']['endtime_' .$priceUid] = array('vDEF' => $priceDataArray['endtime']);
		$data['data']['sDEF']['lDEF']['fe_group_' .$priceUid] = array('vDEF' => $priceDataArray['fe_group']);
		$data['data']['sDEF']['lDEF']['purchase_price_' .$priceUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['purchase_price'] /100)));
		$data['data']['sDEF']['lDEF']['price_scale_amount_start_' .$priceUid] = array('vDEF' => $priceDataArray['price_scale_amount_start']);
		$data['data']['sDEF']['lDEF']['price_scale_amount_end_' .$priceUid] = array('vDEF' => $priceDataArray['price_scale_amount_end']);

		$xml = t3lib_div::array2xml($data, '', 0, 'T3FlexForms');

		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
		'tx_commerce_products',
		'uid=' .$productUid,
		array('prices' => $xml)
		);

		return (bool)$res;

	}


	/**
	* update Flexform XML from Database
	* 
	* @author      Christian Sander <cs2@marketing-factory>
	* @param       integer $articleUid    ID of article
	* @return      boolean Status of method
	* @see tx_commerce_belib
	*/

	function updateAttributeValuePriceXMLFromDatabase( $attributeValueUid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
		'*',
		'tx_commerce_attributes_prices',
		'deleted=0 AND uid_article=' .$attributeValueUid
		);
		$data = array('data' => array('sDEF' => array('lDEF')));
		while($priceDataArray = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
			$attributeValueUid=$priceDataArray["uid"];
			$data['data']['sDEF']['lDEF']['price_net_' .$attributeValueUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['price_net'] /100)));
			$data['data']['sDEF']['lDEF']['price_gross_' .$attributeValueUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['price_gross'] /100)));
			$data['data']['sDEF']['lDEF']['hidden_' .$attributeValueUid] = array('vDEF' => $priceDataArray['hidden']);
			$data['data']['sDEF']['lDEF']['starttime_' .$attributeValueUid] = array('vDEF' => $priceDataArray['starttime']);
			$data['data']['sDEF']['lDEF']['endtime_' .$attributeValueUid] = array('vDEF' => $priceDataArray['endtime']);
			$data['data']['sDEF']['lDEF']['fe_group_' .$attributeValueUid] = array('vDEF' => $priceDataArray['fe_group']);
			$data['data']['sDEF']['lDEF']['purchase_price_' .$attributeValueUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['purchase_price'] /100)));
			$data['data']['sDEF']['lDEF']['price_scale_amount_start_' .$attributeValueUid] = array('vDEF' => $priceDataArray['price_scale_amount_start']);
			$data['data']['sDEF']['lDEF']['price_scale_amount_end_' .$attributeValueUid] = array('vDEF' => $priceDataArray['price_scale_amount_end']);
		}

		$xml = t3lib_div::array2xml($data, '', 0, 'T3FlexForms');

		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
		'tx_commerce_attribute_values',
		'uid=' .$attributeValueUid,
		array('prices' => $xml)
		);

		return (bool)$res;
	}


	/**
	* update Flexform XML from Database
	* 
	* @author      Christian Sander <cs2@marketing-factory>
	* @param       integer $articleUid    ID of article
	* @return      boolean Status of method
	* @see tx_commerce_belib
	*/

	function updateProductsPriceXMLFromDatabase( $productUid) {
	
		$res = false;
		
		if($productUid) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'tx_commerce_products_prices',
			'deleted=0 AND uid_product=' .$productUid
			);
			$data = array('data' => array('sDEF' => array('lDEF')));
			while($priceDataArray = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
				$attributeValueUid=$priceDataArray["uid"];
				$data['data']['sDEF']['lDEF']['price_net_' .$attributeValueUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['price_net'] /100)));
				$data['data']['sDEF']['lDEF']['price_gross_' .$attributeValueUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['price_gross'] /100)));
				$data['data']['sDEF']['lDEF']['hidden_' .$attributeValueUid] = array('vDEF' => $priceDataArray['hidden']);
				$data['data']['sDEF']['lDEF']['starttime_' .$attributeValueUid] = array('vDEF' => $priceDataArray['starttime']);
				$data['data']['sDEF']['lDEF']['endtime_' .$attributeValueUid] = array('vDEF' => $priceDataArray['endtime']);
				$data['data']['sDEF']['lDEF']['fe_group_' .$attributeValueUid] = array('vDEF' => $priceDataArray['fe_group']);
				$data['data']['sDEF']['lDEF']['purchase_price_' .$attributeValueUid] = array('vDEF' => sprintf('%.2f', ($priceDataArray['purchase_price'] /100)));
				$data['data']['sDEF']['lDEF']['price_scale_amount_start_' .$attributeValueUid] = array('vDEF' => $priceDataArray['price_scale_amount_start']);
				$data['data']['sDEF']['lDEF']['price_scale_amount_end_' .$attributeValueUid] = array('vDEF' => $priceDataArray['price_scale_amount_end']);
			}

			$xml = t3lib_div::array2xml($data, '', 0, 'T3FlexForms');

			$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
			'tx_commerce_products',
			'uid=' .$productUid,
			array('prices' => $xml)
			);
		}

		return (bool)$res;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']["ext/com_advanced_products/be_hooks/class.tx_com_advanced_products_dmhooks.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']["ext/com_advanced_products/be_hooks/class.tx_com_advanced_products_dmhooks.php"]);
}
?>

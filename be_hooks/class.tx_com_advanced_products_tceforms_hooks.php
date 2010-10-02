<?php
class tx_com_advanced_products_tceforms_hooks {
	private $extconf;
	private $lastMaxItems=FALSE;
	private $next=FALSE;
	
	function __construct() {
		$this->extconf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['commerce']);
	}

	/**
	 *
	 * @param string $table: The name of the database table (just for calling compatibility)
	 * @param string $field: The name of the field we work on in $table (just for calling compatibility)
	 * @param array $row: The values of all $fields in $table
	 * @param stringe $altName: Unknown, just for calling compatibility
	 * @param string $palette: Unknown, just for calling compatibility
	 * @param string $extra: Unknown, just for calling compatibility
	 * @param string $pal: Unknown, just for calling compatibility
	 * @param mixed $this: Unknown, just for calling compatibility
	 * @return void: Nothing
	 */
	function getSingleField_preProcess($table, $field, &$row, &$out, &$palette, &$extra, $pal, $pObj) {

		if($table=='tx_commerce_products_prices' || $table=='tx_commerce_attributes_prices') {
			$row['price_gross']=$this->centurionDivision(intval($row['price_gross']));
			$row['price_net']=$this->centurionDivision(intval($row['price_net']));
			$row['purchase_price']=$this->centurionDivision(intval($row['purchase_price']));
		} 
	}
	
	/**
	 * Converts a database price into a human readable one i.e. dividing it by 100 using . as a separator
	 * @param int $price: The database price
	 * @result string: The $price divided by 100
	 */
	private function centurionDivision($price) {
	
		$price = floatval($price);
		$result = sprintf("%01.2f",($price/100));
		return $result;
	}
	
	/**
	 * This hook gets called after a field in tceforms gets rendered. We use this to restore the old values after the hook above got called 
	 *
	 * @param string $table: The name of the database table (just for calling compatibility)
	 * @param string $field: The name of the field we work on in $table (just for calling compatibility)
	 * @param array $row: The values of all $fields in $table
	 * @param stringe $altName: Unknown, just for calling compatibility
	 * @param string $palette: Unknown, just for calling compatibility
	 * @param string $extra: Unknown, just for calling compatibility
	 * @param string $pal: Unknown, just for calling compatibility
	 * @param mixed $this: Unknown, just for calling compatibility
	 * @return void: Nothing
	 */
	function getSingleField_postProcess($table, $field, $row, &$out, $palette, $extra) {
		if(
			($table=='tx_commerce_products' && $field == 'prices' && !$row['sys_language_uid'] && strpos($row['uid_product'],'_'.$this->extconf['paymentID'].'|') === false && strpos($row['uid_product'],'_'.$this->extconf['deliveryID'].'|') === false && is_numeric($row['uid']))
			|| ($table=='tx_commerce_attributes' && $field == 'prices' && !$row['sys_language_uid'] && strpos($row['uid_attribute_value'],'_'.$this->extconf['paymentID'].'|') === false && strpos($row['uid_attribute_value'],'_'.$this->extconf['deliveryID'].'|') === false && is_numeric($row['uid']))
		) {
			$splitText='<div class="typo3-newRecordLink">';
			$outa = explode($splitText, $out, 2);
			$out=$outa[0].$this->getScaleAmount($row['uid']).$splitText.$outa[1];
		} 
	}

	
	/**
	 * This function returns the html code for the scale price calculation 
	 */
	function getScaleAmount($uid) {
		//Hier
		//return 'Extrem';
		return	'<div class="bgColor5">price scale startamount</div>
			<div class="bgColor4"><input style="width: 77px;" class="formField1" maxlength="20" name="data[tx_commerce_products]['.$uid.'][create_new_scale_prices_startamount]" type="input"></div>
			</div><div><div class="bgColor5">price scale add prices</div>
			<div class="bgColor4"><input style="width: 77px;" class="formField1" maxlength="20" name="data[tx_commerce_products]['.$uid.'][create_new_scale_prices_count]" type="input"></div>
			</div><div><div class="bgColor5">price scale steps</div>
			<div class="bgColor4"><input style="width: 77px;" class="formField1" maxlength="20" name="data[tx_commerce_products]['.$uid.'][create_new_scale_prices_steps]" type="input"></div>
			</div><div><div class="bgColor5">price scale access</div>
			<div class="bgColor4"><input name="data[tx_commerce_products]['.$uid.'][prices][data][sDEF][lDEF][create_new_scale_prices_fe_group][vDEF]_selIconVal" value="0" type="hidden"><select name="data[tx_commerce_products]['.$uid.'][prices][data][sDEF][lDEF][create_new_scale_prices_fe_group][vDEF]" class="select" onchange="if (this.options[this.selectedIndex].value==\'--div--\') {this.selectedIndex=0;} TBE_EDITOR.fieldChanged(\'tx_commerce_products\',\''.$uid.'\',\'prices\',\'data[tx_commerce_products]['.$uid.'][prices]\');"><option value="0" selected="selected"></option>
			<option value="-1">Hide at login</option>
			<option value="-2">Show at any login</option>
			</select></div>
			';
	}
}
?>

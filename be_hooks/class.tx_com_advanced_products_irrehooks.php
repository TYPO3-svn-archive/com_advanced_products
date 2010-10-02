<?php
require_once(PATH_t3lib.'interfaces/interface.t3lib_tceformsinlinehook.php');
class tx_com_advanced_products_irrehooks implements t3lib_tceformsInlineHook {
	var $parentObject;
	private $extconf;

	/**
	 * Initializes this hook object.
	 *
	 * @param	t3lib_TCEforms_inline		$parentObject: The calling t3lib_TCEforms_inline object.
	 * @return	void
	 */
	public function init(&$parentObject) {
		$this->parentObject = $parentObject;
	}
	
	function __construct() {
		$this->extconf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['commerce']);
	}

	/**
	 * Pre-processing to define which control items are enabled or disabled.
	 *
	 * @param	string		$parentUid: The uid of the parent (embedding) record (uid or NEW...)
	 * @param	string		$foreignTable: The table (foreign_table) we create control-icons for
	 * @param	array		$childRecord: The current record of that foreign_table
	 * @param	array		$childConfig: TCA configuration of the current field of the child record
	 * @param	boolean		$isVirtual: Defines whether the current records is only virtually shown and not physically part of the parent record
	 * @param	array		&$enabledControls: (reference) Associative array with the enabled control items
	 * @return	void
	 */
	public function renderForeignRecordHeaderControl_preProcess($parentUid, $foreignTable, array $childRecord, array $childConfig, $isVirtual, array &$enabledControls) {

		if($foreignTable=='tx_commerce_products_prices' || $foreignTable=='tx_commerce_attributes_prices') {
			$enabledControls = array(
				'new'		=> true,
				'sort'		=> true,
				'hide'		=> true,
				'delete'	=> true,
			); 
		}
	}

	/**
	 * Post-processing to define which control items to show. Possibly own icons can be added here.
	 *
	 * @param	string		$parentUid: The uid of the parent (embedding) record (uid or NEW...)
	 * @param	string		$foreignTable: The table (foreign_table) we create control-icons for
	 * @param	array		$childRecord: The current record of that foreign_table
	 * @param	array		$childConfig: TCA configuration of the current field of the child record
	 * @param	boolean		$isVirtual: Defines whether the current records is only virtually shown and not physically part of the parent record
	 * @param	array		&$controlItems: (reference) Associative array with the currently available control items
	 * @return	void
	 */
	public function renderForeignRecordHeaderControl_postProcess($parentUid, $foreignTable, array $childRecord, array $childConfig, $isVirtual, array &$cells) {
	}
}


?>
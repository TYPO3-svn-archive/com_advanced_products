<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

require_once(t3lib_extmgm::extPath('com_advanced_products').'class.tx_com_advanced_products_attributevalues.php');
require_once(t3lib_extmgm::extPath('com_advanced_products').'class.tx_com_advanced_products_products.php');

$attributesValuesColumns = Array (
	'ordernumber_ext' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:com_advanced_products/locallang_db.xml:tx_commerce_attribute_values.tx_comadvancedproducts_ordernumber_ext',
		'l10n_mode' => 'exclude',
		'config' => Array (
			'type' => 'input',
			'size' => '40',
			'max' => '80',
			'eval' => 'trim',
		)
	),
	'prices' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_articles.prices',
		'l10n_mode' => 'exclude',
		'config' => array (
			'type' => 'inline',
			'foreign_table'=>'tx_commerce_attributes_prices',
			'foreign_field'=>'uid_attribute_value',
			'foreign_label'=>'price_gross',
		),
	),
	'tax' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_articles.tax',
		'l10n_mode' => 'exclude',
		'config' => Array (
			'type' => 'input',
			'size' => '30',
			'eval' => 'double2,nospace',
		)
	),
	'prices_text' => array (
		'exclude' => 1,
		'displayCond' => 'REC:NEW:true',
		'label' => 'Sie koennen erst einen Preis eingeben wenn der Wert gespeichert wurde. -> SET ME IN LOCALLANG',
		'config' => array (
			'type' => 'none',
		)
	),
);


t3lib_div::loadTCA("tx_commerce_attribute_values");
t3lib_extMgm::addTCAcolumns("tx_commerce_attribute_values",$attributesValuesColumns,1);
t3lib_extMgm::addToAllTCAtypes("tx_commerce_attribute_values","ordernumber_ext;;;;1-1-1,tax, prices, prices_text");


$attributesColumns = Array (
	"ordernumber_ext_prio" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:com_advanced_products/locallang_db.xml:tx_commerce_attributes.tx_comadvancedproducts_ordernumber_ext_prio",		
		"config" => Array (
			"type"     => "input",
			"size"     => "4",
			"max"      => "4",
			"eval"     => "int",
			"checkbox" => "0",
			"range"    => Array (
				"upper" => "1000",
				"lower" => "1"
			),
			"default" => 1
		)
	),
);

t3lib_div::loadTCA("tx_commerce_attributes");
t3lib_extMgm::addTCAcolumns("tx_commerce_attributes",$attributesColumns,1);
t3lib_extMgm::addToAllTCAtypes("tx_commerce_attributes","ordernumber_ext_prio");


$productsColumns = Array (
	'ordernumber' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:com_advanced_products/locallang_db.xml:tx_commerce_products.tx_comadvancedproducts_products_ordernr',
		'l10n_mode' => 'exclude',
		'config' => Array (
			'type' => 'input',
			'size' => '30',
			'max' => '80',
			'eval' => 'trim',
		)
	),
	'prices' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_articles.prices',
		'l10n_mode' => 'exclude',
		'config' => array (
			'type' => 'inline',
			'foreign_table'=>'tx_commerce_products_prices',
			'foreign_field'=>'uid_product',
			'foreign_label'=>'price_gross',
		),
	),
	'prices_text' => array (
		'exclude' => 1,
		'displayCond' => 'REC:NEW:true',
		'label' => 'Sie koennen erst einen Preis eingeben wenn das Produkt gespeichert wurde. -> SET ME IN LOCALLANG',
		'config' => array (
			'type' => 'none',
		)
	),
	'tax' => Array (
		'exclude' => 1,
		'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_articles.tax',
		'l10n_mode' => 'exclude',
		'config' => Array (
			'type' => 'input',
			'size' => '30',
			'eval' => 'double2,nospace',
		)
	),
);

t3lib_div::loadTCA("tx_commerce_products");
t3lib_extMgm::addTCAcolumns("tx_commerce_products",$productsColumns,1);
t3lib_extMgm::addToAllTCAtypes("tx_commerce_products","--div--;LLL:EXT:com_advanced_products/locallang_db.xml:tx_commerce_products.tx_comadvancedproducts_article_options,ordernumber;;;;1-1-1, tax, prices,prices_text",0,"after:relatedpage");



$TCA['tx_commerce_attributes_prices'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_article_prices',
		'label' => 'price_net',
		'label_alt' => 'price_net,price_gross',
		'label_alt_force' => 1,
		'label_userFunc' => 'tx_commerce_article_price->getTCARecordTitle',
	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'versioning' => '1',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_missing.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, price_gross, price_net, price_scale_amount, purchase_price',
	)
);

$TCA["tx_commerce_products_prices"] = array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_article_prices',
		'label' => 'price_net',
		'label_alt' => 'price_net,price_gross',
		'label_alt_force' => 1,
		'label_userFunc' => 'tx_commerce_article_price->getTCARecordTitle',
	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'versioning' => '1',
		'languageField' => 'sys_language_uid',
		'transOrigPointerField' => 'l18n_parent',
		'transOrigDiffSourceField' => 'l18n_diffsource',
		'default_sortby' => 'ORDER BY crdate',
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
			'starttime' => 'starttime',
			'endtime' => 'endtime',
			'fe_group' => 'fe_group',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_missing.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, price_gross, price_net, price_scale_amount, purchase_price',
	)
);


// Only perform from TCA if the BE form is called the first time ('First time' also means
// calling the editform of an attribute), no data has to be saved and extension dynaflex is
// available (of course!)
$postEdit = t3lib_div::_GP('edit');
$postData = t3lib_div::_GP('data');
/*
//if (((is_array($postEdit['tx_commerce_attribute_values']) && !in_array('new',$postEdit['tx_commerce_attribute_values'])) || (is_array($postEdit['tx_commerce_products']) &&  !in_array('new',$postEdit['tx_commerce_products']))) &&
if ((is_array($postEdit['tx_commerce_attribute_values']) || is_array($postEdit['tx_commerce_attributes']) || is_array($postEdit['tx_commerce_products'])) &&
	//$postData == NULL && 
	t3lib_extMgm::isLoaded('dynaflex')
	)	{

			// Load the configuration from a file
			if(is_array($postEdit['tx_commerce_attribute_values']) || is_array($postEdit['tx_commerce_attributes'])) {
				require_once(t3lib_extMgm::extPath('com_advanced_products') .'ext_df_attribute_values_config.php');
				$dynaFlexConf = $attributeValuesDynaFlexConf;
				$dynaFlexConf['workingTable'] = 'tx_commerce_attribute_values';
			} elseif (is_array($postEdit['tx_commerce_products'])) {
				require_once(t3lib_extMgm::extPath('com_advanced_products') .'ext_df_products_config.php');
				$dynaFlexConf = $productDynaFlexConf;
				$dynaFlexConf['workingTable'] = 'tx_commerce_products';
			}
			
			// And start the dynaflex processing
			require_once(t3lib_extMgm::extPath('dynaflex') .'class.dynaflex.php');
			$dynaflex = t3lib_div::makeInstance('dynaflex');
			
				 //debug($GLOBALS['TCA']['tx_commerce_attribute_values']);
			$dynaflex->init($GLOBALS['TCA'], $dynaFlexConf);

			// process DCA and read dataStructArray from index 0
			$GLOBALS['TCA'] = $dynaflex->getDynamicTCA();
			//debug($GLOBALS['TCA']['tx_commerce_products']['columns']['prices']['config']['ds']);
			
			$dataStructArray = $dynaflex->dataStructArray[0];

			// at last cleanup the XML structure in the database
			//$dynaflex->doCleanup('prices');
}
*/
t3lib_extMgm::addStaticFile($_EXTKEY,'static/', '');
?>
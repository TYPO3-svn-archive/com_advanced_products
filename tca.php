<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_commerce_attributes_prices'] = Array (
	'ctrl' => $TCA['tx_commerce_attributes_prices']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group,price_gross,price_net,purchase_price',
	),
	'feInterface' => $TCA['tx_commerce_attribute_values']['feInterface'],
	'columns' => Array (
		'sys_language_uid' => Array (
			'exclude' => 1,
			'label_alt' => 'price_net,price_gross,purchase_price',
			'label_alt_force' => 1,
		//	'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'label' => 'price_gross',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => Array(
					Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_commerce_attributes_prices',
				'foreign_table_where' => 'AND tx_commerce_attributes_prices.pid=###CURRENT_PID### AND tx_commerce_attributes_prices.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'starttime' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'default' => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0',
				'range' => Array (
					'upper' => mktime(0,0,0,12,31,2020),
					'lower' => mktime(0,0,0,date('m')-1,date('d'),date('Y'))
				)
			)
		),
		'fe_group' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
					Array('LLL:EXT:lang/locallang_general.php:LGL.hide_at_login', -1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.any_login', -2),
					Array('LLL:EXT:lang/locallang_general.php:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'price_gross' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_attributes_prices.price_gross',
			'l10n_mode' => 'exclude',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'double2,nospace',
			)
		),
		'price_net' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_attributes_prices.price_net',
			'l10n_mode' => 'exclude',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'double2,nospace',
			)
		),
		
		'purchase_price' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_attributes_prices.purchase_price',
			'l10n_mode' => 'exclude',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'double2,nospace',
			)
		),
		'price_scale_amount_start' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_articles.price_scale_amount_start',
			'l10n_mode' => 'exclude',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'eval' => 'int,nospace,required',
				'range' => array('lower' => 1),
				'default' => '1',
			)
		),
		'price_scale_amount_end' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_articles.price_scale_amount_end',
			'l10n_mode' => 'exclude',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'eval' => 'int,nospace,required',
				'range' => array('lower' => 1),
				'default' => '1',
			)
		),
		'purchase_price' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_attributes_prices.purchase_price',
			'l10n_mode' => 'exclude',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'double2,nospace',
			)
		),
		'uid_article' => Array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'label' => 'ArtcleValue UID',
			'config' => array (
				'type' => 'user',
				'userFunc' => 'tx_commerce_attributeValues->attributeValueUid',
			),
		),
	),
	'types' => Array (
		'0' => Array('showitem' => '
			sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, price_gross, price_net, price_scale_amount, purchase_price;;;;3-3-3, uid_article'),
	),
	'palettes' => Array (
		'1' => Array('showitem' => 'starttime, endtime, fe_group')
	)
	
);

$TCA["tx_commerce_products_prices"] = array (
	"ctrl" => $TCA["tx_commerce_products_prices"]["ctrl"],
	'interface' => Array (
		'showRecordFieldList' => 'sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group,price_gross,price_net,purchase_price',
	),
	'feInterface' => $TCA['tx_commerce_products']['feInterface'],
	'columns' => Array (
		'sys_language_uid' => Array (
			'exclude' => 1,
			'label_alt' => 'price_net,price_gross,purchase_price',
			'label_alt_force' => 1,
		//	'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'label' => 'price_gross',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => Array(
					Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_commerce_products_prices',
				'foreign_table_where' => 'AND tx_commerce_products_prices.pid=###CURRENT_PID### AND tx_commerce_products_prices.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'starttime' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.starttime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'default' => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.endtime',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0',
				'range' => Array (
					'upper' => mktime(0,0,0,12,31,2020),
					'lower' => mktime(0,0,0,date('m')-1,date('d'),date('Y'))
				)
			)
		),
		'fe_group' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.fe_group',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
					Array('LLL:EXT:lang/locallang_general.php:LGL.hide_at_login', -1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.any_login', -2),
					Array('LLL:EXT:lang/locallang_general.php:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'price_gross' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_products_prices.price_gross',
			'l10n_mode' => 'exclude',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'double2,nospace',
			)
		),
		'price_net' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_products_prices.price_net',
			'l10n_mode' => 'exclude',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'double2,nospace',
			)
		),
		
		'purchase_price' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_products_prices.purchase_price',
			'l10n_mode' => 'exclude',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'double2,nospace',
			)
		),
		'price_scale_amount_start' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_articles.price_scale_amount_start',
			'l10n_mode' => 'exclude',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'eval' => 'int,nospace,required',
				'range' => array('lower' => 1),
				'default' => '1',
			)
		),
		'price_scale_amount_end' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_articles.price_scale_amount_end',
			'l10n_mode' => 'exclude',
			'config' => Array (
				'type' => 'input',
				'size' => '10',
				'eval' => 'int,nospace,required',
				'range' => array('lower' => 1),
				'default' => '1',
			)
		),
		'purchase_price' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:commerce/locallang_db.xml:tx_commerce_products_prices.purchase_price',
			'l10n_mode' => 'exclude',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'double2,nospace',
			)
		),
		'uid_product' => Array(
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'label' => 'Product UID',
			'config' => array (
				'type' => 'user',
				'userFunc' => 'tx_commerce_products_products->productUid',
			),
		),
	),
	'types' => Array (
		'0' => Array('showitem' => '
			sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, price_gross, price_net, price_scale_amount, purchase_price;;;;3-3-3, uid_article'),
	),
	'palettes' => Array (
		'1' => Array('showitem' => 'starttime, endtime, fe_group')
	)
);


?>
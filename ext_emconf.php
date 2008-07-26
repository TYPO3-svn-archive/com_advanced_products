<?php

########################################################################
# Extension Manager/Repository config file for ext: "com_advanced_products"
#
# Auto generated 25-07-2008 12:57
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Commerce: Extended products',
	'description' => 'Extend the product and article with functions to set e.g. the Price and the item-nr for all Articles in the product and extend them with Attribute Prices',
	'category' => 'be',
	'author' => 'Sascha Egerer',
	'author_email' => 'seg@softvision.de',
	'shy' => '',
	'dependencies' => 'commerce',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author_company' => '',
	'version' => '0.8.2',
	'constraints' => array(
		'depends' => array(
			'commerce' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:19:{s:9:"ChangeLog";s:4:"cecf";s:10:"README.txt";s:4:"ee2d";s:50:"class.tx_com_advanced_products_attributevalues.php";s:4:"150f";s:43:"class.tx_com_advanced_products_products.php";s:4:"dcdf";s:34:"ext_df_attribute_values_config.php";s:4:"8210";s:26:"ext_df_products_config.php";s:4:"ec4d";s:12:"ext_icon.gif";s:4:"4716";s:17:"ext_localconf.php";s:4:"fba0";s:14:"ext_tables.php";s:4:"6ead";s:14:"ext_tables.sql";s:4:"3c88";s:16:"icon_missing.gif";s:4:"475a";s:16:"locallang_db.xml";s:4:"32a2";s:7:"tca.php";s:4:"6e95";s:65:"lib_hooks/class.tx_com_advanced_products_article_itemnr_hooks.php";s:4:"8c2c";s:64:"lib_hooks/class.tx_com_advanced_products_article_price_hooks.php";s:4:"de3b";s:20:"static/constants.txt";s:4:"d41d";s:16:"static/setup.txt";s:4:"d41d";s:51:"be_hooks/class.tx_com_advanced_products_dmhooks.php";s:4:"e22f";s:14:"doc/manual.sxw";s:4:"d0ef";}',
	'suggests' => array(
	),
);

?>
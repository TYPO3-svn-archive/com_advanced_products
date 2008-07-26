<?php

$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:com_advanced_products/be_hooks/class.tx_com_advanced_products_dmhooks.php:tx_com_advanced_products_dmhooks';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['commerce/lib/class.tx_commerce_pibase.php']['articleMarker'][] = 'EXT:com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_itemnr_hooks.php:tx_com_advanced_products_article_itemnr_hooks';

//Hook for ordernumber
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['commerce/lib/class.tx_commerce_article.php']['postinit'][] = 'EXT:com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_itemnr_hooks.php:tx_com_advanced_products_article_itemnr_hooks';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['commerce/lib/class.tx_commerce_article_price.php']['postpricenet'][] = 'EXT:com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_price_hooks.php:tx_com_advanced_products_article_price_hooks';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['commerce/lib/class.tx_commerce_article_price.php']['postpricegross'][] = 'EXT:com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_price_hooks.php:tx_com_advanced_products_article_price_hooks';
//Hook for tax
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['commerce/lib/class.tx_commerce_article.php']['postinit'][] = 'EXT:com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_price_hooks.php:tx_com_advanced_products_article_price_hooks';

?>
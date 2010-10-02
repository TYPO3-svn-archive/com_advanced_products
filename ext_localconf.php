<?php
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:com_advanced_products/be_hooks/class.tx_com_advanced_products_tcehooksHandler.php:tx_com_advanced_products_tcehooksHandler';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:com_advanced_products/be_hooks/class.tx_com_advanced_products_dmhooks.php:tx_com_advanced_products_dmhooks';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getSingleFieldClass'][] = 'EXT:com_advanced_products/be_hooks/class.tx_com_advanced_products_tceforms_hooks.php:tx_com_advanced_products_tceforms_hooks';

$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:com_advanced_products/be_hooks/class.tx_com_advanced_products_dmhooks.php:tx_com_advanced_products_dmhooks';
$TYPO3_CONF_VARS['EXTCONF']['commerce/lib/class.tx_commerce_pibase.php']['articleMarker'][] = 'EXT:com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_itemnr_hooks.php:tx_com_advanced_products_article_itemnr_hooks';
$TYPO3_CONF_VARS['EXTCONF']['commerce/lib/class.tx_commerce_pibase.php']['articleview'][] = 'EXT:com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_itemnr_hooks.php:tx_com_advanced_products_article_itemnr_hooks';

//Hook for ordernumber
$TYPO3_CONF_VARS['EXTCONF']['commerce/lib/class.tx_commerce_article.php']['post_loaddata'][] = 'EXT:com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_itemnr_hooks.php:tx_com_advanced_products_article_itemnr_hooks';
$TYPO3_CONF_VARS['EXTCONF']['commerce/lib/class.tx_commerce_article_price.php']['postpricenet'][] = 'EXT:com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_price_hooks.php:tx_com_advanced_products_article_price_hooks';
$TYPO3_CONF_VARS['EXTCONF']['commerce/lib/class.tx_commerce_article_price.php']['postpricegross'][] = 'EXT:com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_price_hooks.php:tx_com_advanced_products_article_price_hooks';
$TYPO3_CONF_VARS['EXTCONF']['commerce/lib/class.tx_commerce_article_price.php']['postinit'][] = 'EXT:com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_price_hooks.php:tx_com_advanced_products_article_price_hooks';

//Hook for tax
$TYPO3_CONF_VARS['EXTCONF']['commerce/lib/class.tx_commerce_article.php']['postinit'][] = 'EXT:com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_itemnr_hooks.php:tx_com_advanced_products_article_itemnr_hooks';

$TYPO3_CONF_VARS['EXTCONF']['commerce/pi2/class.tx_commerce_pi2.php']['makeProductList'][] = 'EXT:com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_itemnr_hooks.php:tx_com_advanced_products_article_itemnr_hooks';
$TYPO3_CONF_VARS['EXTCONF']['commerce/lib/class.tx_commerce_pibase.php']['makeLineView'][] = 'EXT:com_advanced_products/lib_hooks/class.tx_com_advanced_products_article_itemnr_hooks.php:tx_com_advanced_products_article_itemnr_hooks';

$TYPO3_CONF_VARS['EXTCONF']['commerce/lib/class.tx_commerce_basic_basket.php']['addArticle'][] = 'EXT:com_advanced_products/lib_hooks/class.tx_com_advanced_products_basic_basket_hooks.php:tx_com_advanced_products_basic_basket_hooks';


?>
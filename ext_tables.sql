#
# Table structure for table 'tx_commerce_attributes'
#
CREATE TABLE tx_commerce_attributes (
	ordernumber_ext_prio int(11) DEFAULT '0' NOT NULL
);

#
# Table structure for table 'tx_commerce_attribute_values'
#
CREATE TABLE tx_commerce_attribute_values (
	ordernumber_ext tinytext NOT NULL,
	prices mediumtext NOT NULL,
	tax double(4,2) DEFAULT '0.00' NOT NULL
);

#
# Table structure for table 'tx_commerce_attributes_prices'
#
CREATE TABLE tx_commerce_attributes_prices (
    uid int(11) DEFAULT '0' NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    t3ver_oid int(11) DEFAULT '0' NOT NULL,
    t3ver_id int(11) DEFAULT '0' NOT NULL,
    t3ver_label varchar(30) DEFAULT '' NOT NULL,
    sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l18n_parent int(11) DEFAULT '0' NOT NULL,
    l18n_diffsource mediumblob NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    starttime int(11) DEFAULT '0' NOT NULL,
    endtime int(11) DEFAULT '0' NOT NULL,
    fe_group int(11) DEFAULT '0' NOT NULL,
    
    uid_attribute_value int(11) DEFAULT '0' NOT NULL,
    price_net int(11) DEFAULT '0',
    price_gross int(11) DEFAULT '0',
    purchase_price int(11) DEFAULT '0',
    price_scale_amount_start int(11) DEFAULT '1',
    price_scale_amount_end int(11) DEFAULT '1',
    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY lang (sys_language_uid),
    KEY langpar (l18n_parent),
    KEY uattributevalue (uid_attribute_value)
);


#
# Table structure for table 'tx_commerce_products_prices'
#
CREATE TABLE tx_commerce_products_prices (
    uid int(11) DEFAULT '0' NOT NULL auto_increment,
    pid int(11) DEFAULT '0' NOT NULL,
    tstamp int(11) DEFAULT '0' NOT NULL,
    crdate int(11) DEFAULT '0' NOT NULL,
    cruser_id int(11) DEFAULT '0' NOT NULL,
    t3ver_oid int(11) DEFAULT '0' NOT NULL,
    t3ver_id int(11) DEFAULT '0' NOT NULL,
    t3ver_label varchar(30) DEFAULT '' NOT NULL,
    sys_language_uid int(11) DEFAULT '0' NOT NULL,
    l18n_parent int(11) DEFAULT '0' NOT NULL,
    l18n_diffsource mediumblob NOT NULL,
    deleted tinyint(4) DEFAULT '0' NOT NULL,
    hidden tinyint(4) DEFAULT '0' NOT NULL,
    starttime int(11) DEFAULT '0' NOT NULL,
    endtime int(11) DEFAULT '0' NOT NULL,
    fe_group int(11) DEFAULT '0' NOT NULL,
    
    uid_product int(11) DEFAULT '0' NOT NULL,
    price_net int(11) DEFAULT '0',
    price_gross int(11) DEFAULT '0',
    purchase_price int(11) DEFAULT '0',
    price_scale_amount_start int(11) DEFAULT '1',
    price_scale_amount_end int(11) DEFAULT '1',
    PRIMARY KEY (uid),
    KEY parent (pid),
    KEY lang (sys_language_uid),
    KEY langpar (l18n_parent),
    KEY uproduct (uid_product)
);


#
# Table structure for table 'tx_commerce_products'
#
CREATE TABLE tx_commerce_products (
	tax double(4,2) DEFAULT '0.00' NOT NULL,
	prices mediumtext NOT NULL,
	ordernumber varchar(80) DEFAULT '' NOT NULL
);
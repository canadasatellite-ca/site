[canadasatellite.ca](https://www.canadasatellite.ca) (Magento 2).

## How to upgrade Mage2.PRO packages
```
sudo service cron stop           
bin/magento maintenance:enable  
composer remove mage2pro/core
composer clear-cache
composer require mage2pro/core:*    
rm -rf var/di var/generation generated/*
bin/magento setup:upgrade
bin/magento cache:enable
bin/magento setup:di:compile
bin/magento cache:clean
rm -rf pub/static/*
bin/magento setup:static-content:deploy \
	--area adminhtml \
	--theme Magento/backend \
	-f en_US en_CA es_ES fr_FR pt_BR zh_Hans_CN
bin/magento setup:static-content:deploy \
	--area frontend \
	--theme MageSuper/magestylish \
	-f en_US en_CA es_ES fr_FR pt_BR zh_Hans_CN
bin/magento cache:clean
bin/magento maintenance:disable
sudo service cron start
rm -rf var/log/*
```

## How to deploy the static content
### On localhost
```posh
bin/magento cache:clean
rm -rf pub/static/*
bin/magento setup:static-content:deploy \
	--area adminhtml \
	--theme Magento/backend \
	-f en_US
bin/magento setup:static-content:deploy \
	--area frontend \
	--theme MageSuper/magestylish \
	-f en_US en_CA
bin/magento cache:clean
```
### On the production server
```
sudo service cron stop           
bin/magento maintenance:enable      
rm -rf var/di var/generation generated/*
bin/magento setup:upgrade
bin/magento cache:enable
bin/magento setup:di:compile
bin/magento cache:clean
rm -rf pub/static/*
bin/magento setup:static-content:deploy \
	--area adminhtml \
	--theme Magento/backend \
	-f en_US en_CA es_ES fr_FR pt_BR zh_Hans_CN
bin/magento setup:static-content:deploy \
	--area frontend \
	--theme MageSuper/magestylish \
	-f en_US en_CA es_ES fr_FR pt_BR zh_Hans_CN
bin/magento cache:clean
bin/magento maintenance:disable
service cron start
rm -rf var/log/*
```

## How to restart services on the production server
```
service cron restart
service mysql restart
service nginx restart
service php7.2-fpm restart
service rabbitmq-server restart
service redis restart
```
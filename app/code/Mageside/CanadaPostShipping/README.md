Magento 2 Canada Post Shipping by Mageside
==========================================

####Support
    v1.6.0 - Magento 2.1.* - 2.3.*

####Change list
    v1.6.0 - Added autofilling registration information.
    v1.5.1 - Added support Magento 2.3.
    v1.5.0 - Added logging api requests. Updated delivery options configuration.
    v1.4.5 - Fixed manifests listings data sorting.
    v1.4.4 - Added "Rates Minimal Price" and "Free Methods Threshold Price" settings.
    v1.4.3 - Fixed tracking dateTime formatting.
    v1.4.1 - Added Non Mailable attribute with functionality.
    v1.4.0 - Added non-delivery handling settings per shipping method.
             Added settings for adding several free methods.
    v1.3.4 - Fixed convertMeasureWeight by locale (only 'en_US' is supported)
    v1.3.3 - Fixed total parcel weight for international delivery, added checking conflict options,
             fixed create packages template (preset store measure units).
    v1.3.1 - Fixed getShipmentPrice error for non contract users.
    v1.3.0 - Added 'void shipment', 'transmit offline', 'cost shipment'.
    v1.2.5 - Fixed error with soap http version.
    v1.2.4 - Updated calculation of expected-mailing-date by working days.
    v1.2.3 - Added option to choose retrieved rates price type (included/excluded tax).
    v1.2.1 - Fixed issue with lowercase postcode.
    v1.2.0 - Added mass print manifest labels.
    v1.1.1 - Improved delivery to post office service performance.
    v1.1.0 - Added delivery to post office service.
    v1.0.2 - Added estimated delivery date to rating titles. Added french translation.
    v1.0.1 - Fixed carrier validation weight.
    v1.0.0 - Start project

####Installation
    1. Download the archive.
    2. Unzip the content of archive, use command 'unzip ArchiveName.zip'. Now you have folder with name 'CanadaPostShipping'.
    3. Make sure to create the directory structure in your Magento - 'Magento_Root/app/code/Mageside'.
    4. Drop/move the unzipped folder to directory 'Magento_Root/app/code/Mageside'.
    5. Run the command 'php bin/magento module:enable Mageside_CanadaPostShipping' in Magento root. 
       If you need to clear static content use 'php bin/magento module:enable --clear-static-content Mageside_CanadaPostShipping'.
    6. Run the command 'php bin/magento setup:upgrade' in Magento root.
    7. Run the command 'php bin/magento setup:di:compile' if you have a single website and store, 
       or 'php bin/magento setup:di:compile-multi-tenant' if you have multiple ones.
    8. Clear cache: 'php bin/magento cache:clean', 'php bin/magento cache:flush'

# Command

### Description

The Magento 2 module add custom commads

### Installation
~~~bash
cd <magento_root>
composer config repositories.swissup composer https://swissup.github.io/packages/
composer require swissup/command:dev-master --prefer-source
bin/magento module:enable  Swissup_Core Swissup_Command
bin/magento setup:upgrade
~~~

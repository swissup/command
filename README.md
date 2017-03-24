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

### Usage
#### Show module output status
~~~bash
 bin/magento module:output:status -h 
Usage:
 module:output:status
~~~

#### Show configuration value(s)
~~~bash
bin/magento config:option:show -h                    
Usage:
 config:option:show [--option="..."] [--website[="..."]] [--store[="..."]]
 
Options:
 --option              Option (example: admin/captcha/enable) (default: "admin/captcha/enable")
 --website             Website (example: 0) (default: 0)
 --store               Store code (example: 0) (default: 0)

~~~

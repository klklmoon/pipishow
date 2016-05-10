#!/bin/bash
phpunit --testdox-html /webservice/webdoc/pipishow/applications/tests/report/test.html --testdox-text /webservice/webdoc/pipishow/applications/tests/report/test.txt --colors --stop-on-error --stop-on-failure --strict --no-configuration /webservice/webdoc/pipishow/applications/tests/phpunit.php

#需要覆盖性测试的可以运行下面的命令
#这里需要首先vim /webservice/server/php/etc/php.d/xdebug.ini
#把xdebug.profiler_enable = off，把这个关掉，生成report大概1分钟左右，否则大概需要3个小时以上才会生成report
#phpunit --testdox-html /webservice/webdoc/letian/applications/tests/report/test.html --testdox-text /webservice/webdoc/letian/applications/tests/report/test.txt --colors --stop-on-error --stop-on-failure --strict --coverage-html /webservice/webdoc/letian/applications/tests/report/coverage /webservice/webdoc/letian/applications/tests/phpunit.php

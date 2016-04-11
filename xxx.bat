@xcopy src src2 /i /s /q > NUL

php --version
@echo ================================================================================

wmic cpu get name
@echo ================================================================================

php php-cs-fixer_v2.phar fix --config=php-cs-fixer_v2.php_cs
@echo --------------------------------------------------------------------------------
php php-cs-fixer_v2.phar fix --config=php-cs-fixer_v2.php_cs
@rm .php_cs.cache.v2

@echo ================================================================================

php php-cs-fixer_v1.phar fix --config-file=php-cs-fixer_v1.php_cs
@echo --------------------------------------------------------------------------------
php php-cs-fixer_v1.phar fix --config-file=php-cs-fixer_v1.php_cs
@rm .php_cs.cache

@rmdir /S /Q src2

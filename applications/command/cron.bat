@echo off

rem -------------------------------------------------------------
rem  Pipi command line script for Windows.
rem
rem  This is the bootstrap script for running yiic on Windows.
rem
rem  @author Su Qian <pipi@suqian.cn>
rem  @link http://show.pipi.com/
rem  @copyright Copyright &copy; 2008 pipi.cn
rem  @license http://show.pipi.cn/
rem  @version $Id: cron.bat 8317 2013-03-29 01:19:47Z suqian $
rem -------------------------------------------------------------

@setlocal

set PIPI_PATH=%~dp0
if "%PHP_COMMAND%" == "" set PHP_COMMAND=php.exe

"%PHP_COMMAND%" "%PIPI_PATH%cron.php" %*

@endlocal
@echo off
echo ---Deploying site

REM ---Deploy the wwwroot folder in repository to custom target (wwwroot\lab01)
robocopy %DEPLOYMENT_SOURCE% %DEPLOYMENT_TARGET%\lab01 index.php /IS
exit
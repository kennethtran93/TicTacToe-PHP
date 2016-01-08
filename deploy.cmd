@echo off
echo ---Deploying site

REM ---Deploy the wwwroot folder in repository to custom target (wwwroot\lab01)
xcopy %DEPLOYMENT_SOURCE%  %DEPLOYMENT_TARGET%\lab01 /exclude:*.deployment+*.gitignore+*.md+*.cmd /Y /s 
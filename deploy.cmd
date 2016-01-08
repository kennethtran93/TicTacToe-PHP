@echo off
echo ---Deploying site

REM ---Deploy the wwwroot folder in repository to custom target (wwwroot\lab01)
xcopy %DEPLOYMENT_SOURCE% /exclude:.deployment+.gitignore+readme.md+deploy.cmd %DEPLOYMENT_TARGET%\lab01 /Y /s 
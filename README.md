# a4850-lab01
### PHP Tic Tac Toe by Ken

Everything is contained inside the index.php file.

The Game class contains everything needed to run the game.  Simply initialize/instantiate the class inside the `<body>` tag to begin.

FEATURES:
* Single-Player Mode Only
* Decent AI logical programming
* Player goes first
* Decent sized game board
* Reset / Start New Game button
* Decent line checks
* Error Checking (includes, but not limited to...)
  * Invalid character value(s) in game board URL
  * Invalid character string length in game board URL
  * Too many game pieces of one side on the game board

PLANNED:
* Ability to choose whether playing two player locally with a friend or with the AI
* Win line emphasized (highlighted)
* Further code optimization
* Perhaps extract all the in-line CSS into its own css file...maybe.

_I've also have this repository set to continuously deploy to Azure, hence there are these additional files:_
* _.deployment_
* _deploy.cmd_
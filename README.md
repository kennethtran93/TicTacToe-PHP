### PHP Tic Tac Toe by Ken

*This was created for ACIT4850 - Lab01 @ [BCIT - British Columbia Institute of Technology](http://www.bcit.ca)*

Everything is contained inside the index.php file.

The Game class contains everything needed to run the game.  Simply initialize/instantiate the class inside the `<body>` tag to begin.

NEW in v1.6:
* FEATURE:  Global Game Stats (Win/Lose/Tie/Total) per board size

FEATURES:
* Single-Player Mode Only
* Decent AI logical programming
* Player goes first
* Decent sized game board
* Ability to choose the grid size (ie 3x3, 5x5, etc. up to 15x15)
* Reset / Start New Game button
* Decent line checks
* Winning line emphasized (highlighted), and non-winning squares faded.
* Error Checking (includes, but not limited to...)
  * Invalid character value(s) in game board URL
  * Invalid character string length in game board URL
  * Too many game pieces of one side on the game board

PLANNED:
* Ability to choose whether playing two player locally with a friend or with the AI
* Perhaps extract all the in-line CSS into its own css file...maybe.

_I've also have this repository set to continuously deploy to Azure, hence there are these additional files:_
* _.deployment_
* _deploy.cmd_

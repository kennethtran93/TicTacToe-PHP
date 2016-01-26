<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8" name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Ken's Tic Tac Toe</title>
    </head>
    <body>
        <!-- Inline HTML css to prevent selection of text -->
        <div style="-moz-user-select: none; -webkit-user-select:none; -ms-user-select:none; user-select:none; -moz-user-drag:none; -webkit-user-drag:none; -ms-user-drag:none; user-drag: none;" unselectable="on">
            <p>Welcome to Ken's PHP Tic Tac Toe game.</p>
            <?php
            // Initialize PHP Tick Tac Toe Game Engine
            new Game();
            ?>
        </div>
    </body>
</html>

<?php

/**
 * This Tic Tac Toe Game class will handle everything.  Simply initialize it above with "new Game()";
 */
class Game {

    var $position;                       // The Game Board Array
    var $board        = '---------';     // The Game Board from URL.  Default is an Empty Board.
    var $debug        = false;           // Secret debug mode :)
    var $valid_char   = ['x', 'o', '-']; // Valid Game Piece Characters.
    var $invalid_char;                   // Array for invalid characters.
    var $grid_size    = 3;               // The game board grid size.
    var $winning_line = [];              // The winning line.
    var $win_lines    = [];                 // All possible win lines from game board

    /**
     * Class Constructor.
     * 
     * Gets and sets the Board size and board pieces
     */

    function __construct() {
        // Checks if the game size variable 'size' exists.
        if (isset($_GET['size'])) {
            // Take it from URL.  Trim whitespaces.
            $this->grid_size = trim($_GET['size']);
            $this->board     = str_repeat('-', pow($this->grid_size, 2));
        }

        // Checks if the game board variable 'board' exists.
        if (isset($_GET['board'])) {
            // The Game Board variable exists.
            // Check its length
            if (strlen(trim($_GET['board'])) == 0) {
                // Board variable exists, but contains no value.
                // Treat as new game.
                // This is redundant but just in case.
                $this->board = str_repeat('-', pow($this->grid_size, 2));
            } else {
                // Board variable exists, and contains some characters.
                // Take it from the URL.  Trim whitespaces and convert to lowercase.
                // Additional checks later on.
                $this->board = trim(strtolower($_GET['board']));
            }
        }

        $this->position = str_split($this->board); // From the board variable, convert into the board array.
        // DEBUG MODE
        if (isset($_GET['debug'])) {
            $this->debug = true; // Debug variable declared.  Enable Setting.
            echo 'Game is running in DEBUGGING Mode.  All Possible Winning Lines are displayed below, while the grid displays additional information.<br />';
        }

        // Generate all Winning Line Combinations
        $this->generate_win_lines();

        // Checks the game board and generate appropriate view.
        $this->game_check();
    }

    /**
     * This function generates all possible winning lines, based on the grid size given.
     * All Horizonal (row), Vertical (column), and Diagonal lines are stored relative to the box array position.
     * This is a multi-dimentional array, where each line is in its own array.
     * The array looks like this:  Line Type > Line > box in line.
     */
    function generate_win_lines() {
        $this->win_lines = []; // clear variable first just in case.
        // This is a special nested for loop system, where it does simultaneous win line generation for all three line types.
        // Iteration 1
        for ($a = 0; $a < $this->grid_size; $a++) {
            $horizontal = []; // temporary array
            $vertical   = []; // temporary array
            // Iteration 2
            for ($b = 0; $b < $this->grid_size; $b++) {
                // This will append to the temporary row array the position of the row.
                $horizontal[] = $this->grid_size * $a + $b;
                // This will append to the temporary column array the position of the column
                $vertical[]   = $this->grid_size * $b + $a;
            }
            // Add the generated row to the overall array.
            $this->win_lines['Horizontal']['Row ' . ($a + 1)]  = $horizontal;
            // Add the generated column to the overall array
            $this->win_lines['Vertical']['Column ' . ($a + 1)] = $vertical;
            // This will append to the overall array the position of the diagonals
            $this->win_lines['Diagonal']['backslash'][]        = $this->grid_size * $a + $a;
            // This will append to the overall array the position of the diagonals
            $this->win_lines['Diagonal']['forward slash'][]    = $this->grid_size * ($a + 1) - ($a + 1);
        }
        // For Testing/Debugging purposes.
        if ($this->debug) {
            // Create Table
            echo '<br /><table border = "1" style="border-collapse: collapse">';
            // Table Header
            echo '<caption>All Winning Line Combinations</caption>';
            // Column Header
            echo '<thead><tr>';
            foreach ($this->win_lines as $line_type => $lines) {
                echo '<th>' . $line_type . '</th>';
            }
            echo '</tr></thead>'; // End Column Header
            // Table Body
            echo '<tr>'; // First and only row
            foreach ($this->win_lines as $line_type) {
                echo '<td><div style="padding:8px;">';
                foreach ($line_type as $line => $pos) {
                    // Prints each line in its own 'row'
                    echo $line . ': [' . implode(',', $pos) . ']<br />';
                }
                echo '</div></td>';
            }
            echo '</tr>'; // End Row
            echo '</table>'; // End Table
        }
    }

    /**
     * This will go through the error checking and generate the appropriate page to display.
     */
    function game_check() {
        // This will check for any invalid characters in the game board values
        $this->invalid_char = array_diff($this->position, $this->valid_char);
        // Here is where our multitude of checks will be done.
        if ($this->grid_size % 2 == 0 || $this->grid_size < 3 || $this->grid_size > 15) {
            // Grid Size not odd or does not meet minimum/maximum size.
            // For the time being it must be odd so that diagonals can truly be corner to corner.
            $this->game_message('invalid-size');
        } else if (count($this->invalid_char, COUNT_RECURSIVE) > 0) {
            // An invalid character was found in the 'board' variable.
            // Display Error Message.
            $this->game_message('invalid-character');
        } else if (strlen($this->board) <> pow($this->grid_size, 2)) {
            // Invalid Game Board - Does not contain the exact character length for the game board.
            // Display Error Message
            $this->game_message('invalid-board');
        } else if ($this->board == str_repeat('-', pow($this->grid_size, 2))) {
            // This marks the start of a new game.
            $this->game_play(true);
            // Display New Game Message
            $this->game_message('new-game');
        } else if (substr_count($this->board, 'x') - substr_count($this->board, 'o') > 1) {
            // There are too many Xs found in the game board.
            // Disable game board links
            $this->game_play(false);
            // Display Error Message
            $this->game_message('too-many-x');
        } else if (substr_count($this->board, 'o') - substr_count($this->board, 'x') > 0) {
            // There are too many Os found in the game board.
            // Disable game board links
            $this->game_play(false);
            // Display Error Message
            $this->game_message('too-many-o');
        } else if ($this->win_check('x')) {
            // X as won the game.
            // Create a (small) game win file
            $this->game_file("win");
            // Disable game board links
            $this->game_play(false);
            // Display Win Message
            $this->game_message('x-win');
        } else if ($this->win_check('o')) {
            // O as won the game.
            // Create a (small) game lose file
            $this->game_file("lose");
            // Disable game board links
            $this->game_play(false);
            // Display Win Message
            $this->game_message('o-win');
        } else if (stristr($this->board, '-') === FALSE) {
            // All cells have been filled, and there are no winners.
            // Create a (small) game tie file
            $this->game_file("tie");
            // Disable game board links (no links should be generated either way, but just in case.)
            $this->game_play(false);
            // Display Tie Game Message
            $this->game_message('tie-game');
        } else {
            // At this point, it's time for the AI to make its move.
            $this->pick_move();
            // Check if the AI's move was the winning move.
            if ($this->win_check('o')) {
                // O as won the game.
                // Create a (small) game lose file
                $this->game_file("lose");
                // Disable game board links
                $this->game_play(false);
                // Display Win Message
                $this->game_message('o-win');
            } else {
                // Player's turn.
                $this->game_play(true);
                // Display turn message
                $this->game_message('ongoing-game');
            }
        }
    }

    /**
     * Displays the Game Board
     * 
     * @param boolean $link whether or not to generate links.  If game is over then set to false.
     */
    function game_play($link) {
        echo '<br />'; // Insert blank line.
        // Inform player of grid size and rules, only if grid size is greater than a 3x3.
        if ($this->grid_size > 3) {
            echo 'NOTE: This is an <strong>advanced ' . ($this->grid_size) . 'x' . ($this->grid_size) . '</strong> game board.<br />';
            echo 'To be considered a winner, you must <strong>claim an entire line</strong> - that is, <strong>' . $this->grid_size . '</strong> in a line to win.<br />';
            echo '<i>Diagonals are considered from corner to corner, crossing the middle box.</i><br /><br />';
        }
        // Change font and size for the HTML table
        echo '<font face = "courier" size = "5">';
        // starts the HTML table
        echo '<table cols = "' . ($this->debug ? $this->grid_size + 2 : $this->grid_size) . '" border = "1" style = "font-weight:bold; border-collapse: collapse">';
        // For Testing / Debugging, display column number headings.
        if ($this->debug) {
            echo '<thead><tr><th></th>';
            for ($col = 1; $col <= $this->grid_size; $col++) {
                echo '<th style="padding: 5px;"> Column ' . $col . '</th>';
            }
            echo '<th></th></tr></thead>';
            echo '<tfoot><tr><th></th>';
            for ($col = 1; $col <= $this->grid_size; $col++) {
                echo '<th> Column ' . $col . '</th>';
            }
            echo '<th></th></tr></tfoot>';
        }
        // opens the first row
        echo '<tbody><tr>';
        $row = 1;   // Debugging use only.
        // For Testing / Debugging, display row number heading.
        if ($this->debug) {
            echo '<th style="padding: 5px;">Row ' . $row . '</th>';
        }
        // Iterates through each of the game board cell
        for ($pos = 0; $pos < pow($this->grid_size, 2); $pos++) {
            // Whether or not to generate links
            if ($link) {
                // Generate the link
                echo $this->show_cell($pos);
            } else {
                // display final result with no links
                echo '<td style="text-align:center;' . (in_array($pos, $this->winning_line[0]) ? ' background-color: #90EE90;' : ' opacity: 0.5;' ) . '"><div style="padding: 1em;">' . $this->position[$pos] . ($this->debug ? ('<br /><span style="font-size:66%;">' . $pos . ':(' . $row . ',' . (($pos % $this->grid_size) + 1) . ')</span>') : '') . '</div></td>';
            }
            if (($pos + 1) % $this->grid_size == 0) {
                // For Testing / Debugging, display row number heading.
                if ($this->debug) {
                    echo '<th style="padding: 5px;">Row ' . $row++ . '</th>';
                }
                if (($pos + 1) != pow($this->grid_size, 2)) {
                    // Start new row
                    echo '</tr><tr>';
                    // For Testing / Debugging, display row number heading.
                    if ($this->debug) {
                        echo '<th style="padding: 5px;">Row ' . $row . '</th>';
                    }
                }
            }
        }
        // Closes the last row
        echo '</tr></tbody>';
        // Closes the HTML table
        echo '</table>';
        // Ends the font type and size change
        echo '</font>';
        // Separates the game board from the game status
        echo '<br /><hr />';
    }

    /**
     * Generates the HTML code for the cell.
     * 
     * @param int $which The cell number of the game board.
     * @return string the HTML code for that specific cell.
     */
    function show_cell($which) {
        // Retrive the token
        $token = $this->position[$which];
        // deal with the easy case
        if ($token <> '-') {
            // Game cell has already been taken.
            $player_board = str_split($this->board);  // Create temporary array to hold the game board.
            // Returns the HTML code.  If it's the cpu that's moved (the position value stored in the engine is not the same as from the URL link), visually colour it in.
            return '<td style="text-align:center;' . ($token != $player_board[$which] ? ' background-color: #FFA500;' : '' ) . '"><div style="padding: 1em;">' . $token . ($this->debug ? ('<br /><span style="font-size:66%;">' . $which . ':(' . ((int) ($which / $this->grid_size) + 1) . ',' . (($which % $this->grid_size) + 1) . ')</span>') : '') . '</div></td>';
        }
        // now the hard case
        $this->newposition         = $this->position;               // Copy original array
        $this->newposition[$which] = 'x';                           // this would be their move
        $move                      = implode($this->newposition);   // make a string from board array
        $link                      = '?size=' . $this->grid_size . '&board=' . $move . ($this->debug ? '&debug' : '');             // this is what we want the link to be
        // so return cell containing an anchor and showing a hyphen.
        // Also makes it so that you can click almost anywhere within that table cell.
        return '<td style="text-align:center;"><a href = "' . $link . '" style = "text-decoration: none;"><div style="padding: 1em;">-' . ($this->debug ? ('<br /><span style="font-size:66%;">' . $which . ':(' . ((int) ($which / $this->grid_size) + 1) . ',' . (($which % $this->grid_size) + 1) . ')</span>') : '') . '</div></a></td>';
    }

    /**
     * The Main Logic to how my AI would pick a move.
     */
    function pick_move() {
        // For Testing / Debugging Notice
        echo ($this->debug ? '<br />> The AI is making its move...<br />' : '');
        // Let's check if there's a winning move
        $ai_win_move = $this->win_check('o');
        if ($ai_win_move != -1) {
            // There is a winning move.  TAKE IT!
            $this->position[$ai_win_move] = 'o';
        } else {
            // There is not a winning move.
            // Let's check if there's a winning move for the player
            $player_win_move = $this->win_check('x');
            if ($player_win_move != -1) {
                // There is a winning move.  BLOCK IT!
                $this->position[$player_win_move] = 'o';
            } else {
                // There is not a winning move from either party.
                // Select random empty cell.
                $board = implode($this->position);
                // Let's try to take the middle cell first.
                $move  = round((pow($this->grid_size, 2) / 2), PHP_ROUND_HALF_ODD);
                // Loops until we have randomly selected an empty cell.
                while (substr($board, $move, 1) != '-') {
                    // Generate a random number
                    $move = rand(0, (pow($this->grid_size, 2) - 1));
                }
                // A random empty cell has been chosen.  Replace it.
                $new_board = substr_replace($board, 'o', $move, 1);

                // Regenerate the array to display where the AI moved.
                $this->position = str_split($new_board);
            }
        }
    }

    /**
     * This function will check for wins, either for the overall game or for the AI.
     * 
     * @param string $token This is usually x or o.
     * @return number/boolean Depending on the parent function that called this.
     *     (pick_move) The game cell number of the potential win, or -1 if it can't find one.
     *     (game_check) This will return whether or not there is a winning line.
     */
    function win_check($token) {
        // For Testing / Debugging Notice
        if ($this->debug && debug_backtrace()[1]['function'] == 'game_check') {
            echo '<br />> Check function called from Game for token ' . $token . '...<br />';
        }

        // If called from pick_move, check board for potential winning moves for token.
        // If called from game_check, check board for winning line for token
        $this->winning_line = []; // clear the variable first, just in case.
        // Iterating through all the possible winning lines.
        foreach ($this->win_lines as $line_type => $lines) {
            // Iterating through each line.
            foreach ($lines as $line_name => $line) {
                $this->winning_line[0] = $line; // Preemptively store winning line position
                $check_value           = 0;     // A temporary calculation variable.
                $win_move              = 0;     // The Game Board Cell location.
                // Checking each cell within a line
                foreach ($line as $pos) {
                    // For Debug / Testing
                    if ($this->debug && debug_backtrace()[1]['function'] == 'game_check') {
                        echo 'Checking for token ' . $token . ' in ' . $line_type . ' ' . $line_name . ' [' . implode(',', $line) . ']';
                    }
                    // Checks if the token matches what's currently in that game cell.
                    if ($this->position[$pos] != $token) {
                        // Token does not match checked position.
                        if (debug_backtrace()[1]['function'] == 'game_check') {
                            // For Testing / Debugging Notice

                            if ($this->debug) {
                                echo ' - Position ' . $pos . '.  Result:  Not Found.  Skipping rest of ' . $line_name . '<br />';
                            }
                            // Win Line Check Mode - no need to proceed further with this line.
                            break;
                        } else if (debug_backtrace()[1]['function'] == 'pick_move') {
                            // Perhaps a potential win cell.
                            $win_move = $pos;
                        }
                    } else {
                        // For Testing / Debugging Notice
                        if ($this->debug && debug_backtrace()[1]['function'] == 'game_check') {
                            echo ' - Position ' . $pos . '.  Result:  Found.<br />';
                        }
                        // It's a match.  Score one for that line.
                        $check_value++;
                    }
                }

                // Depending on the parent function that called this method...
                if (debug_backtrace()[1]['function'] == 'pick_move') {
                    // The AI's pick_move area
                    // If that line is just missing one of the same token...
                    if ($check_value == ($this->grid_size - 1)) {
                        // Check if the potential win cell is empty
                        if ($this->position[$win_move] == '-') {
                            // That cell is empty.  Take it for the win or block.
                            return $win_move;
                        }
                    }
                } else if (debug_backtrace()[1]['function'] == 'game_check') {
                    // The overall game for winning line.
                    if ($check_value == $this->grid_size) {
                        // For Testing / Debugging Notice
                        if ($this->debug) {
                            echo 'We have a winner!<br />';
                        }
                        // It's a winner.
                        return true;
                    }
                }
            }
        }
        // If we have reached this point, then there are no wins for that token.
        $this->winning_line = []; // Clear the winning_line variable.
        if (debug_backtrace()[1]['function'] == 'game_check') {
            // For the game_check function
            return false;
        } else if (debug_backtrace()[1]['function'] == 'pick_move') {
            // For the AI pick_move function.
            return -1;
        } else {
            // an unintended function called this...
            return null;
        }
    }

    /**
     * The Message displayed on the page, generally after the game board.
     * 
     * @param string $message The message to display
     */
    function game_message($message) {
        $newGame = true; // This is for the new game button
        // Display a specific message to the player.
        switch ($message) {
            case 'invalid-size':
                echo 'Invalid Game Board Size.  The board must be an <strong>odd number</strong> starting at 3 and up to 15 (for performance reasons).<br />';
                echo 'Your proposed game board size was <strong>' . $this->grid_size . '</strong>.<br />';
                echo 'Either fix the game board size variable values in the URL or Start a new game by clicking on the button below.';
                break;
            case 'invalid-board':
                echo 'Invalid game board. As the game board size is set to ' . $this->grid_size . ' by ' . $this->grid_size . ', please ensure the variable "board" contains exactly ' . pow($this->grid_size, 2) . ' characters.<br />';
                echo 'There are currently <strong>' . strlen($this->board) . '</strong> characters on the game board.<br />';
                echo 'Either fix the game board variable values in the URL or Start a new game by clicking on the button below.';
                break;
            case 'invalid-character':
                echo 'Invalid character(s) found in the variable "board".  Valid characters are <strong>x</strong>, <strong>o</strong>, and <strong>-</strong> (dash).<br />';
                echo 'The invalid value(s) entered for the "board" variable are as follows: ' . implode(', ', $this->invalid_char) . '<br />';
                echo 'Either fix the game board variable values in the URL or Start a new game by clicking on the button below.';
                break;
            case 'new-game':
                echo 'A new game as started. Since I am the game host, I\'ll let you start first.<br />';
                echo 'Click on a dash to mark that territory with an X flag!<br /><br />';
                echo 'Psst - if you want to change the game board size, click on one of the button(s) below:<br />';
                echo '<i>These button(s) are only available during a new game (before any boxes are marked).</i><br /><br />';
                if ($this->grid_size > 3) {
                    echo '<a draggable="false" href="' . $_SERVER['PHP_SELF'] . '?size=' . ($this->grid_size - 2) . ($this->debug ? '&debug' : '') . '" style="display: inline-block; -webkit-appearance: button; -moz-appearance: button; appearance: button; text-decoration: none; color: initial; padding: 0.5em;">I can\'t take it - DECREASE the board size (to ' . ($this->grid_size - 2) . 'x' . ($this->grid_size - 2) . ')!' . ($this->debug ? ' - in DEBUG mode' : '') . '</a>';
                    echo '<br />';
                    echo '<a draggable="false" href="' . $_SERVER['PHP_SELF'] . ($this->debug ? '&debug' : '') . '" style="display: inline-block; -webkit-appearance: button; -moz-appearance: button; appearance: button; text-decoration: none; color: initial; padding: 0.5em;">Back to the basics - RESET to a normal 3x3 board!' . ($this->debug ? ' - in DEBUG mode' : '') . '</a>';
                    echo '<br />';
                }
                if ($this->grid_size < 15) {
                    echo '<a draggable="false" href="' . $_SERVER['PHP_SELF'] . '?size=' . ($this->grid_size + 2) . ($this->debug ? '&debug' : '') . '" style="display: inline-block; -webkit-appearance: button; -moz-appearance: button; appearance: button; text-decoration: none; color: initial; padding: 0.5em;">It\'s not challenging enough - INCREASE the game board size (to ' . ($this->grid_size + 2) . 'x' . ($this->grid_size + 2) . ')!' . ($this->debug ? ' - in DEBUG mode' : '') . '</a>';
                } else {
                    echo '<br /><i>For performance reasons, the current max size of the board is 15x15.  Is this not challenging enough for you?</i>';
                }
                // For Testing / Debugging Notice

                if (!$this->debug) {
                    $newGame = false; // Do not display the New Game Button.
                }
                break;
            case 'too-many-x':
                echo 'ERROR DETECTED - There are too many Xs on the game board.  QUIT CHEATING!';
                break;
            case 'too-many-o':
                echo 'ERROR DETECTED - There are too many Os on the game board.<br />';
                echo 'Since my program logic does not allow me to cheat, you must have done something bad to the game board.';
                break;
            case 'x-win':
                echo '<strong>X (YOU) is the winner of this game.  Congratulations.</strong>';
                break;
            case 'o-win':
                echo '<strong>O (Ken\'s AI) is the winner of this game.  Care for a rematch?  You\'ll probably lose again...</strong>';
                break;
            case 'tie-game':
                echo '<strong>This game is TIED as there are no more moves left.  Nobody won, or lost.</strong>';
                break;
            case 'ongoing-game':
                echo 'No winner yet.  <strong>It\'s your turn now. Click on a dash to mark that territory with an X flag!</strong>  My move is highlighted in orange.<br />';
                echo 'Alternatively you can chicken out/give up, which means that the host wins by default, by clicking the button below.';
                break;
            default:
                // If this is reached, a specific message has been missed.  
                echo 'Well this is awkward...You\'ve somehow found a path during the game to reach this message, despite my attempts to provide a near-flawless game.  Kudos to you!';
        }
        // Are we displaying the new game button...
        if ($newGame) {
            // This is a HTML link with in-line CSS button styling.
            echo '<br /><br /><a draggable="false" href="' . $_SERVER['PHP_SELF'] . '?size=' . $this->grid_size . '" style="-webkit-appearance: button; -moz-appearance: button; appearance: button; text-decoration: none; color: initial; padding: 0.5em;">Click here to start a new game' . ($this->debug ? ' (no debug info)' : '') . '!</a>';

            if ($this->debug) {
                echo '<br /><br /><a draggable="false" href="' . $_SERVER['PHP_SELF'] . '?size=' . $this->grid_size . '&debug" style="-webkit-appearance: button; -moz-appearance: button; appearance: button; text-decoration: none; color: initial; padding: 0.5em;">Click here to start a new game with debugging info!</a>';
            }
        }
        // Display Game Statistics
        $this->game_stats();
    }

    /**
     * Create a small Comma Seperated Values file to keep track of all completed games.
     * Inside file:  Timestamp (with microseconds), IP Address, Board Size, Win Line
     */
    function game_file($stat) {
        // Generate Filename
        $file_name = date('Ymd_His') . "-" . substr(microtime(TRUE), -4) . "." . $stat;
        // Generate Directory path
        $dir_name  = "stats/" . $this->grid_size . "/";
        // Create folders if not exist
        if (!is_dir($dir_name)) {
            mkdir($dir_name, 0750, true);
        }
        // Create File
        $file = fopen($dir_name . $file_name, 'w');
        // Generate file line content
        $txt  = microtime(TRUE) . "," . $_SERVER['REMOTE_ADDR'] . "," . $this->grid_size . "," . $this->board;
        // Write/Save to file.
        fwrite($file, $txt);
        // Close file.
        fclose($file);

        if ($this->debug) {
            echo "<br /><br /> Stat for this game written to file: " . $file_name;
        }
    }

    /**
     * Generate Game Statistics
     */
    function game_stats() {
        $wins  = count(glob("stats/" . $this->grid_size . "/*.win"));
        $loses = count(glob("stats/" . $this->grid_size . "/*.lose"));
        $ties  = count(glob("stats/" . $this->grid_size . "/*.tie"));
        $games = count(glob("stats/" . $this->grid_size . "/*.*"));
        $winP  = 0.00;
        $loseP = 0.00;
        $tieP  = 0.00;

        echo "<br /><br /><br />";

        if ($games > 0) {
            $winP  = ((double) $wins / (double) $games) * 100;
            $loseP = ((double) $loses / (double) $games) * 100;
            $tieP  = ((double) $ties / (double) $games) * 100;
            echo "Out of <strong>" . $games . "</strong> completed games for board size <strong>" . $this->grid_size . "x" . $this->grid_size . "</strong>...<br />";
            echo "Total Player (X) Wins: <strong>" . $wins . "</strong> ( " . $winP . "% )<br />";
            echo "Total AI (O) Wins / Player Defeats: <strong>" . $loses . "</strong> ( " . $loseP . "% )<br />";
            echo "Total Game Ties: <strong>" . $ties . "</strong> ( " . $tieP . "% )";
        } else {
            echo "There are no game statistics to display as a game of this board size has yet to be completed.";
        }
    }

}
?>
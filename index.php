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
        <p>Welcome to Ken's PHP Tic Tac Toe game.</p>
        <?php
        // Initialize PHP Tick Tac Toe Game Engine
        new Game();
        ?>
    </body>
</html>

<?php

/**
 * This Tic Tac Toe Game class will handle everything.  Simply initialize it above with "new Game()";
 */
class Game {

    var $position;                     // The Game Board Array
    var $board      = '---------';     // The Game Board from URL.  Default is an Empty Board.
    var $debug      = false;           // Secret debug mode :)
    var $valid_char = ['x', 'o', '-']; // Valid Game Piece Characters.
    var $invalid_char;                 // Array for invalid characters.

    /**
     * Class Constructor.
     */

    function __construct() {
        // Checks if the game board variable 'board' exists.
        if (isset($_GET['board'])) {
            // The Game Board variable exists.
            // Check its length
            if (strlen(trim($_GET['board'])) == 0) {
                // Board variable exists, but contains no value.
                // Treat as new game.
                $this->board = '---------'; // This is redundant but just in case.
            } else {
                // Board variable exists, and contains some characters.
                // Take it from the URL.  Trim whitespaces and convert to lowercase.
                // Additional checks later on.
                $this->board = trim(strtolower($_GET['board']));
            }
        }

        $this->position = str_split($this->board); // From the board variable, convert into the board array.
        //
        // DEBUG MODE
        if (isset($_GET['debug'])) {
            $this->debug = true; // Debug variable declared
        }

        // Checks the game board and generate appropriate view.
        $this->game_check();
    }

    /**
     * This will go through the error checking and generate the appropriate page to display.
     */
    function game_check() {
        // This will check for any invalid characters in the game board values
        $this->invalid_char = array_diff($this->position, $this->valid_char);
        // Here is where our multitude of checks will be done.
        if (count($this->invalid_char, COUNT_RECURSIVE) > 0) {
            // An invalid character was found in the 'board' variable.
            // Display Error Message.
            $this->game_message('invalid-character');
        } else if (strlen($this->board) <> 9) {
            // Invalid Game Board - Does not contain the exact character length for the game board.
            // Display Error Message
            $this->game_message('invalid-board');
        } else if ($this->board == '---------') {
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
            $this->game_message('too-many-y');
        } else if ($this->winner('x')) {
            // X as won the game.
            // Disable game board links
            $this->game_play(false);
            // Display Win Message
            $this->game_message('x-win');
        } else if ($this->winner('o')) {
            // O as won the game.
            // Disable game board links
            $this->game_play(false);
            // Display Win Message
            $this->game_message('o-win');
        } else if (stristr($this->board, '-') === FALSE) {
            // All cells have been filled, and there are no winners.
            // Disable game board links (no links should be generated either way, but just in case.)
            $this->game_play(false);
            // Display Tie Game Message
            $this->game_message('tie-game');
        } else {
            // At this point, it's time for the AI to make its move.
            $this->pick_move();
            if ($this->winner('o')) {
                // O as won the game.
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
        // Change font and size for the HTML table
        echo '<font face = "courier" size = "5">';
        // starts the HTML table
        echo '<table cols = "3" border = "1" style = "font-weight:bold; border-collapse: collapse">';
        // opens the first row
        echo '<tr>';
        // Iterates through each of the game board cell
        for ($pos = 0; $pos < 9; $pos++) {
            // Whether or not to generate links
            if ($link) {
                // Generate the link
                echo $this->show_cell($pos);
            } else {
                // display final result with no links
                echo '<td style = "padding: 1em;">' . $this->position[$pos] . '</td>';
            }
            if ($pos % 3 == 2) {
                // Start new row after the third column
                echo '</tr><tr>';
            }
        }
        // Closes the last row
        echo '</tr>';
        // Closes the HTML table
        echo '</table>';
        // Ends the font type and size change
        echo '</font>';
        // Separates the game board from the game status
        echo '<hr />';
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
            return '<td style = "padding: 1em;' . ($this->position[$which] != $player_board[$which] ? ' background-color: #FFA500;' : '' ) . '">' . $token . '</td>';
        }
        // now the hard case
        $this->newposition         = $this->position;               // Copy original array
        $this->newposition[$which] = 'x';                           // this would be their move
        $move                      = implode($this->newposition);   // make a string from board array
        $link                      = '?board=' . $move;             // this is what we want the link to be
        // so return cell containing an anchor and showing a hyphen.
        // Also makes it so that you can click almost anywhere within that table cell.
        return '<td><a href = "' . $link . '" style = "text-decoration: none;"><div style = "padding: 1em;">-</div></a></td>';
    }

    /**
     * The Main Logic to how my AI would pick a move.
     */
    function pick_move() {
        // Let's check if there's a winning move
        $ai_win_move = $this->potential_win('o');
        if ($ai_win_move != -1) {
            // There is a winning move.  TAKE IT!
            $this->position[$ai_win_move] = 'o';
        } else {
            // There is not a winning move.
            // Let's check if there's a winning move for the player
            $player_win_move = $this->potential_win('x');
            if ($player_win_move != -1) {
                // There is a winning move.  BLOCK IT!
                $this->position[$player_win_move] = 'o';
            } else {
                // There is not a winning move from either party.
                // Select random empty cell.
                $board = implode($this->position);
                // Let's try to take the middle cell first.
                $move  = 4;
                // Loops until we have randomly selected an empty cell.
                while (substr($board, $move, 1) != '-') {
                    // Generate a random number
                    $move = rand(0, 8);
                }
                // A random empty cell has been chosen.  Replace it.
                $new_board      = substr_replace($board, 'o', $move, 1);
                
                // Regenerate the array to display where the AI moved.
                $this->position = str_split($new_board);
            }
        }
    }

    /**
     * This will check if there is a potential winning move.
     * 
     * @param string $token This is usually x or o.
     * @return number  The game cell number of the potential win, or -1 if it can't find one.
     */
    function potential_win($token) {
        // Check board for potential winning moves for token.
        // Horizontal row checking
        for ($row = 0; $row < 3; $row++) {
            $check_value = 0; // A temporary calculation variable.
            $win_move    = 0; // The Game Board Cell location.
            // Iterate through the column of a row
            for ($col = 0; $col < 3; $col++) {
                // Checks if the token matches what's currently in the game cell.
                if ($this->position[3 * $row + $col] != $token) {
                    // It's not a match - it could be a potential win cell now.
                    $win_move = 3 * $row + $col;
                } else {
                    // It contains that token.  Score one for that row.
                    $check_value++;
                }
            }
            // If that row contains two token out of three...
            if ($check_value == 2) {
                // Check if the potential win cell is empty.
                if ($this->position[$win_move] == '-') {
                    // That cell is empty.  Take it for the win or block!
                    return $win_move;
                }
            }
        }
        // At this point no horizontal rows have any potential wins.
        // Vertical column checking
        for ($col = 0; $col < 3; $col++) {
            $check_value = 0; // A temporary calculation variable.
            $win_move    = 0; // The Game Board Cell location.
            // Iterate through the row of a column
            for ($row = 0; $row < 3; $row++) {
                // Checks if the token matches what's currently in that game cell.
                if ($this->position[3 * $row + $col] != $token) {
                    // It's not a match - perhaps a potential win cell.
                    $win_move = 3 * $row + $col;
                } else {
                    // It contains that token.  Score one for that column.
                    $check_value++;
                }
            }
            // If that column contains two tokens out of three...
            if ($check_value == 2) {
                // Check if the potential win cell is empty.
                if ($this->position[$win_move] == '-') {
                    // That cell is empty.  Take it for the win or block!
                    return $win_move;
                }
            }
        }
        // At this point no horizontal or vertical rows have any potential wins.
        // Diagonal line Checking
        $diagonals = [[0, 4, 8], [2, 4, 6]]; // The cell locations for the diagonal lines.
        // Checking each diagonal line.
        foreach ($diagonals as $line) {
            $check_value = 0; // A temporary calculation variable.
            $win_move    = 0; // The Game Board Cell location.
            // Checking each cell for a line
            foreach ($line as $pos) {
                // Checks if the token matches what's currently in that game cell.
                if ($this->position[$pos] != $token) {
                    // It's not a match - perhaps a potential win cell.
                    $win_move = $pos;
                } else {
                    // It's a match.  Score one for that line.
                    $check_value++;
                }
            }
            // If that line contains two tokens out of three...
            if ($check_value == 2) {
                // Check if the potential win cell is empty
                if ($this->position[$win_move] == '-') {
                    // That cell is empty.  Take it for the win or block.
                    return $win_move;
                }
            }
        }
        // If we have reached this point, then there are no winning moves available for that token.
        return -1;
    }

    /**
     * Determine if a win has been made by a token.
     * 
     * Here is where the Debug messages are shown if specified.
     * 
     * @param string $token Usually either x or o.
     * @return boolean Returns TRUE if won, FALSE otherwise.
     */
    function winner($token) {
        $won = false; // By default, set to no win yet.
        // Horizontal checking
        // Iterating through each row
        for ($row = 0; $row < 3; $row++) {
            $won = true; // Here we're using negative testing.
            // Iterating throuch the columns in a row
            for ($col = 0; $col < 3; $col++) {
                // For debugging purposes
                if ($this->debug) {
                    echo 'checking row cell: ' . $row . ', ' . $col;
                    echo ' position: ' . (3 * $row + $col);
                }
                // Checks if the token is not in the game cell.
                if ($this->position[3 * $row + $col] != $token) {
                    $won = false;  // note the negative test
                }
                // For debugging purposes
                if ($this->debug) {
                    echo ' result: ' . $won . '<br />';
                }
                // When the first non-win cell has been reached...
                if (!$won) {
                    // For debugging purposes
                    if ($this->debug) {
                        echo '<i>Skipped checking the rest of this row (row ' . ($row + 1) . ').</i><br />';
                    }
                    // Stop checking the current row and move on.
                    break;
                }
            }
            // If all three columns of that row is true, we have a horizontal row winner.
            if ($won) {
                // Horizontal row winner - stop checking everything else.
                return true;
            }
        }

        // Vertical column checking
        // Iterate through each column
        for ($col = 0; $col < 3; $col++) {
            $won = true; // We're doing negative testing
            // Iterate through the rows of a column
            for ($row = 0; $row < 3; $row++) {
                // for debugging purposes
                if ($this->debug) {
                    echo 'checking column cell: ' . $row . ', ' . $col;
                    echo ' position: ' . (3 * $row + $col);
                }
                // Checks if the token is not in the game cell.
                if ($this->position[3 * $row + $col] != $token) {
                    $won = false;  // note the negative test
                }
                // For debugging purposes
                if ($this->debug) {
                    echo ' result: ' . $won . '<br />';
                }

                // When the first non-win cell has been reached...
                if (!$won) {
                    // For debugging purposes
                    if ($this->debug) {
                        echo '<i>Skipped checking the rest of this column (column ' . ($col + 1) . ').</i><br />';
                    }
                    // Stop checking the current column and move on.
                    break;
                }
            }
            // If all three rows of that column is true, we have a vertical column winner.
            if ($won) {
                // Vertical column winner - stop checking everything else.
                return true;
            }
        }

        // Diagonal line Checking
        // For debugging purposes
        if ($this->debug) {
            echo 'checking diagonals...<br />';
        }
        // Checks the cell that corresponds to a diagonal line.
        if (($this->position[0] == $token) &&
                ($this->position[4] == $token) &&
                ($this->position[8] == $token)) {
            // A backslash diagonal line win.
            return true;
        } else if (($this->position[2] == $token) &&
                ($this->position[4] == $token) &&
                ($this->position[6] == $token)) {
            // A forward slash diagonal line win.
            return true;
        }

        // At this point, no line wins are present.
        return false;
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
            case 'invalid-board':
                echo 'Invalid game board. Ensure the variable "board" contains exactly nine (9) characters.<br />';
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
                echo 'Click on a dash to mark that territory with an X flag!';
                $newGame = false; // Do not display the New Game Button.
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
            echo '<br /><br /><a href="' . $_SERVER['PHP_SELF'] . '" style="-webkit-appearance: button; -moz-appearance: button; appearance: button; text-decoration: none; color: initial; padding: 0.5em;">Click here to start a new game!</a>';
        }
    }

}
?>
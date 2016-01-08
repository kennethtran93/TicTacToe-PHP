<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title>Ken's Tic Tac Toe</title>
    </head>
    <body>
        <p>Welcome to Ken's PHP Tic Tac Toe game.</p>
        <?php
        // put your code here
        if (isset($_GET['board'])) {
            // board variable found
            $position = trim($_GET['board']);
            if (strlen($position) == 9) {
                // Perfect variable response.  Continuing...
                // Start Game Board
                $game = new Game($position);

                if ($game->winner('x')) {
                    echo '<strong>X is the winner in this game.</strong>';
                } else if ($game->winner('o')) {
                    echo '<strong>O is the winner in this game.</strong>';
                } else if ($position == '---------') {
                    echo 'A new game as started.  Click on a dash to mark your territory with an X!';
                } else {
                    echo '<strong>No winner yet.</strong>';
                }
            } else if (strlen($position) == 0) {
                // variable exist, but empty
                $game = new Game('---------');
                echo 'A new game as started.  Click on a dash to mark your territory with an X!';
            } else {
                // Imperfect variable response.
                echo 'Invalid game board.  Ensure the variable contains exactly nine (9) characters.<br />';
                echo 'There are currently ' . strlen($position) . ' characters on the game board.<br />';
                echo 'Either fix the game board variable in the URL, click the browser back button,'
                . ' or <a href="?board=---------">click here to start a new game</a>.';
            }
        } else {
            // board variable not found.  Creating one now...
            $game = new Game('---------');
            echo 'A new game as started.  Click on a dash to mark your territory with an X!';
        }
        ?>
    </body>
</html>

<?php

class Game {

    var $position;
    var $board;
    var $DEBUG = false;

    function __construct($squares) {
        $this->board = $squares;
        $this->position = str_split($squares);

        if (isset($_GET['debug'])) {
            // Debug variable declared
            $this->DEBUG = true;
        }
        if ($this->winner('x') || $this->winner('o')) {
            $this->gameOver();
        } else {
            $this->displayGrid();
        }
    }

    function displayGrid() {
        echo '<table cols="3" style="font-size:large; font-weight:bold">'; // starts table
        echo '<tr>'; // opens the first row
        for ($pos = 0; $pos < 9; $pos++) {
            echo $this->show_cell($pos);
            if ($pos % 3 == 2) {
                echo '</tr><tr>'; // Start new row
            }
        }
        echo '</tr>'; // closes the last row
        echo '</table>'; // closes table
        echo '<hr />';  // separates the game board from results
    }

    function show_cell($which) {
        $token = $this->position[$which];
// deal with the easy case
        if ($token <> '-') {
            return '<td>' . $token . '</td>';
        }
// now the card case
        $this->newposition = $this->position; // copy original
        $this->newposition[$which] = 'x'; // this would be their move
        $move = implode($this->newposition); // make a string from board array
        $link = '?board=' . $move; // this is what we want the link to be
// so return cell containing an anchor and showing a hyphen
        return '<td><a href="' . $link . '">-</a></td>';
    }

    function winner($token) {
        $won = false;
// Horizontal checking
        for ($row = 0; $row < 3; $row++) {
            $won = true;
            for ($col = 0; $col < 3; $col++) {
                if ($this->DEBUG) {
                    echo 'checking row cell: ' . $row . ',' . $col;
                    echo '  position: ' . (3 * $row + $col);
                }
                if ($this->position[3 * $row + $col] != $token) {
                    $won = false;  // note the negative test
                }
                if ($this->DEBUG) {
                    echo '  result: ' . $won . '<br />';
                }
                if (!$won) {
                    if ($this->DEBUG) {
                        echo '<i>Skipped checking the rest of this row (row ' . ($row + 1) . ').</i><br />';
                    }
                    break;
                }
            }
            if ($won) {
                break;
            }
        }
        if (!$won) {
// Vertical checking
            for ($col = 0; $col < 3; $col++) {
                $won = true;
                for ($row = 0; $row < 3; $row++) {
                    if ($this->DEBUG) {
                        echo 'checking column cell: ' . $row . ',' . $col;
                        echo '  position: ' . (3 * $row + $col);
                    }
                    if ($this->position[3 * $row + $col] != $token) {
                        $won = false;  // note the negative test
                    }
                    if ($this->DEBUG) {
                        echo '  result: ' . $won . '<br />';
                    }

                    if (!$won) {
                        if ($this->DEBUG) {
                            echo '<i>Skipped checking the rest of this column (column ' . ($col + 1) . ').</i><br />';
                        }
                        break;
                    }
                }
                if ($won) {
                    break;
                }
            }
        }
        if (!$won) {
// Diagonal Checking
            if ($this->DEBUG) {
                echo 'checking diagonals...<br />';
            }
            if (($this->position[0] == $token) &&
                    ($this->position[4] == $token) &&
                    ($this->position[8] == $token)) {
                $won = true;
            } else if (($this->position[2] == $token) &&
                    ($this->position[4] == $token) &&
                    ($this->position[6] == $token)) {
                $won = true;
            }
        }
        return $won;
    }

    function gameOver() {
        echo '<table cols="3" style="font-size:large; font-weight:bold">'; // starts table
        echo '<tr>'; // opens the first row
        for ($pos = 0; $pos < 9; $pos++) {
            echo '<td>' . $this->position[$pos] . '</td>'; // display final result.  no links.
            if ($pos % 3 == 2) {
                echo '</tr><tr>'; // Start new row
            }
        }
        echo '</tr>'; // closes the last row
        echo '</table>'; // closes table
        echo '<hr />';  // separates the game board from results
    }

}
?>
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

                if ($position == '---------') {
                    echo 'A new game as started.  Since I am the game host, I\'ll let you start first.<br />';
                    echo 'Click on a dash to mark your territory with an X!';
                } else if (substr_count($position, 'x') - substr_count($position, 'o') > 1) {
                    echo 'ERROR DETECTED - There are too many Xs on the game board.  QUIT CHEATING!<br />';
                    echo '<a href="' . $_SERVER['PHP_SELF'] . '">Click here to start a new game!</a>';
                } else if (substr_count($position, 'o') - substr_count($position, 'x') > 0) {
                    echo 'ERROR DETECTED - There are too many Os on the game board.  Since I\'m not programmed to cheat, you must have done something to the game board.<br />';
                    echo '<a href="' . $_SERVER['PHP_SELF'] . '">Click here to start a new game!</a>';
                } else if ($game->winner('x')) {
                    echo '<strong>X (YOU) is the winner in this game.  Congratulations.</strong><br />';
                    echo '<a href="' . $_SERVER['PHP_SELF'] . '">Click here to start a new game!</a>';
                } else if ($game->winner('o')) {
                    echo '<strong>O (Ken\'s AI) is the winner in this game.  YOU FAIL!</strong><br />';
                    echo '<a href="' . $_SERVER['PHP_SELF'] . '">Click here to start a new game!</a>';
                } else if (stristr($position, '-') === FALSE) {
                    echo 'This game is TIED as there are no more moves left.<br />';
                    echo '<a href="' . $_SERVER['PHP_SELF'] . '">Click here to start a new game!</a>';
                } else {
                    echo 'No winner yet.  <strong>It\'s your turn now.</strong><br />';
                    echo 'Alternatively you can chicken out, which means that the host wins by default, by <a href="' . $_SERVER['PHP_SELF'] . '">clicking here to start a new game!</a>';
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
                . ' or <a href="' . $_SERVER['PHP_SELF'] . '">click here to start a new game</a>.';
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
        if ((substr_count($this->board, 'x') - substr_count($this->board, 'o')) > 1 || (substr_count($this->board, 'o') - substr_count($this->board, 'x')) > 0) {
            $this->gameOver();
        } else {
            if ($this->winner('x') || $this->winner('o') || stristr($this->board, '-') === FALSE) {
                $this->gameOver();
            } else if ($this->board == '---------') {
                $this->displayGrid();
            } else {
                $this->pick_move();
                if ($this->winner('o')) {
                    $this->gameOver();
                } else {
                    $this->displayGrid();
                }
            }
        }
    }

    function displayGrid() {
        echo '<font face="courier" size="5">';
        echo '<table cols="3" border="1" style="font-weight:bold; border-collapse: collapse">'; // starts table
        echo '<tr>'; // opens the first row
        for ($pos = 0; $pos < 9; $pos++) {
            echo $this->show_cell($pos);
            if ($pos % 3 == 2) {
                echo '</tr><tr>'; // Start new row
            }
        }
        echo '</tr>'; // closes the last row
        echo '</table>'; // closes table
        echo '</font>';
        echo '<hr />';  // separates the game board from results
    }

    function show_cell($which) {
        $token = $this->position[$which];
        // deal with the easy case
        if ($token <> '-') {
            return '<td style="padding: 5px;">' . $token . '</td>';
        }
        // now the card case
        $this->newposition = $this->position; // copy original
        $this->newposition[$which] = 'x'; // this would be their move
        $move = implode($this->newposition); // make a string from board array
        $link = '?board=' . $move; // this is what we want the link to be
        // so return cell containing an anchor and showing a hyphen
        return '<td><a href="' . $link . '" style="text-decoration: none;"><div style="padding: 5px;">-</div></a></td>';
    }

    function pick_move() {
        // Let's check if there's a winning move
        $ai_win_move = $this->ai_check_move('o');
        if ($ai_win_move != -1) {
            $this->position[$ai_win_move] = 'o';
        } else {
            $player_win_move = $this->ai_check_move('x');
            if ($player_win_move != -1) {
                $this->position[$player_win_move] = 'o';
            } else {
                $board = implode($this->position);
                $move = 0;
                do {
                    $move = rand(0, 8);
                } while (substr($board, $move, 1) != '-');
                $new_board = substr_replace($board, 'o', $move, 1);
                $this->position = str_split($new_board);
            }
        }
    }

    function ai_check_move($token) {
        // Check board for potential winning moves for token.
        // Horizontal checking
        for ($row = 0; $row < 3; $row++) {
            $check_value = 0;
            $win_move = 0;
            for ($col = 0; $col < 3; $col++) {
                if ($this->position[3 * $row + $col] != $token) {
                    $win_move = 3 * $row + $col;
                } else {
                    $check_value++;
                }
            }
            if ($check_value == 2) {
                if ($this->position[$win_move] == '-') {
                    return $win_move;
                }
            }
        }
        // Vertical checking
        for ($col = 0; $col < 3; $col++) {
            $check_value = 0;
            $win_move = 0;
            for ($row = 0; $row < 3; $row++) {
                if ($this->position[3 * $row + $col] != $token) {
                    $win_move = 3 * $row + $col;
                } else {
                    $check_value++;
                }
            }
            if ($check_value == 2) {
                if ($this->position[$win_move] == '-') {
                    return $win_move;
                }
            }
        }
        // Diagonal Checking
        $diagonals = [[0, 4, 8], [2, 4, 6]];
        foreach ($diagonals as $line) {
            $check_value = 0;
            $win_move = 0;
            foreach ($line as $pos) {
                if ($this->position[$pos] != $token) {
                    $win_move = $pos;
                } else {
                    $check_value++;
                }
            }
            if ($check_value == 2) {
                if ($this->position[$win_move] == '-') {
                    return $win_move;
                }
            }
        }

        // If we have reached this point, then there are no winning moves available for that token.
        return -1;
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
        echo '<font face="courier" size="5">';
        echo '<table cols="3" border="1" style="font-weight:bold; border-collapse: collapse">'; // starts table
        echo '<tr>'; // opens the first row
        for ($pos = 0; $pos < 9; $pos++) {
            echo '<td style="padding: 5px;">' . $this->position[$pos] . '</td>'; // display final result.  no links.
            if ($pos % 3 == 2) {
                echo '</tr><tr>'; // Start new row
            }
        }
        echo '</tr>'; // closes the last row
        echo '</table>'; // closes table
        echo '</font>';
        echo '<hr />';  // separates the game board from results
    }

}
?>
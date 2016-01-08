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
        <?php
        // put your code here
        $DEBUG = false;
        if (isset($_GET['debug'])) {
            // Debug variable declared
            $DEBUG = true;
        }
        if (isset($_GET['board'])) {
            // board variable found
            $position = $_GET['board'];
            if (strlen($position) == 9) {
                // Perfect variable response.  Continuing...
                $squares = str_split($position);
                // Debug - Display Draft Game Grid
                if ($DEBUG) {
                    echo '<font face="courier" size="5">';
                    for ($x = 0; $x < 9; $x++) {
                        echo $squares[$x];
                        if (($x + 1) % 3 == 0) {
                            echo '<br />';
                        }
                    }
                    echo '</font>';
                }

                if (winner('x', $squares, $DEBUG)) {
                    echo '<strong>X is the winner in this game.</strong>';
                } else if (winner('o', $squares, $DEBUG)) {
                    echo '<strong>O is the winner in this game.</strong>';
                } else {
                    echo '<strong>No winner yet.</strong>';
                }
            } else {
                // Imperfect variable response.
                echo 'Invalid variable response.  Ensure the variable contains exactly nine (9) characters.<br />';
                echo 'You have currently entered ' . strlen($position) . ' characters.<br />';
                echo 'Use x and o for the respective players.  Use - (dash) for an empty square.';
            }
        } else {
            // board variable not found.
            echo 'PHP GET variable "board" not found. <br />';
            echo 'Please append the following to the URL above: <br /><br />';
            echo '?board=--------- <br /><br />';
            echo 'where each individual dash is a place in the tic tac toe board, going left to right, up to down.';
        }
        ?>
    </body>
</html>

<?php

function winner($token, $position, $DEBUG) {
    $won = false;
    // Horizontal checking
    for ($row = 0; $row < 3; $row++) {
        $won = true;
        for ($col = 0; $col < 3; $col++) {
            if ($DEBUG) {
                echo 'checking row cell: ' . $row . ',' . $col;
                echo '  position: ' . (3 * $row + $col);
            }
            if ($position[3 * $row + $col] != $token) {
                $won = false;  // note the negative test
            }
            if ($DEBUG) {
                echo '  result: ' . $won . '<br />';
            }
            if (!$won) {
                if ($DEBUG) {
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
                if ($DEBUG) {
                    echo 'checking column cell: ' . $row . ',' . $col;
                    echo '  position: ' . (3 * $row + $col);
                }
                if ($position[3 * $row + $col] != $token) {
                    $won = false;  // note the negative test
                }
                if ($DEBUG) {
                    echo '  result: ' . $won . '<br />';
                }

                if (!$won) {
                    if ($DEBUG) {
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
        if ($DEBUG) {
            echo 'checking diagonals...<br />';
        }
        if (($position[0] == $token) &&
                ($position[4] == $token) &&
                ($position[8] == $token)) {
            $won = true;
        } else if (($position[2] == $token) &&
                ($position[4] == $token) &&
                ($position[6] == $token)) {
            $won = true;
        }
    }
    return $won;
}
?>
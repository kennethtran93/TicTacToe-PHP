<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
    </head>
    <body>
        <?php
        // put your code here
        if (isset($_GET['board'])) {
            // board variable found
            $position = $_GET['board'];
            if (strlen($position) == 9) {
                // Perfect variable response.  Continuing...
                $squares = str_split($position);
                if (winner('x', $squares)) {
                    echo 'X is the winner in this game.';
                } else if (winner('o', $squares)) {
                    echo 'O is the winner in this game.';
                } else {
                    echo 'No winner yet.';
                }
            } else {
                // Imperfect variable response.
                echo 'Invalid variable response.  Ensure the variable contains exactly nine characters.<br />';
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

function winner($token, $position) {
    $won = false;
    // Row Checking
    if (($position[0] == $token) &&
            ($position[1] == $token) &&
            ($position[2] == $token)) {
        $won = true;
    } else if (($position[3] == $token) &&
            ($position[4] == $token) &&
            ($position[5] == $token)) {
        $won = true;
    } else if (($position[6] == $token) &&
            ($position[7] == $token) &&
            ($position[8] == $token)) {
        $won = true;
    }
    // Column Checking
    else if (($position[0] == $token) &&
            ($position[3] == $token) &&
            ($position[6] == $token)) {
        $won = true;
    } else if (($position[1] == $token) &&
            ($position[4] == $token) &&
            ($position[7] == $token)) {
        $won = true;
    } else if (($position[2] == $token) &&
            ($position[5] == $token) &&
            ($position[8] == $token)) {
        $won = true;
    }
    // Diagonal Checking
    else if (($position[0] == $token) &&
            ($position[4] == $token) &&
            ($position[8] == $token)) {
        $won = true;
    } else if (($position[2] == $token) &&
            ($position[4] == $token) &&
            ($position[6] == $token)) {
        $won = true;
    }
    return $won;
}
?>
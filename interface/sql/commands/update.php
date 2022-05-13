<?php

// Data Submission Structure
// -------------------------------------------------------------------------------
// {
//     "condition": {
//         "name": <string>,
//         "value": <string>,
//         "valuetype": <string> <-- used as stmt type ('s' for string for example)
//     },
//     "set": {
//         "key0": <string>,
//         "key1": <string>,
//         "key<...>": <string>,
//         "key<n>": <string>
//     },
//     "valuetypes": "<key0 type><key1 type>...<ken<n> type>"
// }

class update {
    function auth() {

        $security = new security;

        if (isset($_GET['userid'])) {
            $userid = $_GET['userid'];
        } else {
            echo "<br>HTTP400 BadRequest";
            exit();
        }
        if (isset($_GET['sessionid'])) {
            $sessionid = $_GET['sessionid'];
        } else {
            echo "<br>HTTP400 BadRequest";
            exit();
        }
        $id = $security->auth($userid, $sessionid);
        if ($id == "HTTP419 InvalidSession") {
            echo $id;
            exit();
        }
        echo "{\"Session_Id\":\"". $id. "\"}<br>";
    }
    
    function update() {
        $conn = connectToDatabase();
        if (!$conn) {
            echo 'connect error: '. mysqli_connect_error();
            exit();
        }
        if (isset($_POST['data'])) {
            $json = $_POST['data'];
        }

        if (isset($_GET['table'])) {
            $table = cleanPassword($_GET['table']);
        } else {
            echo "<br>HTTP400 BadRequest";
            exit();
        }

        if ($table == "Player") {
            echo "<br>HTTP403 AccessDenied";
            exit();
        }

        $data = json_decode($json, true);
        $keys = array_keys($data['set']);

        $cond = cleanPassword($data['condition']['name']);
        $condVal = cleanPassword($data['condition']['value']);
        $valType = cleanPassword($data['valuetypes']);
        $condValType = cleanPassword($data['condition']['valuetype']);

        $i = 0;
        while ($i < count($keys)) {
            $key = cleanPassword($keys[$i]);
            $value = cleanPassword($data['set'][$keys[$i]]);
            $queryString = "update ". $table. " set ". $key. " = ? where ". $cond. " = ?;";
            // echo $queryString;
            $query = mysqli_prepare($conn, $queryString);
            if (!$query) {
                echo 'mysqli error: '. mysqli_error($conn);
                exit();
            }
            //echo $valType[$i];
            // echo "<br>". $value. "<br>". $condVal;
            mysqli_stmt_bind_param($query, $valType[$i]. $condValType, $value, $condVal);
            if (mysqli_stmt_execute($query)) {
                # $result = $result. " ;;; ". mysqli_stmt_get_result($query);
                $result = mysqli_stmt_get_result($query);
                $affected = mysqli_stmt_affected_rows($query);
                # $affected = $affected. mysqli_stmt_affected_rows($query);
                // echo $affected. " ;;; ". $result;
                echo "<br>HTTP200 Ok";
            } else {
                echo 'stmt error: '. mysqli_stmt_error($query);
                exit();
            }
            $i = $i + 1;
        }
    }

    function main() {
        $this->auth();
        $this->update();
    }

}
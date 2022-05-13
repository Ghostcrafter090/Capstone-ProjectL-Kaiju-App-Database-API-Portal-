<?php

// Data Submission Structure
// -------------------------------------------------------------------------------
// {
//     "set": {
//         "key0": <string>,
//         "key1": <string>,
//         "key<...>": <string>,
//         "key<n>": <string>
//     },
//     "valuetypes": "<key0 type><key1 type>...<ken<n> type>"
// }

class insert {
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
    function insert() {
        $conn = connectToDatabase();
        if (!$conn) {
            echo '<br>connect error: '. mysqli_connect_error();
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

        $valType = cleanPassword($data['valuetypes']);

        $queryString = "insert into ". $table. " (";
        $values = "";

        $i = 0;
        while ($i < count($keys)) {
            if (!($queryString == "insert into ". $table. " (")) {
                $queryString = $queryString. ", ";
                $values = $values. ",";
            }
            $key = cleanPassword($keys[$i]);
            $queryString = $queryString. $key;
            $values = $values. " ? ";
            $i = $i + 1;
        }

        $queryString = $queryString. ") values (";
        $valuesArray = [];

        $i = 0;
        while ($i < count($keys)) {
            $value = cleanPassword($data['set'][$keys[$i]]);
            array_push($valuesArray, $value);
            $i = $i + 1;
        }

        // echo count($valuesArray);

        $queryString = $queryString. $values. ");";

        // echo $queryString;
        $query = mysqli_prepare($conn, $queryString);
        if (!$query) {
            echo '<br>mysqli error: '. mysqli_error($conn);
            exit();
        }
        // echo $valType;
        # mysqli_stmt_bind_param($query, $valType, $valuesArray);
        $query->bind_param($valType, ...$valuesArray);
        if (mysqli_stmt_execute($query)) {
            # $result = $result. " ;;; ". mysqli_stmt_get_result($query);
            $result = mysqli_stmt_get_result($query);
            $affected = mysqli_stmt_affected_rows($query);
            # $affected = $affected. mysqli_stmt_affected_rows($query);
            // echo $affected. " ;;; ". $result;
            echo "<br>HTTP200 Ok";
        } else {
            echo '<br>stmt error: '. mysqli_stmt_error($query);
            exit();
        }
    }

    function main() {
        $this->auth();
        $this->insert();
    }
}
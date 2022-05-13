<?php

// {
//     "condition": {
//         "name": <string>
//         "value": <string>
//         "valuetype": <string>
//     }
// }

class get {
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

    function getRowsNumber($stmt) {
        $count = $stmt->fetchColumn();
        print $count;
    }

    function get() {
        $result = FALSE;
        $conn = connectToDatabase();
        if (!$conn) {
            echo '<br>connect error: '. mysqli_connect_error();
            exit();
        }
        if (isset($_POST['data'])) {
            $json = $_POST['data'];
        }
		
		echo $json;

        if (isset($_GET['table'])) {
            $table = cleanPassword($_GET['table']);
        } else {
            echo "<br>HTTP400 BadRequest";
            exit();
        }

        $data = json_decode($json, true);

        $cond = cleanPassword($data['condition']['name']);
        $condVal = cleanPassword($data['condition']['value']);
        $condValType = cleanPassword($data['condition']['valuetype']);

        $query = mysqli_prepare($conn, "select * from ". $table. " where (". $cond. " = ? )");

        if (!$query) {
            echo '<br>mysqli error: '. mysqli_error($conn);
            exit();
        }

        mysqli_stmt_bind_param($query, $condValType, $condVal);
        if (mysqli_stmt_execute($query)) {
            $result = mysqli_stmt_get_result($query);
        }
        if (!$result) {
            die("Error Signing In." . mysqli_error($conn));
        }
        return $result;
    }

    function arrayResults($result) {
        $rows = $result->fetch_all(MYSQLI_ASSOC);
        echo "{\"data\":[{";
        foreach ($rows as $row) {
            echo "},{";
            $keys = array_keys($row);
            $i = 0;
            echo "\"null\":\"null\"";
            while ($i < count($keys)) {
                echo ",\"". $keys[$i]. "\":\"". $row[$keys[$i]]. "\"";
                $i = $i + 1;
            }
        }
        echo "}]}";
    }

    function main() {
        $this->auth();
        $array = $this->arrayResults($this->get());
    }
}


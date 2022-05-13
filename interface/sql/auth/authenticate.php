<?php

include($_SERVER['DOCUMENT_ROOT'].'/interface/sql/connection.php');
include($_SERVER['DOCUMENT_ROOT'].'/interface/sql/auth/userinfo.php');

function generateRandomString($length) {
    $chars = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@";
    $i = 0;
    $out = "";
    while ($i < $length) {
        $out = $out. $chars[rand(0, 63)];
        $i = $i + 1;
    }
    return $out;
}

class security {
    function updateSessionId($username, $id) {
        $conn = connectToDatabase();
        if (!$conn) {
            echo 'connect error: '. mysqli_connect_error();
            exit();
        }
        $cond = "Username";
        $condVal = cleanPassword($username);
        $key = "Session_ID";
        $value = cleanPassword($id);
        $queryString = "update Player set ". $key. " = ? where ". $cond. " = ?;";
        // echo $queryString;
        $query = mysqli_prepare($conn, $queryString);
        if (!$query) {
            echo 'mysqli error: '. mysqli_error($conn);
            exit();
        }
        // echo "<br>". $value. "<br>". $condVal;
        mysqli_stmt_bind_param($query, "ss", $value, $condVal);
        if (mysqli_stmt_execute($query)) {
            $result = mysqli_stmt_get_result($query);
            $affected = mysqli_stmt_affected_rows($query);
            // echo $affected. " ;;; ". $result;
        } else {
            echo 'stmt error: '. mysqli_stmt_error($query);
            exit();
        }
    }

    function updateUserInfo($username) {
        $conn = connectToDatabase();
        if (!$conn) {
            echo 'connect error: '. mysqli_connect_error();
            exit();
        }
        $cond = "Username";
        $condVal = cleanPassword($username);
        $key = "UserInfo";
        $value = cleanPassword((new userinfo)->collect());
        $queryString = "update Player set ". $key. " = ? where ". $cond. " = ?;";
        // echo $queryString;
        $query = mysqli_prepare($conn, $queryString);
        if (!$query) {
            echo 'mysqli error: '. mysqli_error($conn);
            exit();
        }
        // echo "<br>". $value. "<br>". $condVal;
        mysqli_stmt_bind_param($query, "ss", $value, $condVal);
        if (mysqli_stmt_execute($query)) {
            $result = mysqli_stmt_get_result($query);
            $affected = mysqli_stmt_affected_rows($query);
            // echo $affected. " ;;; ". $result;
        } else {
            echo 'stmt error: '. mysqli_stmt_error($query);
            exit();
        }
    }

    function addUser($username, $password) {
        $out = 1;
        $conn = connectToDatabase();
        if (!$conn) {
            echo 'connect error: '. mysqli_connect_error();
            exit();
        }
        $queryString = "insert into Player (Username, Password, Session_ID, UserInfo) values ( ? , ? , ? , ? )";
        // echo $queryString;
        $query = mysqli_prepare($conn, $queryString);
        if (!$query) {
            echo 'mysqli error: '. mysqli_error($conn);
            exit();
        }
        // echo "<br>". $value. "<br>". $condVal;
        $userid = cleanPassword($username);
        $encryptHash = password_hash(cleanPassword($password), PASSWORD_BCRYPT);
        $sessionId = cleanPassword(generateRandomString(63));
        $userInfo = cleanPassword((new userinfo)->collect());
        mysqli_stmt_bind_param($query, "ssss", $userid, $encryptHash, $sessionId, $userInfo);
        if (mysqli_stmt_execute($query)) {
            $result = mysqli_stmt_get_result($query);
            $affected = mysqli_stmt_affected_rows($query);
            // echo $affected. " ;;; ". $result;
            $out = 0;
        } else {
            echo 'stmt error: '. mysqli_stmt_error($query);
            exit();
        }
        return $out;
    }

    function checkUser($username, $password) {
        $result = connectToDatabaseLogin(cleanPassword($username));
        $row = mysqli_fetch_array($result);
        $pass = 0;
        if (isset($row[1])) {
            if ($username == $row[1]) {
                if (isset($row[2])) {
                    if (password_verify(cleanPassword($password), $row[2])) {
                        $pass = 1;
                    }
                }
            }
        }
        return $pass;
    }

    function unixTimestamp($string) {
        $datetime = explode(" ", $string);
        $year = intval(explode("-", $datetime[0])[0]);
        $month = intval(explode("-", $datetime[0])[1]);
        $day = intval(explode("-", $datetime[0])[2]);
        $hour = intval(explode(":", $datetime[1])[0]);
        $min = intval(explode(":", $datetime[1])[1]);
        $sec = intval(explode(":", $datetime[1])[2]);
        $unix = ($year * 365 * 24 * 60 * 60) + ($this->getMonthsLength($month, $year) * 24 * 60 * 60) + ($day * 24 * 60 * 60) + ($hour * 60 * 60) + ($min * 60) + $sec;
        return $unix;
    }

    function getMonthsLength($m, $y) {
        $d = 0;
        if ($m == 1) {
            $d = $d + 31;
        }
        if ($m == 2) {
            if (($y % 4) == 0) {
                $d = $d + 1;
            }
            $d = $d + 28;
        }
        if ($m == 3) {
            $d = $d + 31;
        }
        if ($m == 4) {
            $d = $d + 30;
        }
        if ($m == 5) {
            $d = $d + 31;
        }
        if ($m == 6) {
            $d = $d + 30;
        }
        if ($m == 7) {
            $d = $d + 31;
        }
        if ($m == 8) {
            $d = $d + 31;
        }
        if ($m == 9) {
            $d = $d + 30;
        }
        if ($m == 10) {
            $d = $d + 31;
        }
        if ($m == 11) {
            $d = $d + 30;
        }
        if ($m == 12) {
            $d = $d + 31;
        }
        return $d;
    }

    function checkSessionId($username, $sessionId) {
        $result = connectToDatabaseLogin(cleanPassword($username));
        $row = mysqli_fetch_array($result);
        $pass = 0;
        if (isset($row[1])) {
            if (cleanPassword($username) == $row[1]) {
                if (isset($row[3])) {
                    if (isset($row[4])) {
                        if (($this->unixTimestamp($row[3]) + 300) > $this->unixTimestamp(date("Y-m-d H:m:s"))) {
                            if ($row[4] == cleanPassword($sessionId)) {
                                $pass = 1;
                            }
                        }
                    }
                }
            }
        }
        return $pass;
    }

    function checkUserInfo($username) {
        $result = connectToDatabaseLogin(cleanPassword($username));
        $row = mysqli_fetch_array($result);
        $pass = 0;
        if (isset($row[1])) {
            if (cleanPassword($username) == $row[1]) {
                if (isset($row[3])) {
                    if (isset($row[5])) {
                        if (($this->unixTimestamp($row[3]) + 300) > $this->unixTimestamp(date("Y-m-d H:m:s"))) {
                            $userInfo = (new userinfo)->collect();
                            if ($row[5] == cleanPassword($userInfo)) {
                                $pass = 1;
                            }
                        }
                    }
                }
            }
        }
        return $pass;
    }

    function genSessionId($username) {
        $sessionId = generateRandomString(63);
        $this->updateSessionId(cleanPassword($username), cleanPassword($sessionId));
        return cleanPassword($sessionId);
    }

    function signin($username, $password) {
        if ($this->checkUser($username, $password) == 1) {
            $sessionId = $this->genSessionId(cleanPassword($username));
            $this->updateUserInfo(cleanPassword($username));
        } else {
            $sessionId = "HTTP401 InvalidCredentials";
        }
        return $sessionId;
    }

    function auth($username, $sessionId) {
        if ($this->checkSessionId($username, $sessionId) == 1) {
            if ($this->checkUserInfo(cleanPassword($username)) == 1) {
                $newSessionId = $this->genSessionId($username);
                $this->updateUserInfo(cleanPassword($username));
            } else {
                $newSessionId = "HTTP419 InvalidSession";
            }
        } else {
            $newSessionId = "HTTP419 InvalidSession";
        }
        return $newSessionId;
    }

    function signup($username, $password) {
        return $this->addUser($username, $password);
    }
}
<?php

    // Fields ommitted for security reasons, replace as needed when testing.

    require 'passwdTools.php';
    include($_SERVER['DOCUMENT_ROOT'].'/serverinfo.php');

    if (isset($_ENV['DB_HOST'])) {
        $DB_HOST = $_ENV['DB_HOST'];
    } else {
        $DB_HOST = '<DBHOST>';
    }
    if (isset($_ENV['DB_NAME'])) {
        $DB_NAME = $_ENV['DB_NAME'];
    } else {
        $DB_NAME = '<DBNAME>';
    }
    if (isset($_ENV['DB_USER'])) {
        $DB_USER = $_ENV['DB_USER'];
    } else {
        $DB_USER = '<DBUSER>';
    }
    if (isset($_ENV['DB_PASSWD'])) {
        $DB_PASSWD = $_ENV['DB_PASSWD'];
    } else {
        $DB_PASSWD = '<DBPASSWD>';
    }

    function mysqliConnect() {
        global $DB_HOST;
        global $DB_NAME;
        global $DB_USER;
        global $DB_PASSWD;

        return mysqli_connect($DB_HOST, $DB_USER, $DB_PASSWD, $DB_NAME);
    }

    function pdoConnect() {

        global $DB_HOST;
        global $DB_NAME;

        $conn = new PDO("mysql:host=". $DB_HOST. ";dbname=". $DB_NAME. ";");
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    }

    function connect() {

        global $pdoBool;

        if ($pdoBool !== 1) {
            $out = mysqliConnect();
        } else {
            $out = pdoConnect();
        }
        return $out;
    }

    function connectToDatabaseMenu() {
        $result = FALSE;
        $conn = connect();
        if (!$conn) {
            die("Unable to connect. Error: " . mysqli_connect_error());
        }
        if (isset($_GET['search'])) {
            $search = $_GET['search'];
        } else {
            $search = "";
        }
        $cleanSearch = '%'. cleanPassword($search). '%';
        $query = mysqli_prepare($conn, "call listEmployees( ?, ? );");
        mysqli_stmt_bind_param($query, 'ss', $cleanSearch, $cleanSearch);
        if (mysqli_stmt_execute($query)) {
            $result = mysqli_stmt_get_result($query);
        }
        if (!$result) {
            die("Uncaught Exception grabbing sql data." . mysqli_error($conn));
        }
        return $result;
    }

    function connectToDatabaseLogin($username) {
        $result = FALSE;
        $conn = connect();
        if (!$conn) {
            die("Unable to connect. Error: " . mysqli_connect_error());
        }

        $cleanUsername = cleanPassword($username);
        $query = mysqli_prepare($conn, "select * from Player where (username = ? )");
        mysqli_stmt_bind_param($query, 's', $cleanUsername);
        if (mysqli_stmt_execute($query)) {
            $result = mysqli_stmt_get_result($query);
        }
        if (!$result) {
            die("Error Signing In." . mysqli_error($conn));
        }
        return $result;
    }

    function connectToDatabaseRegister($username, $password) {
        $conn = connect();
        if (!$conn) {
            die("Unable to connect. Error: " . mysqli_connect_error());
        }
        $cleanUsername = cleanPassword($username);
        $cleanPassword = $password;
        $query = mysqli_prepare($conn, "insert into users (username, password) values ( ? , ? )");
        mysqli_stmt_bind_param($query, 'ss', $cleanUsername, $cleanPassword);
        if (mysqli_stmt_execute($query)) {
            $result = mysqli_stmt_affected_rows($query);
        }
        if (!$result) {
            die("Error Creating Account." . mysqli_error($conn));
        }
        return $result;
    }

    function connectToDatabase() {
        if (isset($_GET['currentCount'])) {
            $currentCount = intval($_GET['currentCount'], 10);
        } else {
            $currentCount = -1;
        }
        $conn = connect();
        if (!$conn) {
            die("Unable to connect. Error: " . mysqli_connect_error());
        }
        return $conn;
    }
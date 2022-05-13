<?php

include($_SERVER['DOCUMENT_ROOT'].'/interface/sql/auth/authenticate.php');
include($_SERVER['DOCUMENT_ROOT'].'/interface/sql/commands/get.php');
include($_SERVER['DOCUMENT_ROOT'].'/interface/sql/commands/update.php');
include($_SERVER['DOCUMENT_ROOT'].'/interface/sql/commands/insert.php');


class authsys {
    function signin() {
        $security = new security;
        if (isset($_GET['userid'])) {
            $username = $_GET['userid'];
        } else {
            echo "HTTP400 BadRequest";
            exit();
        }
        if (isset($_GET['passwd'])) {
            $password = $_GET['passwd'];
        } else {
            echo "HTTP400 BadRequest";
            exit();
        }
        return $security->signin($username, $password);
    }

    function signup() {
        $security = new security;
        if (isset($_GET['userid'])) {
            $username = $_GET['userid'];
        } else {
            echo "HTTP400 BadRequest";
            exit();
        }
        if (isset($_GET['passwd'])) {
            $password = $_GET['passwd'];
        } else {
            echo "HTTP400 BadRequest";
            exit();
        }
        $security->signup($username, $password);
    }
}

function main() {
    if (isset($_GET['comm'])) {
        $comm = $_GET['comm'];
    } else {
        echo "HTTP400 BadRequest";
        exit();
    }

    $authsys = new authsys;

    if ($comm == "auth") {
        $out = $authsys->signin();
    } else if ($comm == "addauth") {
        $outn = $authsys->signup();
        if ($outn == 0) {
            $out = "HTTP200 Ok";
        } else {
            $out = "HTTP500 InternalError";
        }
    } else if (($comm == "get") or ($comm == "insert") or ($comm == "update")) {

        if ($comm == "get") {
            $get = new get;
            $out = $get->main();
        }

        if ($comm == "update") {
            $get = new update;
            $out = $get->main();
        }

        if ($comm == "insert") {
            $get = new insert;
            $out = $get->main();
        }

    } else {
        echo "HTTP400 BadRequest";
        exit();
    }
    echo $out;
}

main();

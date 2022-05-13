<?php
// echo $_SERVER['HTTP_ACCEPT']. "<br>";
// echo $_SERVER['HTTP_ACCEPT_CHARSET']. "<br>";
// echo $_SERVER['HTTP_ACCEPT_ENCODING']. "<br>";
// echo $_SERVER['HTTP_ACCEPT_LANGUAGE']. "<br>";
// echo $_SERVER['HTTP_CONNECTION']. "<br>";
// echo $_SERVER['HTTP_USER_AGENT']. "<br>";
// echo $_SERVER['AUTH_TYPE']. "<br>";
// echo $_SERVER['REMOTE_HOST']. "<br>";
// echo $_SERVER['HTTPS']. "<br>";
// echo $_SERVER['HTTP_HOST']. "<br>";
// echo $_SERVER['REMOTE_ADDR']. "<br>";

class userinfo {
    function collect() {
        $jsonObj = json_decode("{}", true);
        $fieldList = ['HTTP_ACCEPT', 'HTTP_ACCEPT_CHARSET', 'HTTP_ACCEPT_ENCODING', 'HTTP_ACCEPT_LANGUAGE', 'HTTP_CONNECTION', 'HTTP_USER_AGENT', 'AUTH_TYPE', 'REMOTE_HOST', 'HTTPS', 'HTTP_HOST', 'REMOTE_ADDR'];
        $i = 0;
        while ($i < count($fieldList)) {
            if (isset($_SERVER[$fieldList[$i]])) {
                $jsonObj[$fieldList[$i]] = $_SERVER[$fieldList[$i]];
            }
            $i = $i + 1;
        }
        return json_encode($jsonObj);
    }
}
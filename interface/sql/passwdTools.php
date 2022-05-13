<?php

    function validatePassword($password) {

        $passwordScore = 0;

        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);

        if (!$uppercase) {
            $passwordScore = $passwordScore + 1;
        }
        if (!$lowercase) {
            $passwordScore = $passwordScore + 1;
        }
        if (!$number) {
            $passwordScore = $passwordScore + 1;
        }
        if (!$specialChars) {
            $passwordScore = $passwordScore + 1;
        }
        if (strlen($password) < 8) {
            $passwordScore = -1;
        }

        return $passwordScore;
    }

    function cleanPassword($password) {
        $password = strval($password);
        $allowedChars = '*&/?@!0 123456789@ABCD.EFGHIJKLMNOPQRSTUVWXYZ_-abcdefghijklmnopqrstuvwxyz';
        $i = 0;
        while ($i < strlen($password)) {
            if (strpos($allowedChars, $password[$i]) === FALSE) {
                $password = str_replace($password[$i], "/?/", $password);
            }
            $i = $i + 1;
        }
        return $password;
    }
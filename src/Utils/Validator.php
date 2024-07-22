<?php

namespace Blog\Twig\Utils;

class Utility {

    public static function validateNameAndSurname($value, $fieldName) {
        $regex = '/^[a-zA-ZÀ-ÖØ-öø-ÿ\s\'\-]+$/';

        if (preg_match($regex, $value)) {
            return true;
        } else {
            return "$fieldName ne peut contenir que des lettres, des espaces, des apostrophes et des tirets";
        }
    }

    public static function validateUsername($username) {
        $usernameRegex = '/^[a-zA-Z0-9_]+$/';
        return preg_match($usernameRegex, $username);
    }

    public static function validatePassword($password) {
        $passwordRegex = '/^(?=.*[A-Z])(?=.*\d)(?=.*[^\w\d\s]).{8,}$/';
        return preg_match($passwordRegex, $password);
    }

    public static function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public static function validateMaxLength(string $string,int $max):bool {
        return !empty($string) && strlen($string) <= $max;
    }

    public static function validateContent($content) {
        return !empty($content);
    }
}

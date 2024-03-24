<?php

namespace LSM;

class Validator
{
    /**
     * @param string $item
     * @return string
     */
    private function clearItem(string $item) : string
    {
        return htmlspecialchars(strip_tags(stripslashes($item)));
    }

    /**
     * @param array $data
     * @return array
     */
    public function clearData(array $data) : array
    {
        $result = [];
        foreach ($data as $key => $value) {
            $value = $this->clearItem($value);
            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * @param string $phone
     * @return bool
     */
    public function validatePhone(string $phone) : bool {

        $phone = $this->clearItem($phone);
        $pattern = '/^(\+7|8)\d{10}$/';
        if (preg_match($pattern, $phone)) {
            return true;
        }
        return false;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function validateName(string $name) : bool {

        $name = $this->clearItem($name);
        $pattern = '/(^[\p{L}]{1,30})+$/iu';
        if (preg_match($pattern, $name)) {
            return true;
        }

        return false;
    }

    /**
     * @param string $email
     * @return bool
     */
    public function validateEmail(string $email) : bool {

        $email = $this->clearItem($email);
        $patternFirst = '/.+@.+\..+/i';
        $patternSecond = '/^((([0-9A-Za-z]{1}[-0-9A-z\.]{1,}[0-9A-Za-z]{1})|([0-9А-Яа-я]{1}[-0-9А-я\.]{1,}[0-9А-Яа-я]{1}))@([-A-Za-z]{1,}\.){1,2}[-A-Za-z]{2,})$/u';
        if (preg_match($patternFirst, $email) && preg_match($patternSecond, $email)) {
            return true;
        }
        return false;
    }
}
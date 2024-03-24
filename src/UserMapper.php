<?php

namespace LSM;

use PDO;
use PDOException;

class UserMapper
{
    /**
     * @var PDO
     */
    private Database $database;
    private Validator $validator;


    /**
     * @param Database $database
     */
    public function __construct(Database $database)
    {
        $this->database = $database;
        $this->validator = new Validator();
    }

    /**
     * @return array|null
     */
    public function getAllUsers() :?array
    {
        $statement = $this->database->getConnection()->prepare('SELECT * FROM users');
        $statement->execute();
        return $statement->fetchAll();
    }

    /**
     * @param array $data
     * @return bool
     * @throws UserException
     */

    public function addUser(array $data) : bool
    {
        $data = $this->validator->clearData($data);
        $errors = [];
        if (empty($data['user_name'])) {
//            throw new UserException('Поле "имя" не может быть пустым');
            $errors[] = 'Поле "имя" не может быть пустым';
        } elseif (is_string($data['user_name']) && mb_strlen($data['user_name']) > 30) {
//            throw new UserException('Превышено допустимое количество сиволов (30)');
            $errors[] = 'Превышено допустимое количество сиволов (30)';
        }  elseif (!$this->validator->validateName($data['user_name'])) {
//            throw new UserException('В поле "имя" допустимы только буквы кирилицы и латиницы');
            $errors[] = 'В поле "имя" допустимы только буквы кирилицы и латиницы';
        }

        if (empty($data['phone'])) {
//            throw new UserException('Поле "телефон" не может быть пустым');
            $errors[] = 'Поле "телефон" не может быть пустым';
        } elseif (!$this->validator->validatePhone($data['phone'])) {
//            throw new UserException('Неверный формат номера телефона');
            $errors[] = 'Неверный формат номера телефона';
        }

        if (empty($data['user_email'])) {
//            throw new UserException('Поле "email" не может быть пустым');
            $errors[] = 'Поле "email" не может быть пустым';
        } elseif (!$this->validator->validateEmail($data['user_email'])) {
//            throw new UserException('Неверный формат адреса электронной почты');
            $errors[] = 'Неверный формат адреса электронной почты';
        }

        if (!empty($errors)) {
            throw new UserException('', $errors);
        }

        $statement = $this->database->getConnection()->prepare(
            'INSERT INTO users (name, email, phone) VALUES (:name, :email, :phone)'
        );

        try {
            $statement->execute([
                'name' => $data['user_name'],
                'email' => $data['user_email'],
                'phone' => $data['phone']
            ]);
        } catch (PDOException $exception) {

            if (str_contains($exception->getMessage(), $data['user_email'])) {
                throw new UserException('', ['Пользователь с таким почтовым адресом уже существует']);
            } elseif (str_contains($exception->getMessage(), $data['phone'])) {
                throw new UserException('', ['Пользователь с таким номером телефона уже существует']);
            }
        }

        return true;
    }
}
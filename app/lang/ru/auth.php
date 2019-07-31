<?php
/**
 * Created by PhpStorm.
 * User: user1361
 * Локализация заменяет англоязычные ответы модуля авторизации Sentry на русские
 * OWASP standarts
 *
 */
return [
    'A user could not be found with a login value of [].' => 'Неправильное имя пользователя или пароль.',
    'Login attribute [] was not provided.' => 'Аттрибут логина не был предоставлен.',
    'A user was not found with the given credentials.' => 'Неправильное имя пользователя или пароль.',
    'A user was found to match all plain text credentials however hashed credential [] did not match.' => 'Неправильное имя пользователя или пароль.',
    'The password attribute is required.' => 'Необходим пароль для авторизации',
    'No activation code passed.' => 'Код активации не прошел',
    'Found [] users with the same activation code.' => 'Уже найдены пользователи с таким же кодом активации.',
    'A user was not found with the given activation code.' => 'Пользователь не был найден с данным кодом активации.',
    'Found [] users with the same reset password code.' => 'Уже найдены пользователи с таким же кодом для сброса пароля.',
    'A user was not found with the given reset password code.' => 'Неправильное имя пользователя или пароль.',
    'A login is required for a user, none given.' => 'Не введен логин пользователя или пароль',
    'A password is required for user [], none given.'=> 'Не введен логин пользователя или пароль',
    'A user already exists with login [], logins must be unique for users.' => 'Пользователь с указанным логином уже существует, логин должен быть уникален для каждого пользователя',
    'Cannot attempt activation on an already activated user.' => 'Невозможно выполнить активацию для уже активного пользователя',
    'A hasher has not been provided for the user.'=> 'Hash-код не может быть предоставлен пользователю',
    'Invalid value [] for permission [] given.' => 'Предоставлено некорректное значение для прав пользователя',
    'Cannot JSON decode permissions [].'=> 'Невозможно декодировать права при помощи JSON',
];
<?php

function user_register($CSRFtoken, $username, $password, $password_confirm, $first_name, $last_name, $email, $picture_url)
{
    csrf_val($CSRFtoken);

    $username = check_data($username, true, 'Username', true, true, '/user/register');
    $password = check_data($password, true, 'Password', true, true, '/user/register');
    $password_confirm = check_data($password_confirm, true, 'Password Confirm', true, true, '/user/register');
    $first_name = check_data($first_name, true, 'First Name', true, true, '/user/register');
    $last_name = check_data($last_name, true, 'Last Name', true, true, '/user/register');
    $email = check_data($email, true, 'Email', true, true, '/user/register');
    $picture_url = check_data($picture_url, false, '', true);
    $created = date("Y-m-d H:i:s");

    // Check if username is taken
    $existing_username = sql_select('users', 'id', "username='{$username}'", false);
    if ($existing_username->num_rows > 0) {
        redirect('/user/register', 'Username is taken, please choose another.');
    }

    // Check if email is taken
    $existing_email = sql_select('users', 'id', "email='{$email}'", false);
    if ($existing_email->num_rows > 0) {
        redirect('/user/register', 'Email is taken, please choose another.');
    }

    // Check if passwords match
    if ($password !== $password_confirm) {
        redirect('/user/register', 'Passwords don\'t match.');
    }

    // Hash password
    $password = password_hash($password, PASSWORD_BCRYPT);

    sql_insert('users', [
        'username' => $username,
        'password' => $password,
        'first_name' => $first_name,
        'last_name' => $last_name,
        'email' => $email,
        'email_verified' => '0',
        'picture_url' => $picture_url,
        'created' => $created,
    ]);

    redirect('/user/register', 'Account registered. Please verify your mail.');
}


function user_update($CSRFtoken, $user_id, $username, $password, $new_password, $new_password_confirm, $first_name, $last_name, $email, $picture_url)
{
    csrf_val($CSRFtoken);

    $username = check_data($username, true, 'Username', true, true, '/user/register');
    $password = check_data($password, true, 'Password', true, true, '/user/register');
    $new_password = check_data($new_password, true, 'Password Confirm', true, true, '/user/register');
    $new_password_confirm = check_data($new_password_confirm, true, 'Password Confirm', true, true, '/user/register');
    $first_name = check_data($first_name, true, 'First Name', true, true, '/user/register');
    $last_name = check_data($last_name, true, 'Last Name', true, true, '/user/register');
    $email = check_data($email, true, 'Email', true, true, '/user/register');
    $picture_url = check_data($picture_url, true, 'Picture', true, true, '/user/register');

    $user = sql_select('users', 'username,email,password', "username='{$username}' OR email='{$email}'", true);

    // Verify old password
    if (!password_verify($password, $user['password'])) {
        redirect('/user/register', 'Current password is incorrect.');
    }

    // Check if username is taken
    if ($username != $user['username']) {
        $existing_username = sql_select('users', 'id', "username='{$username}'", false);
        if ($existing_username->num_rows > 0) {
            redirect('/user/settings', 'Username is taken, please choose another.');
        }
    }

    // Check if email is taken
    if ($email != $user['email']) {
        $existing_email = sql_select('users', 'id', "email='{$email}'", false);
        if ($existing_email->num_rows > 0) {
            redirect('/user/settings', 'Email is taken, please choose another.');
        }
    }

    // Check if user wants to update password
    if ($new_password === $new_password_confirm) {
        $new_password = password_hash($new_password, PASSWORD_BCRYPT);

        sql_update('users', [
            'username' => $username,
            'password' => $new_password,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'picture_url' => $picture_url,
        ], "id='{$user_id}'");
    } else {
        sql_update('users', [
            'username' => $username,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'picture_url' => $picture_url,
        ], "id='{$user_id}'");
    }

    redirect('/user/register', 'Account updated.');
}


function user_delete($CSRFtoken, $user_id)
{
    csrf_val($CSRFtoken);

    $user_id = check_data($user_id, true, 'User ID', true, true, '/user/settings');

    sql_delete('users', "id='{$user_id}'");

    redirect('/?reset', 'Account deleted.');
}

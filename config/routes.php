<?php

use TLegasse\ReminderApp\User;
use TLegasse\ReminderApp\Cron;
use TLegasse\ReminderApp\Reminder;
use TLegasse\ReminderApp\Helper\Route;
use TLegasse\ReminderApp\Helper\Flash;


Route::beforeRoute(function(){
    include '../src/Views/elements/header.php';
});

Route::afterRoute(function(){
    include '../src/Views/elements/footer.php';
});

Route::add('/',function(){
    include '../src/Views/home.php';
});

Route::add('/app',function(){
    $user = new User();
    $logged_in_user = $user->isLoggedIn();
    if($logged_in_user) {
        $reminder = new Reminder($logged_in_user);
        $reminders = $reminder->findAll();
        include '../src/Views/Reminder/reminder-app-main.php';
    } else {
        header('Location: /');
    }
});

Route::add('/reminder-edit/([0-9]*)',function($var1){
    $user = new User();
    $logged_in_user = $user->isLoggedIn();
    if($logged_in_user) {
        $reminder = new Reminder($logged_in_user);
        $reminder_to_edit = $reminder->read($var1);
        include '../src/Views/Reminder/reminder-edit.php';
    } else {
        header('Location: /');
    }
});

Route::add('/reminder-edit/([0-9]*)',function($var1){
    $user = new User();
    $logged_in_user = $user->isLoggedIn();
    if($logged_in_user) {
        $reminder = new Reminder($logged_in_user);
        $reminder->update(
            $var1,
            $_POST['title'],
            $_POST['body'],
            $_POST['time_to_trigger'],
            $_POST['status']
        );
        header('Location: /app');
    } else {
        header('Location: /');
    }
},'post');

Route::add('/reminder-create',function(){
    $user = new User();
    $logged_in_user = $user->isLoggedIn();
    if($logged_in_user) {
        include '../src/Views/Reminder/reminder-create.php';
    } else {
        header('Location: /');
    }
});

Route::add('/reminder-create',function(){
    $user = new User();
    $logged_in_user = $user->isLoggedIn();
    if($logged_in_user) {
        $reminder = new Reminder($logged_in_user);
        $reminder->create(
            $_POST['title'],
            $_POST['body'],
            $_POST['time_to_trigger'],
            $_POST['status']
        );
        header('Location: /app');
    } else {
        header('Location: /');
    }
},'post');

Route::add('/reminder-delete/([0-9]*)',function($var1){
    $user = new User();
    $logged_in_user = $user->isLoggedIn();
    if($logged_in_user) {
        $reminder = new Reminder($logged_in_user);
        $reminder_to_edit = $reminder->delete($var1);
        header('Location: /app');
    } else {
        header('Location: /');
    }
});

Route::add('/login',function(){
    include '../src/Views/User/login.php';
});

Route::add('/login',function(){
    $user = new User(
        $_POST['email'],
        $_POST['pass']
    );
    if(!$user->login())
    {
        session_destroy();
        Flash::set('Your email address and password did not match our files. Please try again or reset your password');
    } else {
        header('Location: /app');
    }
},'post');

Route::add('/register',function(){
    include '../src/Views/User/register.php';
});

Route::add('/register',function(){
    $user = new User(
        $_POST['email'],
        $_POST['pass'],
        $_POST['pass_confirm']
    );
    if($user->register()) {
        header('Location: /');
    } else {
        include '../src/Views/User/register.php';
    }
},'post');

Route::add('/logout',function(){
    session_destroy();
    header('Location: /');
});

Route::add('/confirm/([A-Za-z0-9]*)',function($var1){
    $user = new User();
    $user->confirm($var1);
});

Route::add('/reset-password',function(){
    include '../src/Views/User/reset-password-email.php';
});

Route::add('/reset-password',function(){
    $user = new User();
    $user->requestPasswordResetLink($_POST['email']);
    header('Location: /');
},'post');

Route::add('/reset-password/([A-Za-z0-9]*)',function($var1){
    $user = new User();
    if($user->displayResetPassword($var1))
    {
        include '../src/Views/User/reset-password.php';
    } else {
        Flash::set('There was a problem accessing your reset link. Please try again.');
        header('Location: /');
    }
});

Route::add('/reset-password/([A-Za-z0-9]*)',function($var1){
    $user = new User(
        null,
        $_POST['pass'],
        $_POST['pass_confirm']
    );
    if($user->resetPassword($var1))
    {
        header('Location: /app');
    } else {
        header('Location: /');
    }
},'post');

Route::pathNotFound(function(){
    include '../public/page-not-found.php';
});

Route::add('/heartbeat',function(){
    $cron = Cron::get();
    $cron->run();
});


Route::run('/');
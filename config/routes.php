<?php

use TLegasse\ReminderApp\User;
use TLegasse\ReminderApp\Cron;
use TLegasse\ReminderApp\Reminder;
use TLegasse\ReminderApp\Helper\Route;
use TLegasse\ReminderApp\Helper\Flash;


/**
 * Below are the app's routes.
 */

// Before route is run.
Route::beforeRoute(function(){
    include '../Views/elements/header.php';
});

// After route is run
Route::afterRoute(function(){
    include '../Views/elements/footer.php';
});

// Nothing found
Route::pathNotFound(function(){
    include 'page-not-found.php';
});


/**
 * General routes
 */

// Default route
Route::add('/',function(){
    include '../Views/home.php';
});

// Cron heartbeat
Route::add('/heartbeat',function(){
    $cron = Cron::get();
    $cron->run();
});


/**
 * Reminder specific routes
 */

// Our app page
Route::add('/app',function(){
    // Getting the logged in user
    $user = new User();
    $logged_in_user = $user->isLoggedIn();

    if($logged_in_user) {
        // Logged in user exists
        // Get reminder object with their ID referenced
        $reminder = new Reminder($logged_in_user);

        // Store reminders found.
        $reminders = $reminder->findAll();

        // Inncluding our template file
        include '../Views/Reminder/reminder-app-main.php';
    } else {
        header('Location: /');
    }
});

// Reminder edit get action
Route::add('/reminder-edit/([0-9]*)',function($var1){
    // Getting the logged in user
    $user = new User();
    $logged_in_user = $user->isLoggedIn();

    if($logged_in_user) {
        // Logged in user exists
        // Stored reminder exists
        $reminder = new Reminder($logged_in_user);
        // Set it for template
        $reminder_to_edit = $reminder->read($var1);
        include '../Views/Reminder/reminder-edit.php';
    } else {
        header('Location: /');
    }
});

// Reminder edit post action
Route::add('/reminder-edit/([0-9]*)',function($var1){
    // Getting the logged in user
    $user = new User();
    $logged_in_user = $user->isLoggedIn();

    if($logged_in_user) {
        // Logged in user exists
        $reminder = new Reminder($logged_in_user);

        // Saving reminder data
        $reminder->update(
            $var1,
            $_POST['title'],
            $_POST['body'],
            $_POST['time_to_trigger'],
            $_POST['status']
        );
        // Redirecting them to the main dash
        header('Location: /app');
    } else {
        header('Location: /');
    }
},'post');

// Reminder create get action
Route::add('/reminder-create',function(){
    // Getting the logged in user
    $user = new User();
    $logged_in_user = $user->isLoggedIn();

    if($logged_in_user) {
        // Logged in user exists
        include '../Views/Reminder/reminder-create.php';
    } else {
        header('Location: /');
    }
});

// Reminder create post action
Route::add('/reminder-create',function(){
    // Getting the logged in user
    $user = new User();
    $logged_in_user = $user->isLoggedIn();

    if($logged_in_user) {
        // Logged in user exists
        $reminder = new Reminder($logged_in_user);
        // Creating a new reminder
        $reminder->create(
            $_POST['title'],
            $_POST['body'],
            $_POST['time_to_trigger'],
            $_POST['status']
        );
        // Redirecting to dash.
        header('Location: /app');
    } else {
        header('Location: /');
    }
},'post');

// Reminder delete get action
Route::add('/reminder-delete/([0-9]*)',function($var1){
    // Getting the logged in user
    $user = new User();
    $logged_in_user = $user->isLoggedIn();

    if($logged_in_user) {
        // Logged in user exists
        $reminder = new Reminder($logged_in_user);

        // Deleting reminder
        $reminder->delete($var1);

        // Directing to dash
        header('Location: /app');
    } else {
        header('Location: /');
    }
});


/**
 * User specific routes.
 */
// Login get action
Route::add('/login',function(){
    // Including the form
    include '../Views/User/login.php';
});

// Login post action
Route::add('/login',function(){
    // Setting up user
    $user = new User(
        $_POST['email'],
        $_POST['pass']
    );

    if(!$user->login())
    {
        //Looks like login failed
        session_destroy();
        Flash::set('Your email address and password did not match our files. Please try again or reset your password');
    } else {
        header('Location: /app');
    }
},'post');

// Registration form get action
Route::add('/register',function(){
    include '../Views/User/register.php';
});

// Registration form post action
Route::add('/register',function(){
    // Setting up new user
    $user = new User(
        $_POST['email'],
        $_POST['pass'],
        $_POST['pass_confirm']
    );

    // Registering them
    if($user->register()) {
        // Redirecting home
        header('Location: /');
    } else {
        // Oops, something happened.
        include '../Views/User/register.php';
    }
},'post');

// Logout action, destroy session
Route::add('/logout',function(){
    session_destroy();
    header('Location: /');
});

// Registration confirm page
Route::add('/confirm/([A-Za-z0-9]*)',function($var1){
    $user = new User();
    $user->confirm($var1);
});

// Reset password form
Route::add('/reset-password',function(){
    include '../Views/User/reset-password-email.php';
});

// Reset password post action
Route::add('/reset-password',function(){
    // Getting user
    $user = new User();

    // Generating link and emialing
    $user->requestPasswordResetLink($_POST['email']);

    // Redirecting home
    header('Location: /');
},'post');

// Reset password page
Route::add('/reset-password/([A-Za-z0-9]*)',function($var1){
    $user = new User();

    // If user has an active reset flag
    if($user->displayResetPassword($var1))
    {
        // Include the form
        include '../Views/User/reset-password.php';
    } else {
        // Otherwise go home
        Flash::set('There was a problem accessing your reset link. Please try again.');
        header('Location: /');
    }
});

// Reset form post action
Route::add('/reset-password/([A-Za-z0-9]*)',function($var1){
    // Adding new password
    $user = new User(
        null,
        $_POST['pass'],
        $_POST['pass_confirm']
    );

    // Calling reset method
    if($user->resetPassword($var1))
    {
        // Success
        header('Location: /app');
    } else {
        // Failure
        header('Location: /');
    }
},'post');

// Our route run with a specified base-path
Route::run('/');
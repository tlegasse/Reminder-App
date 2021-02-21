<?php
use TLegasse\ReminderApp\Helper\Flash;
use TLegasse\ReminderApp\User;
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link rel="stylesheet" href="/css/bootstrap.css">
    <link rel="stylesheet" href="/css/bootstrap-theme.css">
</head>
<body>
<div class="container"><?php Flash::get(); ?></div>
<div class="container">
    <div class="header clearfix">
        <nav>
            <ul class="nav nav-pills pull-right">
                <?php if(User::isLoggedIn()): ?>
                    <li role="presentation" class="active"><a href="/">Home</a></li>
                    <li role="presentation"><a href="/app">My Reminders</a></li>
                    <li role="presentation"><a href="/reminder-create">Add Reminder</a></li>
                    <li role="presentation"><a href="/logout">Logout</a></li>
                <?php else: ?>
                    <li role="presentation" class="active"><a href="/">Home</a></li>
                    <li role="presentation"><a href="/login">Login</a></li>
                <?php endif; ?>
            </ul>
        </nav>
        <h3 class="text-muted">My Reminder App</h3>
    </div>
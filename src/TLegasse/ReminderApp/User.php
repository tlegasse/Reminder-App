<?php

namespace TLegasse\ReminderApp;

use RedBeanPHP\RedException;
use TLegasse\ReminderApp\Helper\Flash;
use TLegasse\ReminderApp\Helper\Email;
use RedBeanPHP\R;

class User
{
    private $email;
    private $salt;
    private $pass;
    private $pass_confirm;

    public function __construct($email = null, $pass = null, $pass_confirm = null)
    {
        global $config;

        // Getting our salt from configs.
        $this->salt  = $config['security']['salt'];

        // Email address
        $this->email = $email;

        // Password
        $this->pass  = $pass;

        // Password confirm
        $this->pass_confirm  = $pass_confirm;
    }

    public function register()
    {
        if($this->validateEmail() && $this->validatePassword())
        {
            $user = R::find('user','email LIKE ? ', [$this->email]);
            if($user)
            {
                Flash::set('Please ensure that you aren\'t already registered.');
                return false;
            } else {
                $confirm_flag = md5(time());
                $user = R::dispense('user');
                $user->email = $this->email;
                $user->pass = md5($this->salt . $this->pass);
                $user->status = 0;
                $date = new \DateTime();
                $user->created = $date->format('Y-m-d H:i:s');
                $user->flag = $confirm_flag;
                $user_id = R::store($user);

                if(is_int($user_id) && $user_id > 0)
                {
                    $email = new Email(
                        $this->email,
                        'Your account',
                        'You can confirm your account by following this link: http://localhost/confirm/' . $confirm_flag
                    );
                    $email->send();
                    Flash::set('User created. Please check your email for confirmation','success');
                } else {
                    Flash::set('User creation failed. Please try again.');
                }
            }
        }
    }

    public function isLoggedIn()
    {
        return (isset($_SESSION['user']) ? $_SESSION['user']['id'] : false);
    }

    public function confirm($flag)
    {
        $user = R::findOne('user','flag LIKE ? ', [$flag]);

        if(!$user)
        {
            return false;
        }

        try {
            $user->status = 1;
            $user_id = R::store($user);
            $_SESSION['user'] = $user_id;
            Flash::set('Your account has been confirmed!','success');
        } catch(RedException $e)
        {
            Flash::set('An error was encountered while confirming your account');
        }
    }

    public function displayResetPassword($flag)
    {
        $user = R::findOne('user','reset_flag LIKE ? ', [$flag]);
        return (!empty($user));
    }

    public function requestPasswordResetLink($email)
    {
        $user = R::findOne('user','email LIKE ? ', [$email]);

        if(!$user)
        {
            return false;
        }

        try {
            $reset_link = md5(time());
            $user->reset_flag = md5(time());
            R::store($user);

            $email = new Email(
                $user->email,
                'Password Reset Link',
                'Please visit the following link to reset your password http://localhost/' . $reset_link
            );

            $email->send();
            Flash::set('Your password reset link has been sent!','success');
        } catch(RedException $e)
        {
            Flash::set('An error was encountered while accessing your account');
        }
    }

    public function validateEmail()
    {
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL))
        {
            Flash::set('Email invalid');
            return false;
        } else {
            return true;
        }
    }

    public function validatePassword()
    {
        $pass_error = '';
        if (strlen($this->pass) <= 8)
        {
            $pass_error = "Your Password Must Contain At Least 8 Characters!";
        } elseif(!preg_match("#[0-9]+#",$this->pass)) {
            $pass_error = "Your Password Must Contain At Least 1 Number!";
        } elseif(!preg_match("#[A-Z]+#",$this->pass)) {
            $pass_error = "Your Password Must Contain At Least 1 Capital Letter!";
        } elseif(!preg_match("#[a-z]+#",$this->pass)) {
            $pass_error = "Your Password Must Contain At Least 1 Lowercase Letter!";
        } else {
            $pass_error = "Please Check You've Entered Or Confirmed Your Password!";
        }

        if($this->pass != $this->pass_confirm)
        {
            $pass_error = "Please ensure that the password and confirmation password both are of the same value.";
        }

        if(!strlen($pass_error))
        {
            Flash::set($pass_error);
            return false;
        } else {
            return true;
        }
    }

    public function resetPassword($reset_flag)
    {
        $user = R::findOne('user','reset_flag LIKE ? ', [$reset_flag]);
        if($this->validatePassword() && !empty($user))
        {
            try {
                $new_pass_hash = md5($this->salt . $this->pass);
                $user->pass = $new_pass_hash;
                $user->reset_flag = '';
                $user_id = R::store($user);
                $_SESSION['user'] = $user_id;
                Flash::set('Your password has been updated!','success');
                return true;
            } catch (RedException $e) {
                Flash::set('There has been an issue updating your password. Please contact technical support');
            } catch (\Exception $e) {
                Flash::set('There has been an issue updating your password. Please contact technical support');
            }
        } else {
            return false;
        }
    }

    public function login()
    {
        $md5 = md5($this->salt . $this->pass);
        $user = R::findOne('user','pass LIKE ? ', [$md5]);
        if($user)
        {
            $_SESSION['user'] = $user;
            Flash::set('You have been logged in.','success');
            return true;
        } else {
            Flash::set('Please check your email address and password and try again.');
            return false;
        }
    }
}
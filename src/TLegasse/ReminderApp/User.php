<?php

namespace TLegasse\ReminderApp;

use RedBeanPHP\RedException;
use TLegasse\ReminderApp\Helper\Flash;
use TLegasse\ReminderApp\Helper\Email;
use RedBeanPHP\R;

/**
 * Class User
 * All user management functionalities
 * @package TLegasse\ReminderApp
 */
class User
{
    private $email;
    private $salt;
    private $pass;
    private $pass_confirm;

    /**
     * User constructor.
     * @param null $email
     * @param null $pass
     * @param null $pass_confirm
     */
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

    /**
     * Register a new user from the stored properties.
     * @return false
     * @throws RedException\SQL
     */
    public function register()
    {
        // Ensuring that our email address validates and that our password is up to snuff.
        if($this->validateEmail() && $this->validatePassword())
        {
            // Making sure that a user with this email address doesn't already exist
            $user = R::find('user','email LIKE ? ', [$this->email]);

            if($user)
            {
                // If one is found, report the issue and return.
                Flash::set('Please ensure that you aren\'t already registered.');
                return false;
            } else {
                // Setting up our datetime object for use below.
                $date          = new \DateTime();
                // All important user details.
                $confirm_flag  = md5(time());
                $user          = R::dispense('user');
                $user->email   = $this->email;
                $user->pass    = md5($this->salt . $this->pass);
                $user->status  = 0;
                $user->created = $date->format('Y-m-d H:i:s');
                $user->flag    = $confirm_flag;
                $user_id       = R::store($user);

                // If the user_id is an integer and is greater than zero
                if(is_int($user_id) && $user_id > 0)
                {
                    // Send a new email with the url to confirm registration at
                    $email = new Email(
                        $this->email,
                        'Your account',
                        'You can confirm your account by following this link: http://localhost/confirm/' . $confirm_flag
                    );
                    // And sending
                    $email->send();
                    // Letting the user know
                    Flash::set('User created. Please check your email for confirmation','success');
                } else {
                    // Something happened, let the user know.
                    Flash::set('User creation failed. Please try again.');
                }
            }
        }
    }

    /**
     * Determines if the user is logged in already.
     * @return false|mixed
     */
    public function isLoggedIn()
    {
        return (isset($_SESSION['user']) ? $_SESSION['user']['id'] : false);
    }

    /**
     * This method confirms uer creation. It takes a flag to reference.
     * @param $flag
     * @return false
     */
    public function confirm($flag)
    {
        $user = R::findOne('user','flag LIKE ? ', [$flag]);

        try {
            // If no user could be located, throw an exception
            if(!$user)
            {
                throw new \Exception('Something happened');
            }

            // Setting user status to 1 (so they can log in)
            $user->status = 1;

            // Storing the updated user
            $user_id = R::store($user);

            // Logging the user in.
            $_SESSION['user'] = $user_id;

            // Letting the user know they are now logged in.
            Flash::set('Your account has been confirmed!','success');
        } catch(RedException $e)
        {
            Flash::set('An error was encountered while confirming your account');
        }
    }

    /**
     * Ensuring that the flag referenced exists before sending the password reset form.
     * @param $flag
     * @return bool
     */
    public function displayResetPassword($flag)
    {
        // Finding our user by reset flag
        $user = R::findOne('user','reset_flag LIKE ? ', [$flag]);

        // Returning not-empty
        return (!empty($user));
    }

    /**
     * Processes the password reset request
     * @param $email
     * @return false
     */
    public function requestPasswordResetLink($email)
    {
        // Finding the user
        $user = R::findOne('user','email LIKE ? ', [$email]);


        try {
            if(!$user)
            {
                throw new \Exception('Something failed');
            }

            // Saving a new password reset flag.
            $user->reset_flag = md5(time());

            // Updating the user
            R::store($user);

            // Prepping to send them an email.
            $email = new Email(
                $user->email,
                'Password Reset Link',
                'Please visit the following link to reset your password http://localhost/' . $user->reset_flag
            );

            // Sending
            $email->send();

            // Letting the user know.
            Flash::set('Your password reset link has been sent!','success');
        } catch(RedException $e)
        {
            // Catching a redbean exception
            Flash::set('An error was encountered while accessing your account');
        } catch(\Exception $e)
        {
            // Catching a general exception
            Flash::set('An error was encountered while accessing your account');
        }
    }

    /**
     * Validates an email.
     * @return bool
     */
    public function validateEmail()
    {
        // Checking to ensure that the email is alright.
        if (!filter_var($this->email, FILTER_VALIDATE_EMAIL))
        {
            // Letting the user know we don't like the email address
            Flash::set('Email invalid');
            return false;
        } else {
            // Looks good!
            return true;
        }
    }

    /**
     * General password creation handling
     * @return bool
     */
    public function validatePassword()
    {
        try {
            if (strlen($this->pass) <= 8) {
                // Less than 8
                throw new \Exception("Your Password Must Contain At Least 8 Characters!");
            } elseif (!preg_match("#[0-9]+#", $this->pass)) {
                // Doesn't have any ints
                throw new \Exception("Your Password Must Contain At Least 1 Number!");
            } elseif (!preg_match("#[A-Z]+#", $this->pass)) {
                // Doesn't have any caps
                throw new \Exception("Your Password Must Contain At Least 1 Capital Letter!");
            } elseif (!preg_match("#[a-z]+#", $this->pass)) {
                // No lowercases
                throw new \Exception("Your Password Must Contain At Least 1 Lowercase Letter!");
            } else {
                // No password at all!
                throw new \Exception("Please Check You've Entered Or Confirmed Your Password!");
            }

            // Password and password confirm don't match.
            if ($this->pass != $this->pass_confirm) {
                throw new \Exception("Please ensure that the password and confirmation password both are of the same value.");
            }

        } catch (\Exception $e)
        {
            Flash::set($e);
            return false;
        }

        return true;
    }

    /**
     * Processes a password reset
     * @param $reset_flag
     * @return bool
     */
    public function resetPassword($reset_flag)
    {
        // Gets user by reset flag
        $user = R::findOne('user','reset_flag LIKE ? ', [$reset_flag]);

        try {
            // Ensures that password validates and that the user isn't set
            if(!$this->validatePassword() || empty($user))
            {
                throw new \Exception('Something happened.');
            }

            // Sets the new password hash.
            $user->pass = md5($this->salt . $this->pass);

            // Clears reset flag
            $user->reset_flag = '';

            // Stores user
            $user_id = R::store($user);

            // Logs them in
            $_SESSION['user'] = $user_id;

            // Lets them know everything went well
            Flash::set('Your password has been updated!','success');

            return true;
        } catch (RedException $e) {
            // Redbean exception
            Flash::set('There has been an issue updating your password. Please contact technical support');
        } catch (\Exception $e) {
            // General exception
            Flash::set('There has been an issue updating your password. Please contact technical support');
        }
    }

    // Logs the user in
    public function login()
    {
        $md5 = md5($this->salt . $this->pass);
        $user = R::findOne('user','pass LIKE ? ', [$md5]);
        if($user && $user->status == 1)
        {
            // Setting session var
            $_SESSION['user'] = $user;

            // Setting flash to let them know everything worked.
            Flash::set('You have been logged in.','success');
            return true;
        } else {

            // Error, let them know.
            Flash::set('Please check your email address and password and try again.');
            return false;
        }
    }
}
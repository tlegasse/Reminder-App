<?php

namespace TLegasse\ReminderApp;

use TLegasse\ReminderApp\Helper\Email;
use RedBeanPHP\RedException;
use RedBeanPHP\R;

class Cron
{
    private static $instance = null;
    private $time_to_match;

    private function __construct($time_to_match)
    {
        $this->time_to_match = $time_to_match;
    }

    public function get()
    {
        if(self::$instance == null)
        {
            $date = new \Datetime();
            $time_to_match = $date->format('Y-m-d H:i:00');
            self::$instance = new Cron($time_to_match);
        }
        return self::$instance;
    }

    public function run()
    {
        try {
            $reminders_to_trigger = R::find('reminder',' WHERE time_to_trigger LIKE ?', [$this->time_to_match] );
            $reminders_to_trigger = R::loadJoined($reminders_to_trigger,'user');
            if(empty($reminders_to_trigger))
            {
                return false;
            }
            foreach ($reminders_to_trigger as $reminder)
            {
                if(!isset($reminder['user']['email'])) continue;
                $subject = $reminder['title'];
                $body = $reminder['body'];
                $user_email = $reminder['user']['email'];
                $email = new Email($user_email,$subject,$body);
                $email->send();
            }
        } catch (\RedException $e) {
            die('failed');
        }
    }
}
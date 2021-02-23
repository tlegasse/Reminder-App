<?php

namespace TLegasse\ReminderApp;

use TLegasse\ReminderApp\Helper\Email;
use RedBeanPHP\RedException;
use RedBeanPHP\R;

/**
 * Class Cron
 * This is our cron singleton class, used for all things heartbeat and managing emailing.
 * @package TLegasse\ReminderApp
 */
class Cron
{
    private static $instance = null;
    private $time_to_match;

    /**
     * Cron constructor.
     * Takes time_to_match, a time like Y-m-d H:i:00
     * @param $time_to_match
     */
    private function __construct($time_to_match)
    {
        $this->time_to_match = $time_to_match;
    }

    /**
     * Gets our cron singleton
     * @return Cron|null
     */
    public function get()
    {
        if(self::$instance == null)
        {
            $date = new \Datetime();
            // Setting our time_to_match, will be used when run is called to select rows from the database.
            $time_to_match = $date->format('Y-m-d H:i:00');
            self::$instance = new Cron($time_to_match);
        }
        return self::$instance;
    }

    /**
     * This is our main run method, and should happen minutely.
     * @return false
     * @throws \Exception handles our general cron failure.
     */
    public function run()
    {
        try {
            // Getting all relevant reminders.
            $reminders_to_trigger = R::find(
                'reminder',
                ' WHERE time_to_trigger LIKE ?', [$this->time_to_match]
            );

            // Loads users to the corresponding reminders.
            $reminders_to_trigger = R::loadJoined(
                $reminders_to_trigger,
                'user'
            );

            // Returning false if no reminders could be found.
            if(empty($reminders_to_trigger))
            {
                return false;
            }

            // Looping through our reminders
            foreach ($reminders_to_trigger as $reminder)
            {
                // Ensuring that our user has an associated email, or continues the loop
                if(!isset($reminder['user']['email'])) continue;

                // Instantiating our email class to send our email.
                $email = new Email(
                    $reminder['user']['email'], // Comes from the user table
                    $reminder['title'],
                    $reminder['body']
                );

                // Sending our email.
                $email->send();
            }
        } catch (\RedException $e) {
            die('failed');
        }
    }
}
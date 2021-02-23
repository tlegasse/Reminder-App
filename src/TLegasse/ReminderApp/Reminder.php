<?php
namespace TLegasse\ReminderApp;

use RedBeanPHP\RedException;
use TLegasse\ReminderApp\Helper\Flash;
use RedBeanPHP\R;

/**
 * Class Reminder
 * @package TLegasse\ReminderApp
 */
class Reminder {
    public $user_id;

    /**
     * Reminder constructor.
     * @param $user_id
     */
    public function __construct($user_id)
    {
        // Only thing we really need to do is set our user_id. This comes from the session.
        $this->user_id = $user_id;
    }

    // Getting all reminders.
    public function findAll()
    {
        // Really simple, just returning an array of found objects by user_id
        return R::find('reminder','user_id ', [$this->user_id]);
    }

    /**
     * Saving our new reminder
     * @param $title
     * @param $body
     * @param $time_to_trigger
     * @param $status
     * @return bool
     */
    public function create($title,$body,$time_to_trigger,$status)
    {
        try {
            // Dispensing a new reminder bean
            $reminder = R::dispense('reminder');

            // Setting related properties.
            $reminder->title           = $title;
            $reminder->body            = $body;
            $reminder->time_to_trigger = $time_to_trigger;
            $reminder->status          = $status;
            $reminder->user_id         = $this->user_id;

            // Storing our reminder
            R::store($reminder);

            // Setting a flash for the user
            Flash::set('Your reminder has been created.','success');

            // And returning true
            return true;
        } catch(\Exception $e) {

            // Letting the user know that there was a problem.
            Flash::set('There was an error processing your request');
            return false;
        } catch(RedException $e) {

            // Letting the user know that there was a problem.
            Flash::set('There was an error processing your request');
            return false;
        }
    }

    /**
     * Reading our reminder by ID
     * @param $reminder_id
     * @return \RedBeanPHP\OODBBean|NULL
     */
    public function read($reminder_id)
    {
        // Just getting the reminder by ID and referencing the user_id
        return R::findOne('reminder','id = ? AND user_id = ?', [$reminder_id,$this->user_id]);
    }

    /**
     * Delting the referenced reminder
     * @param $reminder_id
     * @return bool
     */
    public function delete($reminder_id)
    {
        try {
            // Finding the reminder specified referencing also the user_id
            $reminder_to_delete = R::findOne('reminder','id = ? AND user_id = ?', [$reminder_id,$this->user_id]);

            // Trashing it.
            R::trash($reminder_to_delete);

            // Letting the user know.
            Flash::set('Your reminder has been removed','success');

            // Returning true
            return true;
        } catch(RedException $e) {

            // Handing any redbean exception.
            Flash::set('There was an error processing your request');
            return false;
        }

    }

    /**
     * Updates our reminder with the related data.
     * @param $reminder_id
     * @param $title
     * @param $body
     * @param $time_to_trigger
     * @param $status
     * @return bool
     */
    public function update($reminder_id,$title,$body,$time_to_trigger,$status)
    {
        try {
            // Finding the reminder in question.
            $reminder = R::findOne('reminder', 'id = ? AND user_id = ?', [$reminder_id, $this->user_id]);

            if (empty($reminder))
            {
                throw new \Exception('reminder not found');
            }

            // Updating our reminder object with appropriate data.
            $reminder->title           = $title;
            $reminder->body            = $body;
            $reminder->time_to_trigger = $time_to_trigger;
            $reminder->status          = $status;

            // Saving our reminder
            R::store($reminder);

            // Setting our flash.
            Flash::set('Your reminder has been saved.','success');

            // Returning true.
            return true;
        } catch(\Exception $e) {

            // General exception detected
            Flash::set('There was an error processing your request');
            return false;
        } catch(RedException $e) {

            // Redbean exception handling
            Flash::set('There was an error processing your request');
            return false;
        }
    }
}
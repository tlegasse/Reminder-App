<?php
namespace TLegasse\ReminderApp;

use RedBeanPHP\RedException;
use TLegasse\ReminderApp\Helper\Flash;
use RedBeanPHP\R;

class Reminder {
    public $user_id;

    public function __construct($user_id)
    {
        $this->user_id = $user_id;
    }

    public function findAll()
    {
        $reminders = R::find('reminder','user_id ', [$this->user_id]);
        return $reminders;
    }

    public function create($title,$body,$time_to_trigger,$status)
    {
        try {
            $reminder = R::dispense('reminder');
            $reminder->title = $title;
            $reminder->body = $body;
            $reminder->time_to_trigger = $time_to_trigger;
            $reminder->status = $status;
            $reminder->user_id = $this->user_id;
            R::store($reminder);
            Flash::set('Your reminder has been created.','success');
            return true;
        } catch(\Exception $e) {
            Flash::set('There was an error processing your request');
            return false;
        }
    }

    public function read($reminder_id)
    {
        return R::findOne('reminder','id = ? AND user_id = ?', [$reminder_id,$this->user_id]);
    }

    public function delete($reminder_id)
    {
        try {
            $reminder_to_delete = R::findOne('reminder','id = ? AND user_id = ?', [$reminder_id,$this->user_id]);
            R::trash($reminder_to_delete);
            Flash::set('Your reminder has been removed','success');
            return true;
        } catch(RedException $e) {
            Flash::set('There was an error processing your request');
            return false;
        }

    }

    public function update($reminder_id,$title,$body,$time_to_trigger,$status)
    {
        try {
            $reminder = R::findOne('reminder', 'id = ? AND user_id = ?', [$reminder_id, $this->user_id]);
            $reminder->title = $title;
            $reminder->body = $body;
            $reminder->time_to_trigger = $time_to_trigger;
            $reminder->status = $status;
            R::store($reminder);
            Flash::set('Your reminder has been saved.','success');
            return true;
        } catch(\Exception $e) {
            Flash::set('There was an error processing your request');
            return false;
        }
    }
}
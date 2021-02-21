<form action="" method="post">
    <div class="container">
        <label for="reminder-title">Title</label>
        <input
            type="text"
            id="reminder-title"
            name="title"
            value="<?= $reminder_to_edit['title'] ?>"
        >
    </div>

    <div class="container">
        <label for="reminder-body">Body</label>
        <textarea
            name="body"
            id="reminder-body"
            cols="30"
            rows="10"
        ><?= $reminder_to_edit['body'] ?></textarea>
    </div>

    <div class="container">
        <label for="reminder-status">Status</label>
        <input
            id="reminder-status"
            type="checkbox"
            checked="<?= $reminder_to_edit['status'] ? 'checked' : '' ?>"
            name="status"
        >
    </div>

    <div class="container">
        <label for="time_to_trigger">Time to Trigger</label>
        <?php
            $date = new \DateTime();
            $min_date = $date->format('Y-m-d') . 'T' . $date->format('H:i');
            $date_to_trigger_current = new \DateTime($reminder_to_edit['time_to_trigger']);
            $date_to_trigger_prepared = $date->format('Y-m-d') . 'T' . $date->format('H:i');
        ?>
        <input
            type="datetime-local"
            id="time_to_trigger"
            name="time_to_trigger"
            min="<?= $min_date ?>"
            value="<?= str_replace(' ','T',$date_to_trigger_prepared) ?>"
        >
    </div>
    <input type="submit">
</form>

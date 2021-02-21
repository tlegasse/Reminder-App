<table class="table">
    <thead>
        <tr>
            <th scope="col">ID</th>
            <th scope="col">Title</th>
            <th scope="col">Status</th>
            <th scope="col">When to Remind</th>
            <th scope="col">Edit</th>
            <th scope="col">Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($reminders as $reminder) { ?>
            <tr>
                <th scope="row"><?= $reminder['id'] ?></th>
                <td><?= $reminder['title'] ?></td>
                <td><?= ($reminder['status'] ? "Active" : "Inactive") ?></td>
                <td><?= $reminder['time_to_trigger'] ?></td>
                <td><a href="/reminder-edit/<?= $reminder['id'] ?>">Edit</a></a></td>
                <td><a href="/reminder-delete/<?= $reminder['id'] ?>">Delete</a></a></td>
            </tr>
        <?php } ?>
    </tbody>
</table>
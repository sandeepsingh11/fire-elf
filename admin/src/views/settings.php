<?php require __DIR__ . '/partials/header.php' ?>

<h1>Settings</h1>

<!-- change username -->
<form action="/settings/update" method="post">
    <h3>Change Username</h3>

    <p>Current username: <strong><?= $this->User->getUsername() ?></strong></p>
    
    <label for="new-username">Input new username:</label>
    <input type="text" name="new-username" id="new-username">

    <input type="submit" name="update-username" value="Update">
    <input type="hidden" name="user-id" value="<?= $this->Session->getUserId() ?>">
</form>

<!-- change pass -->
<form action="/settings/update" method="post">
    <h3>Change Password</h3>

    <label for="current-password">Input current password:</label>
    <input type="password" name="current-password" id="current-password">

    <label for="new-password">Input new password:</label>
    <input type="password" name="new-password" id="new-password">

    <input type="submit" name="update-password" value="Update">
    <input type="hidden" name="user-id" value="<?= $this->Session->getUserId() ?>">
</form>


<!-- users -->
<h3>Users</h3>
<?php foreach ($this->User->getAllUsers() as $user): ?>
    <h4><?= $user['username'] ?></h4>

    <!-- delete user -->
    <form action="/settings/delete" method="post">
        <input type="hidden" name="user-id" value="<?= $user['id'] ?>">
        <input type="submit" name="delete-user" value="Delete">
    </form>
<?php endforeach ?>

<div>
    <p><a href="/register">Create a new user</a></p>
</div>

<?php require __DIR__ . '/partials/footer.php' ?>
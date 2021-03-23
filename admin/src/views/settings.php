<?php require __DIR__ . '/partials/header.php' ?>

<h1>Settings</h1>

<div class="flex flex-form">
    <!-- change username -->
    <form id="username-form" class="form form-secondary" action="/settings/update" method="post">
        <div class="form-title-container">
            <h2 class="form-title">Change Username</h2>
        </div>

        <div class="form-body-container">
            <span>Current username: <strong><?= $this->User->getUsername() ?></strong></span>
            
            <label for="new-username">Input new username:</label>
            <input type="text" name="new-username" id="new-username" required>
            <div id="username-err" class="input-err hide">Username must be 3 - 24 characters and not contain any special characters</div>

            <input type="submit" name="update-username" value="Update">
            
            <input type="hidden" name="user-id" value="<?= $this->Session->getUserId() ?>">
            <input type="hidden" name="csrf-token" value="<?= $_SESSION['csrf_token'] ?>">
        </div>
    </form>

    <!-- change pass -->
    <form id="password-form" class="form form-secondary" action="/settings/update" method="post">
        <div class="form-title-container">
            <h2 class="form-title">Change Password</h2>
        </div>

        <div class="form-body-container">
            <label for="current-password">Input current password:</label>
            <input type="password" name="current-password" id="current-password" required>

            <label for="new-password">Input new password:</label>
            <input type="password" name="new-password" id="new-password" required>
            <div id="password-err" class="input-err hide">Password must contain at least 1 lowercase letter, uppercase letter, and number, and must be at least 8 characters long</div>

            <input type="submit" name="update-password" value="Update">

            <input type="hidden" name="csrf-token" value="<?= $_SESSION['csrf_token'] ?>">
            <input type="hidden" name="user-id" value="<?= $this->Session->getUserId() ?>">
        </div>
    </form>
</div>


<!-- users -->
<h3>Users</h3>
<div class="flex flex-form">
    <?php foreach ($this->User->getAllUsers() as $user): ?>
        <!-- delete user -->
        <form class="form form-secondary form-delete" action="/settings/delete" method="post">
            <div class="form-title-container">
                <h2 class="form-title"><?= $user['username'] ?></h2>
            </div>

            <div class="form-body-container">
                <input type="hidden" name="user-id" value="<?= $user['id'] ?>">
                <input type="hidden" name="entry-name" value="<?= $this->escape($user['username']) ?>">

                <input type="submit" name="delete-user" value="Delete">
            </div>
        </form>
    <?php endforeach ?>
</div>

<div>
    <p><a href="/register">Create a new user</a></p>
</div>

<?php require __DIR__ . '/partials/footer.php' ?>



<script>
    var usernameForm = document.getElementById('username-form');
    var usernameErrEle = document.getElementById('username-err');

    usernameForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // check username validity
        var username = document.getElementById('new-username').value;
        if (!isValidUsername(username)) {
            usernameErrEle.classList.remove('hide');
            usernameErrEle.classList.add('show');
        }
        else {
            e.target.submit();
        }
    });



    var passwordForm = document.getElementById('password-form');
    var passwordErrEle = document.getElementById('password-err');

    passwordForm.addEventListener('submit', function(e) {
        e.preventDefault();

        // check password validity
        var password = document.getElementById('new-password').value;
        if (!isValidPassword(password)) {
            passwordErrEle.classList.remove('hide');
            passwordErrEle.classList.add('show');
        }
        else {
            e.target.submit();
        }
    });
</script>
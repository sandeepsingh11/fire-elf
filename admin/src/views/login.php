<?php require __DIR__ . '/partials/header.php' ?>

<form id="login-form" class="form" action="/login" method="post">
    <div class="form-title-container">
        <h2 class="form-title">Login</h2>
    </div>

    <div class="form-body-container">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <div id="username-err" class="input-err hide">Username must be 3 - 24 characters and not contain any special characters</div>

        <label for="pazz">Password:</label>
        <input type="password" name="pazz" id="pazz" required>

        <input type="submit" value="Login">

        <input type="hidden" name="csrf-token" value="<?= $_SESSION['csrf_token'] ?>">
    </div>
</form>

<?php require __DIR__ . '/partials/footer.php' ?>



<script>
    var form = document.getElementById('login-form');
    var usernameErrEle = document.getElementById('username-err');

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // check username validity
        var username = document.getElementById('username').value;
        if (!isValidUsername(username)) {
            usernameErrEle.classList.remove('hide');
            usernameErrEle.classList.add('show');
            
            return;
        }

        e.target.submit();
    })
</script>
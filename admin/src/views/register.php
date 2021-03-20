<?php require __DIR__ . '/partials/header.php' ?>

<form id="register-form" class="form" action="/register" method="post">
    <div class="form-title-container">
        <h2 class="form-title">Register</h2>
    </div>

    <div class="form-body-container">
        <label for="username">Username:</label>
        <input type="text" name="username" id="username" required>
        <div id="username-err" class="input-err hide">Username must be 3 - 24 characters and not contain any special characters</div>

        <label for="pazz">Password:</label>
        <input type="password" name="pazz" id="pazz" required>
        <div id="password-err" class="input-err hide">Password must contain at least 1 lowercase letter, uppercase letter, and number, and must be at least 8 characters long</div>

        <input type="submit" value="Register">

        <input type="hidden" name="csrf-token" value="<?= $_SESSION['csrf_token'] ?>">
    </div>
</form>

<?php require __DIR__ . '/partials/footer.php' ?>



<script>
    var form = document.getElementById('register-form');
    var usernameErrEle = document.getElementById('username-err');
    var passwordErrEle = document.getElementById('password-err');
    var usernameValid = false;
    var passwordValid = false;

    form.addEventListener('submit', function(e) {
        e.preventDefault();

        // check username validity
        var username = document.getElementById('username').value;
        if (!isValidUsername(username)) {
            usernameErrEle.classList.remove('hide');
            usernameErrEle.classList.add('show');

            usernameValid = false;
        }
        else {
            usernameErrEle.classList.remove('show');
            usernameErrEle.classList.add('hide');

            usernameValid = true;
        }

        // check password validity
        var password = document.getElementById('pazz').value;
        if (!isValidPassword(password)) {
            passwordErrEle.classList.remove('hide');
            passwordErrEle.classList.add('show');

            passwordValid = false;
        }
        else {
            passwordErrEle.classList.remove('show');
            passwordErrEle.classList.add('hide');

            passwordValid = true;
        }



        if ( (usernameValid) && (passwordValid) ) {
            e.target.submit();
        }
    })
</script>
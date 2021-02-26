<?php require __DIR__ . '/partials/header.php' ?>

<!-- https://code-boxx.com/simple-csrf-token-php/ -->
<h1>Loginnn</h1>

<form action="/login" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username">

    <label for="pazz">Password:</label>
    <input type="password" name="pazz" id="pazz">

    <input type="submit" value="Login">

    <input type="hidden" name="csrf-token" value="<?= $_SESSION['csrf_token'] ?>">
</form>

<?php require __DIR__ . '/partials/footer.php' ?>
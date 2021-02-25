<?php require __DIR__ . '/partials/header.php' ?>

<h1>Register</h1>

<form action="/register" method="post">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username">

    <label for="pazz">Password:</label>
    <input type="password" name="pazz" id="pazz">

    <input type="submit" value="Register">
</form>

<?php require __DIR__ . '/partials/footer.php' ?>
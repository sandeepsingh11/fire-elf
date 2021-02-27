<?php require __DIR__ . '/partials/header.php' ?>

<h1>Fire Elf</h1>
<h2>Hewwo <?= ($this->Session->isLoggedIn()) ? $this->User->getUsername($this->Session->getUserId()) : 'stranger' ?>!</h2>

<?php require __DIR__ . '/partials/footer.php' ?>
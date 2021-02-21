<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= ($this->page_title) ? $this->page_title . ' | ' . WEBSITE_NAME : WEBSITE_NAME ?>
    </title>
    
    <?php foreach ($this->css as $css): ?>
        <link rel="stylesheet" href="<?= $css ?>">
    <?php endforeach ?>
</head>
<body>
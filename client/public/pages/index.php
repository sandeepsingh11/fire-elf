<?php
require '../comp/nav.php'
?>

<h1>Hewwo World!</h1>
<h2>Client index</h2>
<?php
$demo = 0;
$test = 's';
?>
<p>Here is some <b>random text</b> in as a <em>paragraph</em></p>
<div>
    <a href="#">I do not link to anywhere</a>
</div>
<div>Added from the <b>editor</b>!<br></div>
<?php
for ($i = 0; $i < 5; $i++) {
    ?>
    <h2><?php echo $i ?></h2>
    <?php
}
?>

<script>console.log('henlo');</script>
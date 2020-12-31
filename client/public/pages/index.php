<?php
$pageTitle = "Home - Demo";
require '../comp/head.php';
require '../comp/nav.php';
?>

<fireelf data-id="1"><h1>Hewwo World!</h1><h2>Client index</h2><p>Here is <u>some</u> <strong>random text</strong> in as a <em>paragraph</em></p><p><a href="#" target="_blank">I do not link to anywhere</a></p><p>Added from the <strong>editor</strong>!</p></fireelf>


<fireelf data-id="2"><h3>I am another block!</h3><p>Hi there:</p><p>Some <strong>extra</strong> <em>content</em> for the 2nd bloque</p></fireelf>

<?php
for ($i = 0; $i < 5; $i++) {
    ?>
    <h2><?php echo $i ?></h2>
    <?php
}
?>

<script>console.log('henlo');</script>

<?php
require '../comp/footer.php';
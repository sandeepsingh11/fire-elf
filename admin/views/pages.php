<?php

// = get all pages from specifed directory = //
$pages_arr = scandir('../' . CLIENT_PAGES_DIR);

// remove '.' and '..', then re-index
unset($pages_arr[0]);
unset($pages_arr[1]);
$pages_arr = array_values($pages_arr);

?>





<h1>Pages</h1>
<h3>Fire Elf</h3>

<div id="pages_container">
    <ol>
        <?php

        foreach ($pages_arr as $page) {
            ?>
            <li class="page"><a href="/pages/edit?id=<?php echo htmlentities($page) ?>"><?php echo htmlentities($page) ?></a></li>
            <?php
        }

        ?>
    </ol>
</div>
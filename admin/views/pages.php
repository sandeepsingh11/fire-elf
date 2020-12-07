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



<a href="/pages/add">Add a new page</a>

<div id="pages_container">
    <ol>
        <?php

        foreach ($pagesInfo_arr as $page) {
            ?>
            <li class="page">
                <a href="/pages/edit?id=<?php echo htmlentities($page['name']) ?>"><?php echo htmlentities($page['name']) ?></a>
                Last updated at: <?php echo htmlentities($page['updated_at']) ?>
            </li>
            <?php
        }

        ?>
    </ol>
</div>
<?php require __DIR__ . '/partials/header.php' ?>

<h1>Pages</h1>
<h3>Fire Elf</h3>



<a href="/pages/editor">Add a new page</a>

<div id="pages_container">
    <ol>
        <?php
        
        foreach ($this->page_arr as $page) {
            ?>
            <li class="page">
                <a href="/pages/editor?id=<?= $page['id'] ?>"><?php echo htmlentities($page['name']) ?></a>
                Last updated at: <?php echo htmlentities($page['updated_at']) ?>
                <form action="/pages" method="post">
                    <input type="hidden" name="delete">
                    <input type="hidden" name="delete-id" value="<?= $page['id'] ?>">

                    <input type="submit" value="Delete">
                </form>
            </li>
            <?php
        }

        ?>
    </ol>
</div>

<?php require __DIR__ . '/partials/footer.php' ?>
<?php require __DIR__ . '/partials/header.php' ?>

<h1>Pages</h1>



<a href="/pages/editor">New page</a>


<div id="page-list-container">
    <table id="page-list-table">
        <tr>
            <th>Page Title</th>
            <th>Created at</th>
            <th>Updated at</th>
            <th>Delete</th>
        </tr>

        <?php foreach ($this->page_arr as $page) : ?>
        <tr>
            <td class="page-list">
                <a href="/pages/editor?id=<?= $page['id'] ?>"><?= $this->escape($page['name']) ?></a>
            </td>
            <td class="page-list">
                <span><?= $page['created_at'] ?></span>
            </td>
            <td class="page-list">
                <span><?= $page['updated_at'] ?></span>
            </td>
            <td class="page-list">
                <form action="/pages" method="post">
                    <input type="hidden" name="delete">
                    <input type="hidden" name="delete-id" value="<?= $page['id'] ?>">

                    <input type="submit" value="X">
                </form>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
</div>

<?php require __DIR__ . '/partials/footer.php' ?>
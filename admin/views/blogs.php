<h1>Blogs</h1>

<a href="blog/editor">Create</a>

<?php
foreach ($blogs_arr as $blog) {
    ?>
    <div>
        <a href="blog/editor?id=<?php echo $blog['id'] ?>">Edit <?php echo $blog['title'] ?></a>
        Last updated at: <?php echo htmlentities($blog['updated_at']) ?>

        <form action="/blogs" method="post">
            <input type="hidden" name="delete">
            <input type="hidden" name="delete-id" value="<?= $blog['id'] ?>">

            <input type="submit" value="Delete">
        </form>
    </div>
    <?php
}
?>
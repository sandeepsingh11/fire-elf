<h1>Blogs</h1>

<a href="blog/editor">Create</a>

<?php
foreach ($blogs_arr as $blog) {
    ?>
    <div>
        <a href="blog/editor?id=<?php echo $blog['id'] ?>">Edit <?php echo $blog['title'] ?></a>
    </div>
    <?php
}
?>
<!-- blog/delete?id=x -->
<?php require __DIR__ . '/partials/header.php' ?>

<h1>Blogs</h1>

<a href="blog/editor">New blog</a>

<div id="blog-list-container">
    <table id="blog-list-table">
        <tr>
            <th>Blog Title</th>
            <th>Author</th>
            <th>Tags</th>
            <th>Created at</th>
            <th>Updated at</th>
            <th>Delete</th>
        </tr>
        
        <?php foreach ($this->blogs_arr as $blog) : ?>
        <tr>
            <td class="blog-list">
                <a href="blog/editor?id=<?= $blog['id'] ?>"><?= $this->escape($blog['title']) ?></a>
            </td>
            <td class="blog-list">
                <span><?= $blog['author'] ?></span>
            </td>
            <td class="blog-list">
                <span><?= (implode(' | ', $blog['tags'])) ?></span>
            </td>
            <td class="blog-list">
                <span><?= $blog['created_at'] ?></span>
            </td>
            <td class="blog-list">
                <span><?= $blog['updated_at'] ?></span>
            </td>
                
            <td class="grid-item">
                <!-- delete -->
                <form class="form-delete" action="/blogs" method="post">
                    <input type="hidden" name="delete">
                    <input type="hidden" name="delete-id" value="<?= $blog['id'] ?>">
                    <input type="hidden" name="entry-name" value="<?= $this->escape($blog['title']) ?>">
    
                    <input type="submit" value="X">
                </form>
            </td>
        </tr>
        <?php endforeach ?>
    </table>
</div>

<?php require __DIR__ . '/partials/footer.php' ?>
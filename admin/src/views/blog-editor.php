<?php require __DIR__ . '/partials/header.php' ?>

<h1>Editor</h1>
<h3>Fire Elf</h3>

<form id="form" action="/blog/editor" method="POST" enctype="multipart/form-data">
    <!-- title -->
    <label for="title">Blog title: </label>
    <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($this->blogTitle) ?>">
    
    <!-- slug -->
    <label for="slug">Blog slug: </label>
    <input type="text" name="slug" id="slug" value="<?php echo htmlspecialchars($this->blogSlug) ?>">

    <!-- author -->
    <label for="author">Blog author: </label>
    <input type="text" name="author" id="author" value="<?php echo htmlspecialchars($this->blogAuthor) ?>">

    <!-- tags -->
    <label for="tags">Blog tags (separated by commas): </label>
    <input type="text" name="tags" id="tags" value="<?php echo htmlspecialchars($this->blogTags) ?>">

    <!-- cover image -->
    <?php if ($this->blogId == -1): ?>
        <label for="cover">Blog cover image: </label>
    <?php else: ?>
        <label for="cover">Blog cover image (current image is '<?= $this->blogCover ?>'): </label>
    <?php endif ?>

    <input type="file" name="cover" id="cover">



    <!-- editor -->
    <div class="editor" id="editor">
        <?= $this->blogContent ?>
    </div>

    <input type="hidden" name="id" id="id" value="<?= $this->blogId; ?>">
    <input type="hidden" name="media-list" id="media-list" value="<?= $this->mediaList; ?>">


    <input type="submit" value="Update">
</form>


<!-- media lib modal -->
<div id="media-lib-modal" class="hide">
    <div class="modal-header-container">
        <h3 id="modal-header">Media Library</h3>
        <span id="modal-close">X</span>
    </div>

    <form id="media-lib-modal-form">
        <input type="file" name="media-lib-upload" id="media-lib-upload">
    </form>

    <div id="media-lib"></div>
</div>

<a href="/blogs">Back to Blogs</a>




<?php require __DIR__ . '/partials/footer.php' ?>
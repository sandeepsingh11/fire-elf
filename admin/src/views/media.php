<?php require __DIR__ . '/partials/header.php' ?>

<h1>Media</h1>


<!-- upload media -->
<form action="media-lib" method="post" enctype="multipart/form-data">
    <label for="media-upload">Upload media</label>
    <input type="file" name="media-upload[]" id="media-upload" multiple>

    <input type="submit" value="Upload">
</form>


<!-- display all media -->
<div style="display: flex; flex-wrap: wrap;">
    <?php foreach ($this->media_arr as $media): ?>
        <?php $imageName = explode(".", $media['name'])[0]; ?>
        <div style="width: 500px;">
            <img src="<?= MEDIA_URL . $media['name'] ?>" alt="<?= $imageName ?>" loading="lazy">
            <a href="<?= MEDIA_URL . $media['name'] ?>" target="_blank" referrerpolicy="no-referrer"><?= $imageName ?></a>

            <form action="/media-lib/delete" method="post">
                <input type="hidden" name="delete">
                <input type="hidden" name="delete-id" value="<?= $media['name'] ?>">

                <input type="submit" value="Delete">
            </form>
        </div>
    <?php endforeach ?>
</div>

<?php require __DIR__ . '/partials/footer.php' ?>
<style>
    img {
        display: block;
        width: 100%;
        object-fit: cover;
        object-position: center;
    }
</style>

<h1>Media</h1>


<!-- upload media -->
<form action="media-lib" method="post" enctype="multipart/form-data">
    <label for="media-upload">Upload media</label>
    <input type="file" name="media-upload" id="media-upload">

    <input type="submit" value="Upload">
</form>


<!-- display all media -->
<div style="display: flex; flex-wrap: wrap;">
    <?php
    foreach ($mediaList["media"] as $media) {
        $imageName = explode(".", $media['name'])[0];
        ?>
        <div style="width: 500px;">
            <img src="<?php echo MEDIA_URL . $media['name'] ?>" alt="<?php echo $imageName ?>" loading="lazy">
            <a href="<?php echo MEDIA_URL . $media['name'] ?>" target="_blank" referrerpolicy="no-referrer"><?php echo $imageName ?></a>

            <form action="/media-lib/delete" method="post">
                <input type="hidden" name="delete">
                <input type="hidden" name="delete-id" value="<?= $media['name'] ?>">

                <input type="submit" value="Delete">
            </form>
        </div>
        <?php
    }
    ?>
</div>
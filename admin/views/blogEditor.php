<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <title>Blog Editor | Fire Elf</title>
        <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>

        <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    </head>

    <body>
        <h1>Editor</h1>
        <h3>Fire Elf</h3>

        <form id="form-editor" action="/blog/editor" method="POST" enctype="multipart/form-data">
            <label for="title">Blog title: </label>
            <input type="text" name="title" id="title" value="<?php echo htmlspecialchars($blogTitle) ?>">
            
            <label for="slug">Blog slug: </label>
            <input type="text" name="slug" id="slug" value="<?php echo htmlspecialchars($blogSlug) ?>">

            <label for="author">Blog author: </label>
            <input type="text" name="author" id="author" value="<?php echo htmlspecialchars($blogAuthor) ?>">

            <label for="tags">Blog tags (separated by commas): </label>
            <input type="text" name="tags" id="tags" value="<?php echo htmlspecialchars($blogTags) ?>">
 
            <?php
            if ($blogId == -1) {
                ?>
                <label for="cover">Blog cover image: </label>
                <?php
            }
            else {
                ?>
                <label for="cover">Blog cover image (current image is '<?php echo $blogCover ?>'): </label>
                <?php
            }
            ?>
            <input type="file" name="cover" id="cover">



            <!-- editor -->
            <div class="editor" id="editor">
                <?php echo $blogContent ?>
            </div>
        
            <input type="hidden" name="id" id="id" value="<?php echo $blogId; ?>">
        
        
            <input type="submit" value="Update">
        </form>

        <a href="/blogs">Back to Blogs</a>





        <!-- Include the Quill library -->
        <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

        <!-- Include quill-image-uploader module - https://github.com/NoelOConnell/quill-image-uploader -->
        <script src="https://unpkg.com/quill-image-uploader@1.2.2/dist/quill.imageUploader.min.js"></script>


        <script>
            var fileObj_arr = [];

            // create quill editor
            var editor = '#editor';

            // Initialize Quill editor
            var toolbarOptions = [
                [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                ['bold', 'italic', 'underline', 'strike', 'link'],
                [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                [{ 'align': [] }],
                ['image'],
                ['clean']
            ];

            // Initialize quill-image-uploader module
            Quill.register("modules/imageUploader", ImageUploader);


            
            var quillEle = new Quill(editor, {
                modules: {
                    toolbar: toolbarOptions,
                    imageUploader: {
                        upload: file => {
                            return new Promise((resolve, reject) => {
                                // push new img name
                                fileObj_arr.push(file.name);
                            });
                        }
                    }
                },
                theme: 'snow'
            });



            // on form submit, get quill delta (ops) content,
            // then resume submit
            $('#form-editor').submit(function(e)  {
                e.preventDefault();


                // get quill delta
                var delta = quillEle.getContents();
                var delta_json = JSON.stringify(delta);
                delta_json = delta_json.replace(/'/g, '&#39;'); // convert "'"

                var inputOps = '<input type="hidden" name="ops" id="ops" value=\'' + delta_json + '\'>';
          
                // append to form for submission
                $('#id').after(inputOps);



                // resume form submit
                e.target.submit();
            });
        </script>
    </body>
</html>
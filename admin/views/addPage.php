<!-- https://quilljs.com/docs/quickstart/ -->

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Add Page | Fire Elf</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  </head>
  <body>
    <h1>Add Page</h1>
    <h3>Fire Elf</h3>

    <form id="form" action="/pages/add" method="POST">
        <label for="title">Page title:</label>
        <input type="text" name="title" id="title">

        <label for="dir">Parent URL</label>
        <select name="dir" id="dir"> 
          <option value="/">/</option>
          
          <?php
          foreach($pageUrl_arr as $page) {
            $pageUrlDir = $page['dir'] . htmlspecialchars($page['name']);
            $pageFileDir = $page['dir'] . htmlspecialchars($page['file']);
            ?>
            <option value="<?php echo $pageFileDir?>">
              <?php echo $pageUrlDir ?>
            </option>
            <?php
          }
          ?>

        </select>


        <div name="content-1" id="editor-1"></div>
        
        
        <input type="submit" value="Add page">
    </form>




    <!-- Include the Quill library -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    <!-- Include quill-image-uploader module - https://github.com/NoelOConnell/quill-image-uploader -->
    <script src="https://unpkg.com/quill-image-uploader@1.2.2/dist/quill.imageUploader.min.js"></script>



    <script>
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
      var fileObj_arr = [];


      var quillEle = new Quill('#editor-1', {
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





      // on form submit, get quill delta (ops),
      // then resume submit
      $('#form').submit(function(e)  {
        e.preventDefault();


        // get quill delta
        var delta = quillEle.getContents();
        var delta_json = JSON.stringify(delta);
        delta_json = delta_json.replace(/'/g, '&#39;'); // convert "'"

        var inputOps = '<input type="hidden" name="ops-1" id="ops-1" value=\'' + delta_json + '\'>';
        
        // append to form for submission
        $('#editor-1').after(inputOps);



        // build image names string
        var imgNames_str = "";
        var len = fileObj_arr.length;


        // there could be more image names pushed into fileObj_arr
        // than there are new base64 images since the user can delete
        // images after uploading, and there currently isn't an easy
        // way to implement an image deletion listener to pop the
        // deleted image name out of the array.

        // instead, use the latest pushed names (hence reversed for loop)
        for (var i = len - 1; i >= 0; i--) {
          imgNames_str += fileObj_arr[i] + ",";
        }

        // trim off last " "
        imgNames_str = imgNames_str.slice(0, -1);

        // append to form for submission
        var inputImgNames = '<input type="hidden" name="image-names" value="' + imgNames_str + '">';
        $('#editor-1').after(inputImgNames);


        // resume form submit
        e.target.submit();
      });
    </script>
  </body>
</html>
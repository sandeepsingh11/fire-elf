<!-- https://quilljs.com/docs/quickstart/ -->

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Edit Page | Fire Elf</title>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js" integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0=" crossorigin="anonymous"></script>
    
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
  </head>
  <body>
    <h1>Editor</h1>
    <h3>Fire Elf</h3>

    <form id="form-editor" action="/pages/edit" method="POST">      
      <?php
      for($i = 0; $i < sizeof($quillBlock_arr); $i++) {
        ?>
        <div 
          id="editor-<?php echo ($i + 1) ?>" 
          name="new-content-<?php echo ($i + 1) ?>"
          >
          <?php echo $quillBlock_arr[$i] ?>
        </div>
        <?php
      }
      ?>
      
      <input type="hidden" name="page" id="page" value="<?php echo htmlentities($pageName); ?>">
      
      
      
      <input type="submit" value="Update">
    </form>


    <input type="hidden" name="block-num" id="block-num" value="<?php echo $i ?>">
    <input type="hidden" name="content" id="content" value="<?php echo htmlentities($pageContent); ?>">
    
    <a href="/pages">Back to Pages</a>




    <!-- Include the Quill library -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    

    <script>
      var quillEle_arr = [];
      var blockNum = $('#block-num').val();

      // create quill editor for each block content
      for (var i = 0; i < blockNum; i++) {
        var currentSelector = '#editor-' + (i + 1);

        // Initialize Quill editors
        var toolbarOptions = [
          [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
          ['bold', 'italic', 'underline', 'strike', 'link'],
          [{ 'list': 'ordered'}, { 'list': 'bullet' }],
          [{ 'align': [] }],
          ['image'],
          ['clean']
        ];

        var quillEle = new Quill(currentSelector, {
          modules: {
            toolbar: toolbarOptions
          },
          theme: 'snow'
        }); 

        quillEle_arr.push(quillEle);
      }



      // on form submit, get quill delta (ops),
      // then resume submit
      $('#form-editor').submit(function(e)  {
        e.preventDefault();


        // get quill delta for each editor
        for (var i = 0; i < blockNum; i++) {
          var delta = quillEle_arr[i].getContents();
          var delta_json = JSON.stringify(delta);
          delta_json = delta_json.replace(/'/g, '&#39;'); // convert "'"

          var input = '<input type="hidden" name="ops-' + (i + 1) + '" id="ops-' + (i + 1) + '" value=\'' + delta_json + '\'>';
          
          $('#page').after(input);
        }


        // resume form submit
        e.target.submit();
      });
    </script>
  </body>
</html>
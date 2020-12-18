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
    





    <!-- Include the Quill library -->
    <script src="https://cdn.quilljs.com/1.3.6/quill.min.js"></script>

    

    <script>
      var quillEle_arr = [];
      var blockNum = $('#block-num').val();

      // create quill editor for each block content
      for (var i = 0; i < blockNum; i++) {
        var currentSelector = '#editor-' + (i + 1);

        // Initialize Quill editors
        var quillEle = new Quill(currentSelector, {
          theme: 'snow'
        }); 

        quillEle_arr.push(quillEle);
      }


      // escape scripts
      // var content = $('#content').val();
      // content = content.replace(/<\?(php)/g, "<h1>");
      // content = content.replace(/\?>/g, "</h1>");
      // content = content.replace(/<(script)>/g, "<!--js");
      // content = content.replace(/<\/script>/g, "-->");
      


      // on form submit, get quill delta (ops),
      // then resume submit
      $('#form-editor').submit(function(e)  {
        e.preventDefault();


        // get quill delta for each editor
        for (var i = 0; i < blockNum; i++) {
          var delta = quillEle_arr[i].getContents();
          var input = '<input type="hidden" name="ops-' + (i + 1) + '" id="ops-' + (i + 1) + '" value=\'' + JSON.stringify(delta) + '\'>';
          
          $('#page').after(input);
        }


        // resume form submit
        e.target.submit();
        




        // $.ajax({
        //   url: '/pages/edit',
        //   type: 'POST',
        //   data: {
        //     page: page,
        //     ops: delta_arr[0].ops
        //   }
        // });
        
      });
    </script>
  </body>
</html>
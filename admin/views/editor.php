<!-- https://summernote.org/getting-started/ -->

<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Edit Page | Fire Elf</title>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
  </head>
  <body>
    <h1>Editor</h1>
    <h3>Fire Elf</h3>

    <div id="summernote"></div>
    <input type="hidden" id="content" value="<?php echo htmlentities($pageContent); ?>">





    <script>
      $('#summernote').summernote({
        tabsize: 2,
        height: 250,
        toolbar: [
          ['style', ['style']],
          ['font', ['bold', 'underline', 'clear']],
          ['color', ['color']],
          ['para', ['ul', 'ol', 'paragraph']],
          ['table', ['table']],
          ['insert', ['link', 'picture', 'video']],
          ['view', ['fullscreen', 'codeview', 'help']]
        ]
      });

      var content = $('#content').val();
      $('#summernote').summernote('pasteHTML', content);
    </script>
  </body>
</html>
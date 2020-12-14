<!-- https://summernote.org/getting-started/ -->

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <title>Edit Page | Fire Elf</title>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <!-- <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script> -->
    <link href="/summernote/summernote-lite.min.css" rel="stylesheet">
    <script src="/summernote/summernote-lite.min.js"></script>
  </head>
  <body>
    <h1>Editor</h1>
    <h3>Fire Elf</h3>

    <form action="/pages/edit" method="POST">
        <textarea name="new-content" id="summernote"></textarea>
        <input type="hidden" name="page" id="page" value="<?php echo htmlentities($pageName); ?>">
        
        
        <input type="submit" value="Update">
    </form>


    <input type="hidden" name="content" id="content" value="<?php echo htmlentities($pageContent); ?>">





    <script>
      $('#summernote').summernote({
        // placeholder: ' ',
        tabsize: 2,
        height: 250,
        // airMode: true,
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
      console.log(content);
      content = content.replace(/<\?(php)/g, "<!--");
      content = content.replace(/\?>/g, "-->");
      content = content.replace(/<(script)>/g, "<p style='background-color:salmon;color:salmon' data-lang='js'>");
      content = content.replace(/<\/script>/g, "</p>");
      // content = content.replace(/<\?(php)/g, "<p style='background-color:salmon;color:salmon' data-lang='php'>");
      // content = content.replace(/\?>/g, "</p>");
      // content = content.replace(/<(script)>/g, "<p style='background-color:salmon;color:salmon' data-lang='js'>");
      // content = content.replace(/<\/script>/g, "</p>");
      console.log(content);
      $('#summernote').summernote('pasteHTML', content);




      $('#summernote').blur(function(e) {
        var phpkey1 = "<"+"?php",
            phpkeyend = "?>",
            stylekey1 = "&lt;style&gt;",
            stylekey2 = "&lt;style type=\"text/css\"&gt;",
            stylekeyend = "&lt;/style&gt;",
            scriptkey1 = "&lt;script&gt;",
            scriptkey2 = "&lt;script type=\"text/javascript\"&gt;",
            scriptkeyend = "&lt;/script&gt;";

        var code = $(this).code();

        code = $.trim(code)
          .replace(/<!--\?php/g, phpkey1)
          .replace(/\?-->/g, phpkeyend)
          .replace(/<style>/g, stylekey1)
          .replace(/<style type="text\/css">/g, stylekey2)
          .replace(/<\/style>/g, stylekeyend)
          .replace(/<script>/g, scriptkey1)
          .replace(/<script type="text\/javascript">/g, scriptkey2)
          .replace(/<\/script>/g, scriptkeyend);

        var content = $("textarea[name='new-content']").html(code);
        console.log('blurred!');
      });
    </script>
  </body>
</html>
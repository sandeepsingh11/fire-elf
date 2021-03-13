<?php require __DIR__ . '/partials/header.php' ?>
    <h1>Page Editor</h1>
    <h3>Fire Elf</h3>

    <form id="form" action="/pages/editor" method="POST">      
      <label for="title">Page title:</label>
      <input type="text" name="title" id="title" value="<?= $this->pageName ?>">

      <label for="dir">Parent directory</label>
      <select name="dir" id="dir">
        
        <?php
        foreach($this->pages_arr as $page) {
          $pageParentDir = $page['dir'] . htmlspecialchars($page['file']);
          ?>
          <option value="<?php echo $pageParentDir ?>">
            <?php echo $pageParentDir ?>
          </option>
          <?php
        }
        ?>
      </select>

      <div name="content" id="editor">
        <?php echo $this->quillBlock ?>
      </div>
      


      <input type="hidden" name="id" id="id" value="<?= $this->pageId ?>">
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

    
    <a href="/pages">Back to Pages</a>




    
    <?php require __DIR__ . '/partials/footer.php' ?>
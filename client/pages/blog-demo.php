<?php
            $pageTitle = "Blog Demo - Fire Elf";
            require __dir__ . '/../comp/head.php';
            require __dir__ . '/../comp/nav.php';
            require __DIR__ . '/../../vendor/autoload.php';
            ?><fireelf data-id="1"><h1>Blogu</h1></fireelf>
            <?php
            $Blog = new Blog();
            Controller::prettyPrint($Blog->getBlog(1));

            require __dir__ . '/../comp/footer.php';
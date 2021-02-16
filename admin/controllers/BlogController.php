<?php

class BlogController extends Controller {

    private $blog;
    private $Session;

    public function __construct()
    {
        $this->blog = new Blog();  
        $this->Session = new Session();  
    }



    /**
     * get page to display all blog posts
     */
    public function getAll() {
        $blogs_arr = $this->blog->getAllBlogs();

        $messages_arr = $this->Session->getAllMessages();
        Controller::prettyPrint($messages_arr);
        
        include_once __DIR__ . '/../views/blogs.php';
    }


    /**
     * get blog editor page.
     * Could be to create a new blog post or
     * update an existing blog post (?id=1)
     */
    public function get() {
        // blog values
        $blogId = -1; // -1 if creating a new blog (no existing blog id)
        $blogTitle = '';
        $blogSlug = '';
        $blogAuthor = '';
        $blogTags = '';
        $blogCover = '';
        $blogContent = '';

        if ($this->Session->errorExists()) {
            // if session values are present (from an error), get values
            $formVals = $_SESSION['error_values'];

            $blogId = $formVals['id'];
            $blogTitle = $formVals['title'];
            $blogSlug = $formVals['slug'];
            $blogAuthor = $formVals['author'];
            $blogTags = $formVals['tags'];
            $blogContent = $formVals['ops'];
        }
        else if (isset($_GET['id'])) {
            // if editing an existing blog post, get values
            $blogId = $_GET['id'];

            if ($blog_arr = $this->blog->getBlogInfo($blogId)) {
                $blogTitle = $blog_arr['title'];
                $blogSlug = $blog_arr['slug'];
                $blogAuthor = $blog_arr['author'];
                $blogTags = $blog_arr['tags'];
                $blogCover = $blog_arr['cover'];
                $blogContent = $blog_arr['content'];
            }
            else {
                $this->Session->setError("Blog Id:$blogId not found");

                header('Location: /blogs');
            }
        }


        $messages_arr = $this->Session->getAllMessages();
        Controller::prettyPrint($messages_arr);
        
        include_once __DIR__ . '/../views/blogEditor.php';
    }



    /**
     * create or update blog data
     */
    public function post() {
        // check for submitted data
        if ( ($_POST['title'] == '') || ($_POST['slug'] == '') || (!isset($_POST['ops'])) ) {
            // if req input is not filled

            $lexer = new nadar\quill\Lexer($_POST['ops']);
            $ops = $lexer->render();

            $session_arr = array(
                'id' => intval($_POST['id']),
                'title' => $_POST['title'],
                'slug' => $_POST['slug'],
                'author' => $_POST['author'],
                'tags' => $_POST['tags'],
                'ops' => $ops
            );
            
            $errorMessage = "Please fill in all required fields";
            $this->Session->setError($errorMessage, $session_arr);
            
            header('Location: /blog/editor');
        }
        else {
            // get blog values
            $title = $_POST['title'];
            $slug = $_POST['slug'];
            $author = $_POST['author'];
            $tags_arr = explode(",", $_POST['tags']);
            $imageObj = $_FILES['cover'];
            $id = intval($_POST['id']);
            $ops = $_POST['ops'];
    
            $this->blog->setBlogData($id, $title, $slug, $author, $tags_arr, $imageObj, $ops);
        }
    }
}
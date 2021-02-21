<?php

class BlogController extends Controller {

    private $blog;
    private $Session;

    public $blogs_arr;
    public $blogId;
    public $blogTitle;
    public $blogSlug;
    public $blogAuthor;
    public $blogTags;
    public $blogCover;
    public $blogContent;



    public function __construct()
    {
        $this->blog = new Blog();  
        $this->Session = new Session();  
    }



    /**
     * get page to display all blog posts
     */
    public function getAll() {
        // get all blog entries
        $this->blogs_arr = $this->blog->getAllBlogs();

        $messages_arr = $this->Session->getAllMessages();
        Controller::prettyPrint($messages_arr);
        
        $this->page_title = 'Blogs';
        $this->view('blogs');
    }


    /**
     * get blog editor page.
     * Could be to create a new blog post or
     * update an existing blog post (?id=1)
     */
    public function get() {
        // blog values
        $this->blogId = -1; // -1 if creating a new blog (no existing blog id)
        $this->blogTitle = '';
        $this->blogSlug = '';
        $this->blogAuthor = '';
        $this->blogTags = '';
        $this->blogCover = '';
        $this->blogContent = '';

        if ($this->Session->errorExists()) {
            // if session values are present (from an error), get values
            $formVals = $_SESSION['error_values'];

            $this->blogId = $formVals['id'];
            $this->blogTitle = $formVals['title'];
            $this->blogSlug = $formVals['slug'];
            $this->blogAuthor = $formVals['author'];
            $this->blogTags = $formVals['tags'];
            $this->blogContent = $formVals['ops'];
        }
        else if (isset($_GET['id'])) {
            // if editing an existing blog post, get values
            $this->blogId = $_GET['id'];

            if ($blog_arr = $this->blog->getBlogInfo($this->blogId)) {
                $this->blogTitle = $blog_arr['title'];
                $this->blogSlug = $blog_arr['slug'];
                $this->blogAuthor = $blog_arr['author'];
                $this->blogTags = $blog_arr['tags'];
                $this->blogCover = $blog_arr['cover'];
                $this->blogContent = $blog_arr['content'];
            }
            else {
                $this->Session->setError("Blog Id:$this->blogId not found");

                header('Location: /blogs');
            }
        }


        $messages_arr = $this->Session->getAllMessages();
        Controller::prettyPrint($messages_arr);




        // inject css
        $css_arr = array(
            'https://cdn.quilljs.com/1.3.6/quill.snow.css'
        );
        $this->css = $css_arr;
        
        
        
        // inject js
        $js_arr = array(
            'https://code.jquery.com/jquery-3.5.1.min.js',
            'https://cdn.quilljs.com/1.3.6/quill.min.js',
            'https://unpkg.com/quill-image-uploader@1.2.2/dist/quill.imageUploader.min.js',
            $this->getScript('quilljs-handler')
        );
        $this->js = $js_arr;


        
        $this->page_title = 'Blog Editor';
        $this->view('blog-editor');
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
            $imageCover = $_FILES['cover'];
            $imageNames = $_POST['image-names'];
            $id = intval($_POST['id']);
            $ops = $_POST['ops'];
    
            $this->blog->setBlogData($id, $title, $slug, $author, $tags_arr, $imageCover, $imageNames, $ops);
        }
    }



    /**
     * handle blog delete request
     */
    public function delete() {
        if (isset($_POST['delete'])) {
            $this->blogId = $_POST['delete-id'];

            $this->blog->deleteBlog($this->blogId);
        }
        

        header('Location: /blogs');
    }
}
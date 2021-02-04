<?php

class BlogController extends Controller {

    private $blog;

    public function __construct()
    {
        $this->blog = new Blog();    
    }



    /**
     * get page to display all blog posts
     */
    public function getAll() {
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

        // if editing an existing blog post, get values
        if (isset($_GET['id'])) {
            $blogId = $_GET['id'];

            $blog_arr = $this->blog->getBlogInfo($blogId);

            $blogTitle = $blog_arr['title'];
            $blogSlug = $blog_arr['slug'];
            $blogAuthor = $blog_arr['author'];
            $blogTags = $blog_arr['tags'];
            $blogCover = $blog_arr['cover'];
            $blogContent = $blog_arr['content'];
        }


        include_once __DIR__ . '/../views/blogEditor.php';
    }



    /**
     * create or update blog data
     */
    public function post() {
        // check for submitted data
        if ( (!isset($_POST['title'])) || (!isset($_POST['slug'])) || (!isset($_POST['ops'])) ) {
            // save vals to session (or GET)
            header('Location: /blog/edit?id=');
        }

        // get blog values
        $title = $_POST['title'];
        $slug = $_POST['slug'];
        $author = $_POST['author'];
        $tags_arr = explode(",", $_POST['tags']);
        $imageObj = $_FILES['cover'];
        $id = $_POST['id'];
        $ops = $_POST['ops'];

        $this->blog->setBlogData($id, $title, $slug, $author, $tags_arr, $imageObj, $ops);
    }
}
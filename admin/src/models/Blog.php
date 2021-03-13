<?php

class Blog {
    
    private $blogList;
    private $blogListPath = __DIR__ . '/../blog_list.json';
    private $Session;

    public function __construct()
    {
        $temp = file_get_contents($this->blogListPath);
        $this->blogList = json_decode($temp, true);

        $this->Session = new Session();
    }



    public function getblogList() {
        return $this->blogList;
    }



    /**
     * get all blogs from blog_list.json
     * @return array
     */
    public function getAllBlogs() {
        return $this->blogList['blog'];
    }



    /**
     * Get blog information
     * 
     * @param int $id the blog id
     * 
     * @return array|bool return blog assoc array on success, false on failure
     */
    public function getBlog($id) {
        foreach ($this->blogList['blog'] as $blog) {
            if ($blog['id'] == $id) {
                // convert tags arr to string
                $tags = implode(',', $blog['tags']);

                $blog_arr = array(
                    'id' => $id,
                    'title' => $blog['title'],
                    'slug' => $blog['slug'],
                    'author' => $blog['author'],
                    'tags' => $tags,
                    'cover' => $blog['cover'],
                    'content' => $blog['content'],
                    'created_at' => $blog['created_at'],
                    'updated_at' => $blog['updated_at']
                );

                return $blog_arr;
            }
        }

        return false;
    }



    /**
     * set / update blog data
     * @param integer $id
     * @param string $title
     * @param string $slug
     * @param string $author
     * @param array $tags
     * @param array $imageCover
     * @param array $imageNames
     * @param string $ops
     */
    public function setBlogData($id, $title, $slug, $author, $tags, $imageCover, $imageNames, $ops) {
        // create or update blog?
        if ($id == -1) {
            $create = true;
        }
        else {
            $create = false;
        }




        // prep quilljs ops
        $ops = json_decode($ops, true);
        
        // convert quilljs base64 imgs into html img tags and store locally
        $Media = new Media();
        $ops = $Media->base64ToImage($ops, $imageNames);


        // convert quill delta to html
        $lexer = new nadar\quill\Lexer($ops);
        $ops = $lexer->render();


        // populate empty image alt attributes
        $ops = $Media->populateImgAlt($ops);




        
        // for errors, create form value array to pass to session
        $formSession_arr = array(
            'id' => $id,
            'title' => $title,
            'slug' => $slug,
            'author' => $author,
            'tags' => implode(',', $tags),
            'ops' => $ops
        );


        
        // cover image handler
        if ($create) {
            if ($imageCover['tmp_name'] != '') {
                // if an image was submitted, continue
                if ($imageCover["error"] != 0) {
                    // if error exists

                    $uploadErrMessage = $this->mediaList->phpFileUploadErrors[$imageCover["error"]];
                    $this->Session->setError($uploadErrMessage, $formSession_arr);
                    
                    header('Location: /blog/editor');
                    exit();
                }
                else {
                    // move media file to media folder
                    $success = $Media->storeImage($imageCover);
                    if (!$success) {

                        $errorMessage = "Image was not stored successfully. Please try again";
                        $this->Session->setError($errorMessage, $formSession_arr);
                        
                        header('Location: /blog/editor');
                        exit();
                    }
                }
            }
        }
        else {
            // update - if existing cover image is different than the submitted image, update
            
            // get existing blog entry
            if (!$blogObj = $this->getBlog($id)) {
                
                $errorMessage = "Blog Id:$id not found";
                $this->Session->setError($errorMessage, $formSession_arr);
                
                header('Location: /blog/editor');
                exit();
            }
            else if ($imageCover['tmp_name'] == '') {
                // if no new image was submitted, use old image
                $imageCover['name'] = $blogObj['cover'];
            }
            else if ($blogObj['cover'] != $imageCover['name']) {
                // image names are diff

                // if an image was submitted, continue
                if ($imageCover["error"] != 0) {
                    // if error exists

                    $uploadErrMessage = $this->mediaList->phpFileUploadErrors[$imageCover["error"]];
                    $this->Session->setError($uploadErrMessage, $formSession_arr);
                    
                    header('Location: /blog/editor');
                    exit();
                }
                else {
                    // move media file to media folder
                    $success = $Media->storeImage($imageCover);
                    if (!$success) {
                        
                        $errorMessage = "Image was not stored successfully. Please try again";
                        $this->Session->setError($errorMessage, $formSession_arr);
                        
                        header('Location: /blog/editor');
                        exit();
                    }
                }
            }
        }



        // get next available blog id
        if ($create) {
            $id = $this->nextBlogId();
        }
            
        // $ops is converted already, get html content
        $htmlContent = $ops;
        
        // get current date time
        $date = date('m-d-Y h:ia');
        

        // populate 'json'
        if ($create) {
            $newBlogInfo = array(
                'id' => $id,
                'title' => $title,
                'slug' => $slug,
                'author' => $author,
                'tags' => $tags,
                'cover' => $imageCover['name'],
                'content' => $htmlContent,
                'created_at' => $date,
                'updated_at' => ''
            );

            array_push($this->blogList['blog'], $newBlogInfo);
        }
        else {
            $createdDate = $blogObj['created_at'];

            $newBlogInfo = array(
                'id' => $id,
                'title' => $title,
                'slug' => $slug, ///////////rename file in client side
                'author' => $author,
                'tags' => $tags,
                'cover' => $imageCover['name'],
                'content' => $htmlContent,
                'created_at' => $createdDate,
                'updated_at' => $date
            );


            // loop through properties of old and new blog entry and
            // only change / update what is different
            for ($i = 0; $i < sizeof($this->blogList['blog']); $i++) {
                if ($this->blogList['blog'][$i]['id'] == $id) {
                    $this->blogList['blog'][$i] = $newBlogInfo;


                    // correct blog entry index, loop through props
                    
                    // for ($j = 0; $j < sizeof($this->blogList['blog'][$i]); $j++) {
                    //     $props = array_keys($this->blogList['blog'][$i]);
                    //     echo $this->blogList['blog'][$i][$props[$j]] . '&&' . $newBlogInfo[$props[$j]] . ' ,';
                    //     if ($this->blogList['blog'][$i][$props[$j]] != $newBlogInfo[$props[$j]]) {
                    //         $this->blogList['blog'][$i][$props[$j]] = $newBlogInfo[$props[$j]];
                    //         echo 'naisu! ,';
                    //     }
                    // }

                    break;
                }
            }
        }

        // write new json
        $this->setBlogList($this->blogList);

        // set success message
        if ($create) {
            $successMessage = 'New blog created!';
        }
        else {
            $successMessage = 'Blog updated!';
        }
        $this->Session->setSuccess($successMessage);


        
        // success! Redirect back to media page
        header('Location: /blogs');
    }



    /**
     * check if blog exists
     * @param int $blogId
     * @return bool
     */
    public function blogExists($blogId) {
        $blogList = $this->getBlogList();

        // loop through each blog
        foreach ($blogList['blog'] as $blog) {
            if ($blog['id'] == $blogId) {
                return true;
            }
        }

        return false;
    }



    /**
     * find the next blog id to use from blog_list.json
     * @return integer
     */
    private function nextBlogId() {
        $blogsLen = sizeof($this->blogList['blog']);

        $lastId = $this->blogList['blog'][$blogsLen - 1]['id'];

        return ($lastId + 1);
    }



    /**
     * write a new json string into the 'blog list' json object
     * @param string $newblogList
     */
    private function setblogList($newBlogList) {
        $blogList_json = json_encode($newBlogList, JSON_PRETTY_PRINT);
        file_put_contents($this->blogListPath, $blogList_json);
    }



    /**
     * delete a blog entry
     * @param int $blogId the blog id
     * @return bool true if successful, false if fails
     */
    public function deleteBlog($blogId) {
        if ($this->blogExists($blogId)) {
            // if blog does exist

            $blogList = $this->getBlogList();
    

            //
            // ─── DELETE ENTRY FROM BLOG_LIST.json ─────────────────────────────────
            //
    
            // loop through each blog
            for ($i = 0; $i < sizeof($blogList['blog']); $i++) {
                if ($blogList['blog'][$i]['id'] == $blogId) {
                    
                    // delete entry from array
                    array_splice($blogList['blog'], $i, 1);

                    // update blog_list.json
                    $this->setBlogList($blogList);

                    break;
                }
            }
            
            // ─────────────────────────────────────────────────────────────────
    


            $this->Session->setSuccess('Blog deletion successful.');

            return true;
        }
        else {
            // if blog does not exist

            $this->Session->setError('Blog not found. Deletion failed.');

            return false;
        }
    }
}
# Fire Elf

## About
Fire Elf is my attempt at making a basic WordPress CMS. This is inspired by [Tania's Laconia project](https://github.com/taniarascia/laconia) and my goal of learning the MVC architecture. Fire Elf does the very basics that WordPress does, such as CRUD operations for pages, images, and blogs. This project isn't ready for production use, but great for studying and forking.

## Tech
Fire Elf is based in PHP. I incorporated PHP OOP and utilized classes to help build the MVC architecture. I did not use an external database, but instead created JSON files to save data. I found out that SQLite is probably a better option in this case, so I would like to incorporate this in the future.

## File Structure
```
/admin
    /public
        index.php
    /src
        /controllers
        /models
        /views

/client
    ...public website...
```
Fire Elf is broken down into ```admin``` and ```client``` directories. ```admin``` is the ```admin``` panel where, after logging in, the user can make changes to the public website, like WordPress. ```client``` is the actual public website.

```admin``` uses the MVC architecture and is divided up into ```controllers```, ```models```, and ```views```. There is a ```public``` and ```src``` directory. ```public``` contains the static assests, and ```src``` contains all of the logic. Requests to ```admin``` start at the index.php file in ```public```, then gets redirected to a controller in ```src```, which returns a view that is also located in ```src```.

```client``` can be arranged in any way. In the .config file, you specify where, for example, the image dir is located.

## Features
Fire Elf is able to perform CRUD operations on the different models present. So far, this includes ```Pages```, ```Media``` (images), and ```Blogs```. The settings page can alter the ```User``` model. Pages can be created, edited, and deleted using the [Quilljs wysiwig editor](https://quilljs.com/). The same goes for Blogs. Images can be uploaded and deleted, and gets stored in a media library similar to WordPress. Within the Quilljs editor, the user can open the media library.

## Future
Features to add in the future:
- add user roles
- add nav management to change client navigation
- add more media types (pdf, gifs, docx)
- replace JSON with SQLite
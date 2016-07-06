<?php
//Admin listing
if(empty($plugins)) {
    $plugins = array();
}
$plugin = array(
    'plugin_title' => 'Gallery Plugin',
    'plugin_url' => '/admin/plugin/gallery',
    'plugin_description' => 'A gallery plugin for GWPRESS.'
);
array_push($plugins, $plugin);

//Routes
if(empty($plugin_routes)) {
    $plugin_routes = array();
}

$plugin_route = array(
    'plugin_url' => '/admin/plugin/gallery',
    'plugin_page_name' => 'gallery/gallery.php'
);
array_push($plugin_routes, $plugin_route);

class GalleryPlugin {

    public function install() {
        //Create gallery categories table
        $sql1 = "CREATE TABLE gallery_categories (
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
title VARCHAR(255),
slug VARCHAR(255) UNIQUE,
description TEXT
)";
        db_query($sql1);

        //Create gallery table
        $sql2 = "CREATE TABLE gallery (
id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
category_slug VARCHAR(255),
title VARCHAR(255),
slug VARCHAR(255),
image VARCHAR(255),
description TEXT
)";
        db_query($sql2);

        $flash = new Flash();
        $flash->flash('flash_message', 'Database tables created!');
        header("Location: ".BASE_URL.'/admin/plugin/gallery');
    }

    public function create_gallery_folder($title, $description) {
        //Info
        $gallery_title = mysqli_real_escape_string(db_connect(), $title);
        $gallery_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $gallery_title)));
        $gallery_description = mysqli_real_escape_string(db_connect(), $description);

        //Insert new category into database
        db_query("INSERT INTO gallery_categories (title, slug, description) VALUES ('".$gallery_title."', '".$gallery_slug."', '".$gallery_description."');");

        if (!file_exists(UPLOAD_DIR.'gallery/')) {
            mkdir(UPLOAD_DIR.'gallery/', 0777, true);
        }
        $upload_dir = UPLOAD_DIR.'gallery/';
        if (substr($gallery_slug, -1) == '/') {
            mkdir($upload_dir.$gallery_slug, 0777);
            mkdir($upload_dir.$gallery_slug.'/sm', 0777);
            mkdir($upload_dir.$gallery_slug.'/lg', 0777);
        } else {
            $folder_fixed = ''.$gallery_slug.'/';
            mkdir($upload_dir.$folder_fixed, 0777);
            mkdir($upload_dir.$folder_fixed.'/sm', 0777);
            mkdir($upload_dir.$folder_fixed.'/lg', 0777);
        }
        $flash = new Flash();
        $flash->flash('flash_message', 'Category created!');
        header("Location: ".BASE_URL.'/admin/plugin/gallery');
    }

    public function resize_image($file, $destination, $dimension) {
        $fn = $file;
        $size = getimagesize($fn);
        $ratio = $size[0]/$size[1]; // width/height
        if( $ratio > 1) {
            $width = $dimension;
            $height = $dimension/$ratio;
        }
        else {
            $width = $dimension*$ratio;
            $height = $dimension;
        }
        $src = imagecreatefromstring(file_get_contents($fn));
        $dst = imagecreatetruecolor($width,$height);
        imagecopyresampled($dst,$src,0,0,0,0,$width,$height,$size[0],$size[1]);
        imagedestroy($src);
        imagepng($dst,$destination); // adjust format as needed
        imagedestroy($dst);
    }

    public function upload_file($file, $folder, $title, $description) {
        //Info
        $upload_folder = mysqli_real_escape_string(db_connect(), $folder);
        $upload_title = mysqli_real_escape_string(db_connect(), $title);
        $upload_slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $upload_title)));
        $upload_description = mysqli_real_escape_string(db_connect(), $description);

        //Image file save
        $target_dir = UPLOAD_DIR.'gallery/'.$upload_folder.'/';
        $target_file = $target_dir.'/'.basename($file["name"]);
        $target_file_sm = $target_dir.'/sm/'.basename($file["name"]);
        $target_file_lg = $target_dir.'/lg/'.basename($file["name"]);
        if(move_uploaded_file($file["tmp_name"], $target_file)) {
            //Resize and save in folders
            $this->resize_image($target_file, $target_file_sm, 250);
            $this->resize_image($target_file, $target_file_lg, 800);

            //Insert new category into database
            db_query("INSERT INTO gallery (category_slug, title, slug, image, description) VALUES ('".$upload_folder."', '".$upload_title."', '".$upload_slug."', '".$file["name"]."', '".$upload_description."');");

            $flash = new Flash();
            $flash->flash('flash_message', 'File uploaded!');
            header("Location: ".BASE_URL.'/admin/plugin/gallery');
        } else {
            $flash = new Flash();
            $flash->flash('flash_message', 'Sorry, there was an error uploading your file.', 'warning');
            header("Location: ".BASE_URL.'/admin/plugin/gallery');
        }
    }

    public function get_gallery_images($folder) {
        $gallery_images = db_select("SELECT * FROM gallery WHERE category_slug = '".$folder."'");
        return $gallery_images;
    }

    public function delete_gallery($directory) {
        $gallery_directory = mysqli_real_escape_string(db_connect(), $directory);

        if(empty($gallery_directory)) {
            $flash = new Flash();
            $flash->flash('flash_message', 'Category does not exist!', 'warning');
            header("Location: ".BASE_URL.'/admin/plugin/gallery');
        } else {
            //Delete db entries
            db_query("DELETE FROM gallery_categories WHERE slug = '".$gallery_directory."'");
            $gallery_images = db_query("SELECT * FROM gallery WHERE category_slug = '".$gallery_directory."'");
            foreach($gallery_images as $gallery_image) {
                db_query("DELETE FROM gallery WHERE category_slug = '".$gallery_image['category_slug']."'");
            }

            //Delete files
            $gallery_full_dir = UPLOAD_DIR.'gallery/'.$gallery_directory;
            $this->rrmdir($gallery_full_dir);

            $flash = new Flash();
            $flash->flash('flash_message', 'Category deleted!');
            header("Location: ".BASE_URL.'/admin/plugin/gallery');
        }
    }

    public function rrmdir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir")
                        $this->rrmdir($dir."/".$object);
                    else unlink   ($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public function delete_image($id) {
        $dimage = db_select_row("SELECT * FROM gallery WHERE id = '".$id."'");
        $target_dir = UPLOAD_DIR.'gallery/'.$dimage['category_slug'].'/';
        unlink($target_dir."/".$dimage['image']);
        unlink($target_dir."/sm/".$dimage['image']);
        unlink($target_dir."/lg/".$dimage['image']);
        db_query("DELETE FROM gallery WHERE id = '".$id."'");

        $flash = new Flash();
        $flash->flash('flash_message', 'Image '.$dimage['title'].' deleted!');
        header("Location: ".BASE_URL.'/admin/plugin/gallery');

    }
}
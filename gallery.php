<?php
$gallery_class = new GalleryPlugin();

if(isset($_POST['install'])) {
    $gallery_class->install();
}

if(isset($_POST['gallery_categories'])) {
    $gallery_folder = $_POST['title'];
    $gallery_description = $_POST['description'];
    $gallery_class->create_gallery_folder($gallery_folder, $gallery_description);
}

if(isset($_POST['gallery_images'])) {
    $image_file = $_FILES['file_upload'];
    $image_directory = $_POST['directory'];
    $image_title = $_POST['title'];
    $image_description = $_POST['description'];
    $gallery_class->upload_file($image_file, $image_directory, $image_title, $image_description);
}

if(isset($_POST['gallery_delete'])) {
    $gallery_directory = $_POST['directory'];
    $gallery_class->delete_gallery($gallery_directory);
}

if(isset($_POST['image_delete'])) {
    $gallery_directory = $_POST['dimage_image'];
    $gallery_class->delete_image($gallery_directory);
}
?>
<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header">Gallery Plugin</h1>
    <p>This is a custom gallery plugin made by <a href="http://georgewhitcher.com" target="_blank">George Whitcher</a> for <a href="http://github.com/gwhitcher/gwpress" target="_blank">GWPRESS</a>.</p>
    <p><form id="form1" class="form-horizontal" enctype="multipart/form-data" method="post" accept-charset="utf-8" action="<?php echo BASE_URL; ?>/admin/plugin/gallery"><button type="submit" id="submit" name="install" class="btn btn-default">Install</button></form></p>
</div>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header">Gallery Categories</h1>
    <form id="form2" class="form-horizontal" enctype="multipart/form-data" method="post" accept-charset="utf-8" action="<?php echo BASE_URL; ?>/admin/plugin/gallery">

        <div class="form-group">
            <label for="title" class="col-sm-2 control-label">Title</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="title" name="title" placeholder="Title">
            </div>
        </div>

        <div class="form-group">
            <label for="description" class="col-sm-2 control-label">Description</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="description" name="description" placeholder="Description">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="submit" name="gallery_categories" class="btn btn-default">Submit</button>
            </div>
        </div>
    </form>
</div>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header">Upload Photo</h1>
    <form id="form3" class="form-horizontal" enctype="multipart/form-data" method="post" accept-charset="utf-8" action="<?php echo BASE_URL; ?>/admin/plugin/gallery">

        <div class="form-group">
            <label for="file" class="col-sm-2 control-label">File</label>
            <div class="col-sm-10">
                <span class="btn btn-default btn-file">
                Choose a file...<input type="file" id="file_upload" name="file_upload" placeholder="File" required>
                </span>
            </div>
        </div>

        <div class="form-group">
            <label for="directory" class="col-sm-2 control-label">Directory</label>
            <div class="col-sm-10">
                <select class="form-control" id="directory" name="directory">
                    <?php
                    $gallery_categories = db_select("SELECT * FROM gallery_categories");
                    foreach($gallery_categories as $gcat) {
                        echo '<option value="'.$gcat['slug'].'">Title: '.$gcat['title'].' - Slug: '.$gcat['slug'].'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="title" class="col-sm-2 control-label">Title</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="title" name="title" placeholder="Title">
            </div>
        </div>

        <div class="form-group">
            <label for="description" class="col-sm-2 control-label">Description</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="description" name="description" placeholder="Description">
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="submit" name="gallery_images" class="btn btn-default">Submit</button>
            </div>
        </div>

    </form>
</div>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header">Delete Gallery</h1>
    <form id="form4" class="form-horizontal" enctype="multipart/form-data" method="post" accept-charset="utf-8" action="<?php echo BASE_URL; ?>/admin/plugin/gallery">

        <div class="form-group">
            <label for="directory" class="col-sm-2 control-label">Directory</label>
            <div class="col-sm-10">
                <select class="form-control" id="directory" name="directory">
                    <option value="0">Select one....</option>
                    <?php
                    $gallery_categories = db_select("SELECT * FROM gallery_categories");
                    foreach($gallery_categories as $gcat) {
                        echo '<option value="'.$gcat['slug'].'">'.$gcat['title'].'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="submit" name="gallery_delete" class="btn btn-default" onclick="return confirm('Are you sure you want to delete?');">Submit</button>
            </div>
        </div>

    </form>
</div>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header">Delete Image</h1>
    <form id="form4" class="form-horizontal" enctype="multipart/form-data" method="get" accept-charset="utf-8" action="<?php echo BASE_URL; ?>/admin/plugin/gallery">

        <div class="form-group">
            <label for="dimage_directory" class="col-sm-2 control-label">Directory</label>
            <div class="col-sm-10">
                <select class="form-control" id="dimage_directory" name="dimage_directory">
                    <option value="0">Select one....</option>
                    <?php
                    $gallery_categories = db_select("SELECT * FROM gallery_categories");
                    foreach($gallery_categories as $gcat) {
                        echo '<option value="'.$gcat['slug'].'">'.$gcat['title'].'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="submit" name="category_images" class="btn btn-default">Submit</button>
            </div>
        </div>

    </form>
    <?php if(!empty($_GET['dimage_directory'])) {
        $dimage_directory = mysqli_real_escape_string(db_connect(), $_GET['dimage_directory']);
        ?>
    <form id="form5" class="form-horizontal" enctype="multipart/form-data" method="post" accept-charset="utf-8" action="<?php echo BASE_URL; ?>/admin/plugin/gallery">

        <div class="form-group">
            <label for="dimage_image" class="col-sm-2 control-label">Image</label>
            <div class="col-sm-10">
                <select class="form-control" id="dimage_image" name="dimage_image">
                    <?php
                    $dimage_images = db_select("SELECT * FROM gallery WHERE category_slug = '".$dimage_directory."'");
                    foreach($dimage_images as $dimage_image) {
                        echo '<option value="'.$dimage_image['id'].'">'.$dimage_image['title'].'</option>';
                    }
                    ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="submit" name="image_delete" class="btn btn-default" onclick="return confirm('Are you sure you want to delete?');">Submit</button>
            </div>
        </div>
    </form>
    <?php } ?>
</div>

<div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
    <h1 class="page-header">View Category</h1>
    <div class="form-group">
        <label for="view_category" class="col-sm-2 control-label">Code:</label>
        <div class="col-sm-10">
            <textarea class="form-control" id="view_category" name="view_category">
$gallery_category = 'test-category';
$gallery_class = new GalleryPlugin();
$gallery_images = $gallery_class->get_gallery_images($gallery_category);
$i = 1;
echo '<div class="row">';
foreach($gallery_images as $gallery_item) {

    echo '<div class="col-md-4">';
    echo '<div class="gc-photo-gallery-photo"><!-- Trigger the modal with a button -->
<img class="img-responsive" style="cursor: pointer;" data-toggle="modal" data-target="#myModal'.$i.'" src="'.BASE_URL.'/gw-content/uploads/gallery/'.$gallery_category.'/sm/'.$gallery_item['image'].'"></div>

                        <!-- Modal -->
<div id="myModal'.$i.'" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">'.$gallery_item['title'].'</h4>
      </div>
      <div class="modal-body">
        <p><img class="img-responsive" data-toggle="modal" data-target="#myModal" src="'.BASE_URL.'/gw-content/uploads/gallery/'.$gallery_category.'/lg/'.$gallery_item['image'].'"></p>
        <div class="label label-default">Description</div>
        <p>'.$gallery_item['description'].'</p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>';
    echo '</div>';

    if ($i % 3 == 0) {
        echo '</div><div class="row">';
    }

    $i++;
}
            </textarea>
        </div>
    </div>
</div>
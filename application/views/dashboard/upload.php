
<form action="<?php echo base_url('upload/upload_file'); ?>" 
      method="post" 
      enctype="multipart/form-data">

    <input type="file" name="file" required><br><br>
    <button type="submit">Upload</button>
</form>

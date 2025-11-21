<!DOCTYPE html>
<html>

<head>
    <title>Upload Success</title>
</head>

<body>

    <h3>Image Uploaded Successfully!</h3>

    <ul>
        <?php foreach ($upload_data as $item => $value): ?>
            <li><?php echo $item; ?>: <?php echo $value; ?></li>
        <?php endforeach; ?>
    </ul>

    <img src="<?php echo base_url('uploads/profile/' . $upload_data['file_name']); ?>" width="200">

    <br><br>
    <a href="<?php echo site_url('upload'); ?>">Upload Another</a>

</body>

</html>
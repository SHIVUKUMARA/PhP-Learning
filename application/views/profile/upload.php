<!DOCTYPE html>
<html>

<head>
    <title>Profile Image Upload</title>
</head>

<body>

    <h2>Upload Profile Image</h2>

    <?php if (!empty($error)) echo $error; ?>

    <?php echo form_open_multipart('upload/do_upload'); ?>

    <input type="file" name="userfile" required />
    <br><br>
    <input type="submit" value="Upload Image" />

    </form>

</body>

</html>
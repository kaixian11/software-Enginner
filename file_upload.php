<?php
include 'config/database.php';

error_reporting(E_WARNING);

// now, if image is not empty, try to upload the image
if (isset($_FILES['files'])) {

    // upload to file to folder
    $target_file = $target_directory;

    $query = "SELECT complaintID FROM complaint ORDER BY complaintID DESC LIMIT 1";
    $stmt = $con->prepare($query);

    // execute our query
    $stmt->execute();
    $num = $stmt->rowCount();

    if ($num > 0) {
        // store retrieved row to a variable
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        extract($row);
    }

    // make sure the 'uploads' folder exists
    // if not, create it
    if (!is_dir($target_directory)) {
        mkdir($target_directory, 0777, true);
    }

    if (empty($file_upload_error_messages)) {
        //so try to upload the file
        // it means photo was uploaded
        foreach ($_FILES["files"]["tmp_name"] as $key => $value) {

            $names = $_FILES['files']['name'][$key];
            echo $tmp_names = $_FILES['files']['tmp_name'][$key];

            if (move_uploaded_file($tmp_names, $target_file . $names)) {
                $query_inset = "INSERT INTO files SET file_name=:file_name, complaintID=:complaintID";
                // prepare query for execution
                $stmt_inset = $con->prepare($query_inset);

                $stmt_inset->bindParam(':file_name', $names);
                $stmt_inset->bindParam(':complaintID', $complaintID);

                $stmt_inset->execute();
            } else {
                $file_upload_error_messages .= "<div>Unable to upload file.</div>";
                $file_upload_error_messages .= "<div>Update the record to upload file.</div>";
            }
        }
    }
}

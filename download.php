<?php
include 'config/database.php';

$id = isset($_GET['id']) ? $_GET['id'] :  die('ERROR: Record ID not found.');

$query_check = "SELECT file_name FROM complaint INNER JOIN files ON complaint.complaintID=files.complaintID WHERE files.complaintID = ?";
$stmt_check  = $con->prepare($query_check);

// this is the first question mark
$stmt_check->bindParam(1, $id);

// execute our query
$stmt_check->execute();

// store retrieved row to a variable
$num_check = $stmt_check->rowCount();

if ($num_check > 0) {

    if (!is_dir("file")) {
        mkdir("file", 0777, true);
    }

    $zip_file = "file/all-file.zip";
    touch($zip_file);

    //open zip file
    $zip = new ZipArchive;
    $this_zip = $zip->open($zip_file);

    try {
        // get record ID
        // isset() is a PHP function used to verify if a value is there or not


        // prepare select query
        $query = "SELECT file_name FROM complaint INNER JOIN files ON complaint.complaintID=files.complaintID WHERE files.complaintID = ?";
        $stmt = $con->prepare($query);

        // this is the first question mark
        $stmt->bindParam(1, $id);

        // execute our query
        $stmt->execute();

        // store retrieved row to a variable
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        extract($row);

        if ($this_zip) {

            // $file_with_path = "uploads/paip.jpg";
            // $name = "paip.jpg";
            // $zip->addFile($file_with_path, $name );
            do {
                $folder = opendir("uploads");
                if ($folder) {
                    extract($row);
                    var_dump($file_name);
                    while (false !== ($file  = readdir($folder))) {
                        if ($file == $file_name) {
                            $file_with_path = "uploads/" . $file;
                            $zip->addFile($file_with_path, $file);
                        }
                    }
                    closedir($folder);
                }
            } while ($row = $stmt->fetch(PDO::FETCH_ASSOC));
            $zip->close();

            if (file_exists($zip_file)) {
                //name when downloaded
                $download_name = "complaint.zip";

                header('Content-type: application/zip');
                header('Content-Disposition: attachment; filename="' . $download_name . '"');

                readfile($zip_file); // auto download    
                unlink($zip_file);
            }
        }
    } // show error
    catch (PDOException $exception) {
        die('ERROR: ' . $exception->getMessage());
    }
} else {
    header("Location: user_complaints_list?message=no_file_found");
}

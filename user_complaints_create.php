<!DOCTYPE html>
<html>

<?php
include 'check_session.php';
ob_start();
?>

<head>

    <?php include "bootstrap.php"; ?>

    <title>Create User</title>

    <link rel="stylesheet" href="css/styles.css" />

</head>

<body>
    <!-- NAVBAR -->
    <?php
    include "user_navbar.php";
    ?>
    <!-- NAVBAR END -->
    <main>

        <!-- Container -->
        <div class="container mt-5">
            <div class="page-header">
                <h1>Users</h1>
            </div>

            <!-- html form to create product will be here -->
            <?php
            $userID = $_SESSION['userID'];

            if ($_POST) {
              
                $title = $_POST['title'];
                $description = $_POST['description'];

                $validation = true;
                $target_file = "";
                $file_upload_error_messages = "";

                // Check Empty
                if ($title == "" || $description == "") {
                    $file_upload_error_messages .= "<div class='alert alert-danger'>Please make sure all fields are not empty</div>";
                    $validation = false;
                }

                if (!empty($_FILES["files"]["name"])) {
                    $target_directory = "uploads/";
                    include "file_upload.php";
                } 

                // include database connection
                include 'config/database.php';

                if ($validation == true) {
                    // include database connection
                    include 'config/database.php';

                    try {
                        // insert query
                        $query = "INSERT INTO complaint SET userID=:userID, title=:title, description=:description, complaint_date=:complaint_date, status='New'";
                        // prepare query for execution
                        $stmt = $con->prepare($query);

                        // bind the parameters
                        $stmt->bindParam(':userID', $userID);
                        $stmt->bindParam(':title', $title);
                        $stmt->bindParam(':description', $description);
                        $complaint_date = date('Y-m-d H:i:s'); // get the current date and time
                        $stmt->bindParam(':complaint_date', $complaint_date);

                        // Execute the query
                        if ($stmt->execute()) {
                            header("Location: user_complaints_list.php?message=create_success");
                            ob_end_flush();
                        } else {
                            if (file_exists($target_file)) {
                                unlink($target_file);
                            }
                            echo "<div class='alert alert-danger'>Unable to save record.</div>";
                        }
                    }
                    // show error
                    catch (PDOException $exception) {
                        die('ERROR: ' . $exception->getMessage());
                    }
                } else {
                    // it means there are some errors, so show them to user
                    echo "<div class='alert alert-danger'>";
                    echo "<div>{$file_upload_error_messages}</div>";
                    echo "</div>";
                }
            }
            ?>

            <!-- PHP insert code will be here -->

            <!-- html form here where the product information will be entered -->
            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" enctype="multipart/form-data">
                <div class="mb-5">
                    <p class="fw-bold">Title</p>
                    <div class="input-group input-group-lg">
                        <input type='text' name='title' value="<?php echo isset($title) ? $title : ""; ?>" class='form-control' />
                    </div>
                </div>
                <div class="mb-5">
                    <p class="fw-bold ">Photo / Video</p>
                    <div class="input-group input-group-lg">
                        <input type='file' name='files[]' class='form-control' multiple/>
                    </div>
                </div>
                <div class="mb-5">
                    <p class="fw-bold ">Description</p>
                    <div class="input-group input-group-lg">
                        <textarea name="description" class="form-control" placeholder="Your Message *" style="width: 100%; height: 165px;"><?php echo isset($description) ? $description : ""; ?></textarea>
                    </div>
                </div>
                <div class="m-auto mt-5 row justify-content-center">
                    <input type='submit' value='Submit' class='btn btn-primary col-6 me-3' />
                    <a href='user_complaints_list.php' class='btn btn-danger col-3 ms-3'>Cancle</a>
                </div>
            </form>
        </div>
        <!-- End Container  -->

        <hr class="featurette-divider">

        <!-- FOOTER -->
        <footer class="container">
            <p class="float-end"><a class="text-decoration-none fw-bold" href="#">Back to top</a></p>
            <p class="text-muted fw-bold">&copy; 2022 Chia Yeu Shyang</p>
        </footer>
    </main>
</body>

</html>
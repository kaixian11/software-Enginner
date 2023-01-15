<!DOCTYPE html>
<html>

<?php
include 'check_session.php';
ob_start();
?>

<head>
    <?php include "bootstrap.php"; ?>

    <title>Read Complaint</title>

    <link rel="stylesheet" href="css/styles.css" />

</head>

<body>
    <div>
        <!-- NAVBAR -->
        <?php
        include "helpdesk_navbar.php";
        ?>
        <!-- NAVBAR END -->

        <!-- Content Start-->
        <div class="container mt-5">
            <div class="page-header">
                <h1>Enter an ID</h1>
            </div>
            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="GET">
                <table class='table table-hover table-responsive table-bordered'>
                    <tr>
                        <td>ID</td>
                        <td><input type='text' name='id' class='form-control' /></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td>
                            <input type='submit' value='Search' class='btn btn-primary' />
                        </td>
                    </tr>
                </table>
            </form>
            <?php
            // get passed parameter value, in this case, the record ID
            // isset() is a PHP function used to verify if a value is there or not
            $complaintID = isset($_GET['id']) ? $_GET['id'] : die();

            echo "<hr class='featurette-divider'>";

            //include database connection
            include 'config/database.php';

            // read current record's data
            try {
                // prepare select query
                $query = "SELECT title, status, description, remark, username FROM complaint INNER JOIN users ON complaint.userID=users.userID WHERE complaint.complaintID = ?";
                $stmt = $con->prepare($query);

                // Bind the parameter
                $stmt->bindParam(1, $complaintID);

                // execute our query
                $stmt->execute();

                $num = $stmt->rowCount();

                if ($num > 0) {
                    // store retrieved row to a variable
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    echo "<div class='page-header'>";
                    echo "<h1>Read Complaint</h1>";
                    echo "</div>";

                    extract($row);
                } else {
                    die("<p>Cannot Find the complaint with complaintID = <b>$complaintID</b></p>");
                }
            }

            // show error
            catch (PDOException $exception) {
                die('ERROR: ' . $exception->getMessage());
            }

            if ($_POST) {

                $status = $_POST['status'];
                $remark = $_POST['remark'];
                $categories = $_POST['categories'];

                $validation = true;

                // error message is empty
                $file_upload_error_messages = "";

                // Check Empty
                if ($status == "") {
                    $file_upload_error_messages .= "<div class='alert alert-danger'>Please make sure all fields are not empty</div>";
                    $validation = false;
                }

                if ($categories == "") {
                    $categories = "";
                }

                if ($validation) {
                    try {

                        $query = "UPDATE complaint SET status=:status, remark=:remark, categories=:categories WHERE complaintID=:complaintID";
                        // prepare query for execution
                        $stmt = $con->prepare($query);

                        // bind the parameters

                        $stmt->bindParam(':status', $status);
                        $stmt->bindParam(':remark', $remark);
                        $stmt->bindParam(':complaintID', $complaintID);
                        $stmt->bindParam(':categories', $categories);

                        // Execute the query
                        if ($stmt->execute()) {
                            header("Location: helpdesk_complaints_list.php?message=update_success&id=$complaintID");
                            ob_end_flush();
                        } else {
                            echo "<div class='alert alert-danger'>Unable to update record. Please try again.</div>";
                        }
                    }
                    // show errors
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

            <form action="<?php echo $_SERVER["PHP_SELF"] . "? id={$complaintID}"; ?>" method="POST" enctype="multipart/form-data">
                <!--we have our html table here where the record will be displayed-->
                <div class="table-responsive">
                    <table class='table table-hover table-responsive table-bordered'>
                        <tr>
                            <th>Username</th>
                            <td class="text-center"><?php echo $username; ?></td>
                        </tr>
                        <tr>
                            <th>Complaints</th>
                            <td class="text-center"><?php echo $title;  ?></td>
                        </tr>
                        <tr>
                            <th>Description</th>
                            <td class="text-center"><?php echo $description;  ?></td>
                        </tr>
                        <tr>
                            <th>Status of Complaint</th>
                            <td>
                                <input type="hidden" class="btn-check" name="status" value="Pending" />
                                <div class="d-block d-md-flex mx-1 align-items-center justify-content-center">
                                    <input type="radio" class="btn-check" name="status" id="Not Relevant" value="Not Relevant" autocomplete="off" <?php echo ($status == 'Not Relevant') ?  "checked" : "";  ?>>
                                    <label class="btn btn-lg btn-outline-danger col-12 col-md-3 me-md-4" for="Not Relevant">Not Relevant</label>
                                    <input type="radio" class="btn-check" name="status" id="KIV" value="KIV" autocomplete="off" <?php echo ($status == 'KIV') ?  "checked" : "";  ?>>
                                    <label class="btn btn-lg btn-outline-primary col-12 col-md-2" for="KIV">KIV</label>
                                    <input type="radio" class="btn-check" name="status" id="Active" value="Active" autocomplete="off" <?php echo ($status == 'Active') ?  "checked" : "";  ?>>
                                    <label class="btn btn-lg btn-outline-primary col-12 col-md-2 me-md-4" for="Active">Active</label>
                                    <input type="radio" class="btn-check" name="status" id="Done" value="Done" autocomplete="off" <?php echo ($status == 'Done') ?  "checked" : "";  ?>>
                                    <label class="btn btn-lg btn-outline-success col-12 col-md-2" for="Done">Done</label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>Feedback</th>
                            <td class="text-center">
                                <input type='text' name='remark' value="<?php echo (($remark != "") ? $remark : ""); ?>" class='form-control' />
                            </td>
                        </tr>
                        <tr>
                            <th>Photo / Video</th>
                            <td class="text-center">
                                <?php
                                $query_check = "SELECT file_name FROM complaint INNER JOIN files ON complaint.complaintID=files.complaintID WHERE files.complaintID = ?";
                                $stmt_check  = $con->prepare($query_check);

                                // this is the first question mark
                                $stmt_check->bindParam(1, $complaintID);

                                // execute our query
                                $stmt_check->execute();

                                // store retrieved row to a variable
                                $num_check = $stmt_check->rowCount();

                                if ($num_check > 0) {
                                    echo "<a class='col-6 col-lg m-auto me-lg-1' href='download.php?id={$complaintID}' role='butto'><i class='fa-solid fa-file-arrow-down fa-xl'></i></a>";
                                } else {
                                    echo "<i class='fa-solid fa-file-arrow-down fa-xl text-muted'></i>";
                                }
                                ?></td>
                        </tr>
                        <tr>
                            <th>Categories</th>
                            <td class="text-center">
                                <select class="form-select" name='categories'>
                                <option value="">Select a categories</option>
                                    <?php
                                    $query_prev = "SELECT categories FROM complaint WHERE complaintID=:complaintID";
                                    $stmt_prev = $con->prepare($query_prev);
                                    $stmt_prev->bindParam(":complaintID", $complaintID);
                                    $stmt_prev->execute();
                                    $row_prev = $stmt_prev->fetch(PDO::FETCH_ASSOC);
                                    extract($row_prev);

                                    $query_role = "SELECT role FROM roles WHERE role LIKE '%Executive%'";
                                    $stmt_role = $con->prepare($query_role);
                                    $stmt_role->execute();

                                    $num_role = $stmt_role->rowCount();

                                    //check if more than 0 record found
                                    if ($num_role > 0) {
                                        while ($row_role = $stmt_role->fetch(PDO::FETCH_ASSOC)) {
                                            extract($row_role);
                                            if ($role == $row_prev['categories']) {
                                                echo "<option value=\"$role\" selected>$role</option>";
                                            } else {
                                                echo "<option value=\"$role\">$role</option>";
                                            }
                                        }
                                    }
                                    ?>

                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="2" class="text-center">
                                <input type='submit' value='Save Changes' class='btn btn-primary col-5' />
                                <a href='helpdesk_complaints_list.php' class='btn btn-danger col-5'>Back to complaints list</a>
                            </td>
                        </tr>
                    </table>
                </div>
            </form>
        </div>

        <!-- Content End -->

        <hr class="featurette-divider">

        <!-- FOOTER -->
        <footer class="container">
            <p class="float-end"><a class="text-decoration-none fw-bold" href="#">Back to top</a></p>
            <p class="text-muted fw-bold">&copy; 2022 Than Kai Xian</p>
        </footer>
        <!-- FOOTER END -->
    </div>
</body>

</html>
<!DOCTYPE html>
<html>

<?php
include 'check_session.php';
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
        include "user_navbar.php";
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
            ?>

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
                        <td class="text-center"><?php echo $status; ?></td>
                    </tr>
                    <tr>
                        <th>Feedback</th>
                        <td class="text-center">
                            <?php
                            if ($remark != "") {
                                echo  $remark;
                            } else {
                                echo  "....";
                            }
                            ?></td>
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
                        <td colspan="2">
                            <a href='user_complaints_list.php' class='btn btn-danger col-12'>Back to complaints list</a>
                        </td>
                    </tr>
                </table>
            </div>
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
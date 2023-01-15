<!DOCTYPE html>
<html>

<?php
include 'check_session.php';
ob_start();
?>

<head>

    <?php include "bootstrap.php"; ?>

    <title>Update User</title>

    <link rel="stylesheet" href="css/styles.css" />

</head>

<body>
    <!-- NAVBAR -->
    <?php
    include "navbar.php";
    ?>
    <!-- NAVBAR END -->
    <main>

        <body>
            <!-- container -->
            <div class="container mt-5">
                <div class="page-header">
                    <h1>Update User</h1>
                </div>
                <?php
                // get passed parameter value, in this case, the record ID
                // isset() is a PHP function used to verify if a value is there or not
                $id = isset($_GET['id']) ? $_GET['id'] : die('ERROR: Record ID not found.');

                //include database connection
                include 'config/database.php';

                // read current record's data
                try {
                    // prepare select query
                    $query = "SELECT userID, username, password, email, gender, role FROM users WHERE userID = ? LIMIT 0,1";
                    $stmt = $con->prepare($query);

                    // this is the first question mark
                    $stmt->bindParam(1, $id);

                    // execute our query
                    $stmt->execute();

                    // store retrieved row to a variable
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                }

                // show error
                catch (PDOException $exception) {
                    die('ERROR: ' . $exception->getMessage());
                }
                ?>

                <?php
                // check if form was submitted
                if ($_POST) {

                    $new_password = $_POST['new_password'];
                    $confirm_password = $_POST['confirm_password'];
                    $email = $_POST['email'];
                    $username = $_POST['username'];
                    $gender = $_POST['gender'];
                    $role = $_POST['role'];

                    $validation = true;

                    // error message is empty
                    $file_upload_error_messages = "";

                    // Check Empty
                    if ($username == "" || $gender == "" || $email == "" || $role == "") {
                        echo "<div class='alert alert-danger'>Please make sure all fields are not empty</div>";
                        $validation = false;
                    }

                    if ($confirm_password !== $new_password) {
                        $file_upload_error_messages .= "<div class='alert alert-danger'>Please enter valid confirm password</div>";
                        $validation = false;
                    } else if ($new_password == "" && $confirm_password == "") {
                        $pass = $row['password'];
                    }

                    if ($validation) {
                        try {
                            // write update query
                            // in this case, it seemed like we have so many fields to pass and
                            // it is better to label them and not use question marks
                            $query = "UPDATE users SET password=:password, username=:username, email=:email, gender=:gender, role=:role WHERE userID=:userID";
                            // prepare query for execution
                            $stmt = $con->prepare($query);

                            // bind the parameters
                            $stmt->bindParam(":userID", $id);
                            if ($new_password == "" && $confirm_password == "") {
                                $stmt->bindParam(':password', $pass);
                            } else {
                                $stmt->bindParam(':password', md5($new_password));
                            }        
                            $stmt->bindParam(':username', $username);
                            $stmt->bindParam(':email', $email);
                            $stmt->bindParam(':gender', $gender);
                            $stmt->bindParam(':role', $role);
                            // Execute the query

                            // Execute the query
                            if ($stmt->execute()) {
                                header("Location: admin_users_list.php?message=update_success&id=$id");
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
                } ?>


                <!--we have our html form here where new record information can be updated-->
                <form action="<?php echo $_SERVER["PHP_SELF"] . "?id={$id}"; ?>" method="post" enctype="multipart/form-data">
                    <table class='table table-hover table-responsive table-bordered'>
                        <tr>
                            <td>New Password</td>
                            <td><input type='password' name='new_password' placeholder="Enter new password" class='form-control' /></td>
                        </tr>
                        <tr>
                            <td>Confirm Password</td>
                            <td><input type='password' name='confirm_password' placeholder="Enter confirm password" class='form-control' /></td>
                        </tr>
                        <tr>
                            <td>Username</td>
                            <td><input type='text' name='username' value="<?php echo $row['username']; ?>" class='form-control' /></td>
                        </tr>
                        <tr>
                            <td>Email</td>
                            <td><input type='text' name='email' value="<?php echo $row['email']; ?>" class='form-control' /></td>
                        </tr>
                        <tr>
                            <td>Gender</td>
                            <td class="d-flex">
                                <div class="form-check mx-3">
                                    <input class="form-check-input" type="radio" name="gender" value="Male" id="Male" required <?php echo ($row['gender'] == 'Male') ?  "checked" : "";  ?>>
                                    <label class="form-check-label" for="Male">
                                        Male
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="gender" value="Female" id="Female" required <?php echo ($row['gender'] == 'Female') ?  "checked" : "";  ?>>
                                    <label class="form-check-label" for="Female">
                                        Female
                                    </label>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>Role</td>
                            <td>
                                <select class="form-select" name='role' aria-label="role">
                                    <option selected>Select a role</option>

                                    <?php
                                    // select all data
                                    $query_prev = "SELECT role FROM roles ORDER BY role ASC";
                                    $stmt_prev = $con->prepare($query_prev);
                                    $stmt_prev->execute();

                                    // this is how to get number of rows returned
                                    $num_prev = $stmt_prev->rowCount();

                                    //check if more than 0 record found
                                    if ($num_prev > 0) {

                                        while ($row_prev = $stmt_prev->fetch(PDO::FETCH_ASSOC)) {
                                            extract($row_prev);
                                            echo "<option value='$role'";

                                            if ($role == $row['role']) {
                                                echo "<option value=\"$role\" selected>$role</option>";
                                            } else {
                                                echo "<option value=\"$role\">$role</option>";
                                            }

                                            echo ">$role</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </td>
                        </tr>
            </div>
            <tr>
                <td></td>
                <td>
                    <input type='submit' value='Save Changes' class='btn btn-primary' />
                    <a href='admin_users_list.php' class='btn btn-danger'>Back to read customers</a>
                </td>
            </tr>
            </table>
            </form>
            </div>
            <!-- end .container -->
        </body>


        <hr class="featurette-divider">

        <!-- FOOTER -->
        <footer class="container">
            <p class="float-end"><a class="text-decoration-none fw-bold" href="#">Back to top</a></p>
            <p class="text-muted fw-bold">&copy; 2022 Than Kai Xian</p>
        </footer>
    </main>
</body>

</html>
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
    include "admin_navbar.php";
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
            $file_upload_error_messages = "";

            if ($_POST) {
                $userID = trim($_POST['userID']);
                $password = $_POST['password'];
                $confirm_password = $_POST['confirm_password'];
                $username = $_POST['username'];
                $email = $_POST['email'];
                $role = $_POST['role'];
                $gender = $_POST['gender'];

                $validation = true;

                // Check Empty
                if ($userID == "" || $password == "" || $username == "" || $email == "" || $role == "") {
                    $file_upload_error_messages .= "<div class='alert alert-danger'>Please make sure all fields are not empty</div>";
                    $validation = false;
                }

                // include database connection
                include 'config/database.php';

                // delete message prompt will be here

                // select all data
                $query_check = "SELECT userID FROM users WHERE userID=:userID";
                $stmt_check = $con->prepare($query_check);
                $stmt_check->bindParam(':userID', $userID);

                $stmt_check->execute();

                // this is how to get number of rows returned
                $num_check = $stmt_check->rowCount();

                if ($num_check > 0) {
                    $file_upload_error_messages .= "<div class='alert alert-danger'>User already exist</div>";
                    $validation = false;
                }

                if ($validation == true) {
                    // include database connection
                    include 'config/database.php';

                    try {
                        // insert query
                        $query = "INSERT INTO users SET userID=:userID, password=:password, username=:username, gender=:gender, email=:email, role=:role ,register_date=:register_date";
                        // prepare query for execution
                        $stmt = $con->prepare($query);

                        // bind the parameters
                        $stmt->bindParam(':userID', $userID);
                        $stmt->bindParam(':password', md5($password));
                        $stmt->bindParam(':username', $username);
                        $stmt->bindParam(':email', $email);
                        $stmt->bindParam(':gender', $gender);
                        $stmt->bindParam(':role', $role);
                        $register_date = date('Y-m-d H:i:s'); // get the current date and time
                        $stmt->bindParam(':register_date', $register_date);

                        // Execute the query
                        if ($stmt->execute()) {
                            header("Location: admin_users_list.php?message=create_success");
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
                <div class="m-auto">
                    <p class="fw-bold">userID</p>
                    <div class="input-group input-group-lg mb-3">
                        <span class="input-group-text" id="basic-addon1">@</span>
                        <input type="text" class="form-control" name="userID" value="<?php echo isset($userID) ? $userID : ""; ?>" placeholder="userID" aria-label="userID" aria-describedby="basic-addon1" />
                    </div>
                </div>
                <div class="d-md-flex row">
                    <div class="mb-3 col">
                        <p class="fw-bold">Password</p>
                        <div class="input-group input-group-lg">
                            <input type='password' name='password' class='form-control input-group-lg' />
                        </div>
                    </div>
                    <div class="ms-md-1 mb-3 col">
                        <p class="fw-bold">Confirm Password</p>
                        <div class="input-group input-group-lg">
                            <input type='password' name='confirm_password' class='form-control' />
                        </div>
                    </div>
                </div>
                <div class="d-md-flex row">
                    <div class="ms-md-1 mb-3 col">
                        <p class="fw-bold">Username</p>
                        <div class="input-group input-group-lg">
                            <input type='text' name='username' class='form-control' />
                        </div>
                    </div>
                    <div class="mb-3 col">
                        <p class="fw-bold">Role</p>
                        <div class="input-group input-group-lg">
                            <select class="form-select" name='role' aria-label="role">
                                <option selected>Select a role</option>

                                <?php
                                // select all data
                                $query = "SELECT role FROM roles ORDER BY role ASC";
                                $stmt = $con->prepare($query);
                                $stmt->execute();

                                // this is how to get number of rows returned
                                $num = $stmt->rowCount();

                                //check if more than 0 record found
                                if ($num > 0) {

                                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                                        extract($row);
                                        echo "<option value='$role'";

                                        if (isset($_POST["role"]) && $_POST["role"] == $role) {
                                            echo "selected";
                                        }

                                        echo ">$role</option>";
                                    }
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="mb-3">
                    <p class="fw-bold">Email</p>
                    <div class="input-group input-group-lg">
                        <input type='email' name='email' value="<?php echo isset($email) ? $email : ""; ?>" class='form-control' />
                    </div>
                </div>
                <p class="fw-bold">Gender</p>
                <input type="hidden" class="btn-check" name="gender" value="" />
                <div class="d-flex mx-1 mb-3">
                    <input type="radio" class="btn-check" name="gender" id="Male" value="Male" autocomplete="off" <?php echo ((isset($gender)) && ($gender == 'Male')) ?  "checked" : ""; ?>>
                    <label class="btn btn-lg btn-outline-primary col-6" for="Male">Male</label>
                    <input type="radio" class="btn-check" name="gender" id="Female" value="Female" autocomplete="off" <?php echo ((isset($gender)) && ($gender == 'Female')) ?  "checked" : ""; ?>>
                    <label class="btn btn-lg btn-outline-danger col-6" for="Female">Female</label>
                </div>

                <div class="m-auto mt-5 row justify-content-center">
                    <input type='submit' value='Save' class='btn btn-primary col-6 me-3' />
                    <a href='admin_users_list.php' class='btn btn-danger col-3 ms-3'>Cancle</a>
                </div>
            </form>
        </div>
        <!-- End Container  -->

        <hr class="featurette-divider">

        <!-- FOOTER -->
        <footer class="container">
            <p class="float-end"><a class="text-decoration-none fw-bold" href="#">Back to top</a></p>
            <p class="text-muted fw-bold">&copy; 2022 Than Kai Xian</p>
        </footer>
    </main>
</body>

</html>
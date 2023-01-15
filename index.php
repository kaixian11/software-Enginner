<!DOCTYPE html>
<html>

<?php
// Start the session
session_start();
?>


<head>

    <?php include "bootstrap.php"; ?>

    <title>Login</title>

    <link rel="stylesheet" href="css/login.css" />

    <link href="/docs/5.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">

    <!-- Favicons -->
    <link rel="apple-touch-icon" href="https://getbootstrap.com/docs/5.2/assets/img/favicons/apple-touch-icon.png" sizes="180x180">
    <link rel="icon" href="https://getbootstrap.com/docs/5.2/assets/img/favicons/favicon-32x32.png" sizes="32x32" type="image/png">
    <link rel="icon" href="https://getbootstrap.com/docs/5.2/assets/img/favicons/favicon-16x16.png" sizes="16x16" type="image/png">
    <link rel="mask-icon" href="https://getbootstrap.com/docs/5.2/assets/img/favicons/safari-pinned-tab.svg" color="#712cf9">
    <link rel="icon" href="https://getbootstrap.com/docs/5.2/assets/img/favicons/favicon.ico">
    <meta name="theme-color" content="#712cf9">

</head>

<body class="text-center">

    <?php

    if ($_GET) {
        $error = isset($_GET['error']) ? $_GET['error'] : "";;
        $message = isset($_GET['message']) ? $_GET['message'] : "";;

        if ($error == "logout") {
            echo "<div class='alert alert-success align-item-center'>Logout Successfully</div>";
        } elseif ($error == "session_expired") {
            echo "<div class='alert alert-danger align-item-center'>Access Denied (Session Expired)</div>";
        } elseif ($error != "") {
            echo "<div class='alert alert-danger align-item-center'>Unknown error happened</div>";
        }

        if ($message == "create_success") {
            echo "<div class='alert alert-success align-item-center'>Create Successfully</div>";
        };
    }

    if ($_POST) {
        $userID = trim($_POST['userID']);
        $pass = trim($_POST['password']);

        $validate = true;

        if ($userID == "") {
            echo "<div class='alert alert-danger align-item-center'>Please enter your ID</div>";
            $validate = false;
        }

        if ($pass == "") {
            echo "<div class='alert alert-danger align-item-center'>Please enter your password</div>";
            $validate = false;
        }

        if ($validate) {
            include 'config/database.php';
            try {

                // prepare select query
                $query = "SELECT userID, password, role FROM users WHERE userID = :userID";
                $stmt = $con->prepare($query);

                $stmt->bindParam(':userID', $userID);
                // execute our query
                $stmt->execute();

                $num = $stmt->rowCount();

                if ($num > 0) {
                    // store retrieved row to a variable
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);

                    extract($row);

                    if (md5($pass) == $password) {
                        switch ($role) {
                            case "Admin":
                                $_SESSION["userID"] = $userID;
                                $_SESSION["password"] = $password;
                                $_SESSION["role"] = $role;

                                header("Location: admin_users_list.php");
                                break;

                            case "User":
                                $_SESSION["userID"] = $userID;
                                $_SESSION["password"] = $password;
                                $_SESSION["role"] = $role;

                                header("Location: user_dashboard.php");
                                break;

                            case "Helpdesk":
                                $_SESSION["userID"] = $userID;
                                $_SESSION["password"] = $password;
                                $_SESSION["role"] = $role;

                                header("Location: helpdesk_dashboard.php");
                                break;

                            default:
                                echo "<div class='alert alert-danger align-item-center'>Unknown account role.</div>";
                        }
                    } else {
                        echo "<div class='alert alert-danger align-item-center mt-5'>Incorrect Password</div>";
                    }
                } else {
                    echo "<div class='alert alert-danger align-item-center mt-5'>User not found (Invalid Account)</div>";
                }
            }

            // show error
            catch (PDOException $exception) {
                die('ERROR: ' . $exception->getMessage());
            }
        }
    }
    ?>


    <main class="form-signin w-100 m-auto px-3 py-5 rounded">
        <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST">
            <i class="fa-solid fa-circle-exclamation fa-xl fs-1 mb-4" width="72" height="57"></i>
            <h1 class="h3 mb-3 fw-normal fw-bold">Please sign in</h1>

            <div class="form-floating">
                <input id="userID" type="text" class="form-control" name="userID" placeholder="userID">
                <label for="userID">userID</label>
            </div>
            <div class="form-floating">
                <input id="password" type="password" class="form-control" name="password" placeholder="Password">
                <label for="password">Password</label>
            </div>
            <input type='submit' value='Save' class='w-100 btn btn-lg btn-primary' />
            <p class="mt-5 mb-3 text-muted">&copy; 2023</p>
        </form>
    </main>

</body>

</html>
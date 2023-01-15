<!DOCTYPE HTML>
<html>

<?php
include 'check_session.php';
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Welcome</title>

    <link rel="stylesheet" href="css/welcome.css" />
    <link rel="stylesheet" href="css/styles.css" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css2?family=VT323&display=swap" rel="stylesheet">
    <!-- Fontawesome -->
    <script src="https://kit.fontawesome.com/e0e2f315c7.js" crossorigin="anonymous"></script>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js" integrity="sha384-IDwe1+LCz02ROU9k972gdyvl+AESN10+x7tBKgc9I5HFtuNz0wWnPclzo6p9vxnk" crossorigin="anonymous"></script>

    <!-- Pie chart -->
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
</head>

<body>
    <!-- NAVBAR -->
    <?php
    include "helpdesk_navbar.php";
    ?>
    <!-- NAVBAR END -->

    <?php

    $current_userID = $_SESSION["userID"];

    // include database connection
    include 'config/database.php';

    try {
        $query = "SELECT * FROM 
                (SELECT COUNT(complaintID) as total_complaint FROM complaint) as t, 
                (SELECT COUNT(status) as new_complaint FROM complaint WHERE status=:New) as n, 
                (SELECT COUNT(status) as pending_complaint FROM complaint WHERE status=:Pending) as p, 
                (SELECT COUNT(status) as KIV_complaint FROM complaint WHERE status=:KIV) as k, 
                (SELECT COUNT(status) as active_complaint FROM complaint WHERE status=:Active) as a, 
                (SELECT COUNT(status) as done_complaint FROM complaint WHERE status=:Done) as d,
                (SELECT COUNT(status) as other_complaint FROM complaint WHERE status=:Other) as o";

        $new = "New";
        $pending = "Pending";
        $KIV = "KIV";
        $done = "Done";
        $active = "Active";
        $other = "Not Relevant";

        $stmt = $con->prepare($query);
        $stmt->bindParam(':New', $new);
        $stmt->bindParam(':Pending', $pending);
        $stmt->bindParam(':KIV', $KIV);
        $stmt->bindParam(':Done', $done);
        $stmt->bindParam(':Active', $active);
        $stmt->bindParam(':Other', $other);

        $stmt->execute();

        $num = $stmt->rowCount();

        if ($num > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            extract($row);
        }

        //pending
        $query_user_pending = "SELECT title as pending_title, status FROM complaint WHERE status=:Pending OR status=:New ORDER BY complaint_date DESC";

        $stmt_user_pending = $con->prepare($query_user_pending);

        $stmt_user_pending->bindParam(':Pending', $pending);
        $stmt_user_pending->bindParam(':New', $new);

        $stmt_user_pending->execute();

        $num_user_pending = $stmt_user_pending->rowCount();

        //active
        $query_user_active = "SELECT title as active_title, status FROM complaint WHERE status=:Active ORDER BY complaint_date DESC";

        $stmt_user_active = $con->prepare($query_user_active);

        $stmt_user_active->bindParam(':Active', $active);

        $stmt_user_active->execute();

        $num_user_active = $stmt_user_active->rowCount();

        //KIV
        $query_user_KIV = "SELECT title as KIV_title, status FROM complaint WHERE status=:KIV ORDER BY complaint_date DESC";

        $stmt_user_KIV = $con->prepare($query_user_KIV);

        $stmt_user_KIV->bindParam(':KIV', $KIV);

        $stmt_user_KIV->execute();

        $num_user_KIV = $stmt_user_KIV->rowCount();
    } catch (PDOException $exception) {
        die('ERROR: ' . $exception->getMessage());
    }
    ?>

    <main class="mt-5">
        <div class="container shadow p-3 pb-5 rounded" id="background">
            <div class="monitor-wrapper position-relative bg-black p-4 mb-5">
                <div class="monitor center">
                    <p class="m-0">Welcome <?php echo $current_user ?>&nbsp;ʕ•́ᴥ•̀ʔっ♡</p>
                </div>
            </div>
            <div class="row gx-0 gx-md-5 gy-5 justify-content-center">

                <div class="col-12 col-md-3">
                    <div class="p-3 bg-white bg-opacity-75 border rounded text-center">
                        <h4 class="fw-semibold text-black text-opacity-50">Total Complaint <br> <?php echo "<p class='my-2 fs-3 text-black fw-bolder'>$total_complaint</p>" ?></h4>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="p-3 bg-white border rounded text-center">
                        <h4 class="fw-semibold text-black text-opacity-75">Total New <br> <?php echo "<p class='my-2 fs-3 text-black fw-bolder'>$new_complaint</p>" ?></h4>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="p-3 bg-white border rounded text-center">
                        <h4 class="fw-semibold text-black text-opacity-75">Total Pending <br> <?php echo "<p class='my-2 fs-3 text-black text-opacity-75 fw-bolder'>$pending_complaint</p>" ?></h4>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="p-3 bg-white border rounded text-center">
                        <h4 class="fw-semibold text-black text-opacity-75">Total KIV <br> <?php echo "<p class='my-2 fs-3 text-black fw-bolder'>$KIV_complaint</p>" ?></h4>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="p-3 bg-white border rounded text-center">
                        <h4 class="fw-semibold text-black text-opacity-75">Total Avtive <br> <?php echo "<p class='my-2 fs-3 text-black fw-bolder'>$active_complaint</p>" ?></h4>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="p-3 bg-white border rounded text-center">
                        <h4 class="fw-semibold text-black text-opacity-75">Total Done <br> <?php echo "<p class='my-2 fs-3 text-black fw-bolder'>$done_complaint</p>" ?></h4>
                    </div>
                </div>
                <div class="col-12 col-md-3">
                    <div class="p-3 bg-white border rounded text-center">
                        <h4 class="fw-semibold text-black text-opacity-75">Total Others <br> <?php echo "<p class='my-2 fs-3 text-black fw-bolder'>$other_complaint</p>" ?></h4>
                    </div>
                </div>
            </div>
            <div class="gx-0 gx-md-5 gy-5 mt-5">
                <h3 class="fw-semibold text-light">Complaints</h3>
                <div class="d-md-flex d-block align-items-center">
                    <div class="col-12 col-md-4 p-3 bg-white border rounded text-center">
                        <h4 class="fw-semibold text-black text-opacity-75">Total Done <br> <?php echo "<p class='my-2 fs-3 text-black fw-bolder'>$done_complaint</p>" ?></h4>
                    </div>
                    <div class="col-12 col-md-4 ms-auto mt-md-0 mt-5 d-flex justify-content-end me-0 me-md-5">
                    <a class="btn btn-lg btn-primary rounded text-center " href="helpdesk_complaints_list.php" role="button">Complaint List</a>
                    </div>
                </div>
            </div>
            <div class="accordion mt-5" id="accordionExample">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed fs-4 fw-semibold text-black text-opacity-75" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            Pending & New<?php echo "<b class='w-bold ms-4'>". (intval($pending_complaint) + intval($new_complaint)). "</b>" ?>
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                        <?php $stmt_user_pending->execute();
                        $index = 1;
                        while ($row_user_pending = $stmt_user_pending->fetch(PDO::FETCH_ASSOC)) {

                            extract($row_user_pending);
                            echo "<div class='accordion-body row px-4 justify-content-center mt-2'>";
                            echo "<div class='col-1 p-3 border bg-light me-2 rounded text-center'> $index </div>";
                            echo "<div class='col-10 p-3 border bg-light rounded'> $pending_title </div>";
                            echo " </div>";
                            $index++;
                        } ?>
                    </div>
                </div>
            </div>
            <div class="accordion mt-5" id="accordionExample2">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingtwo">
                        <button class="accordion-button collapsed fs-4 fw-semibold text-black text-opacity-75" type="button" data-bs-toggle="collapse" data-bs-target="#collapsetwo" aria-expanded="true" aria-controls="collapsetwo">
                            Active <?php echo "<b class='w-bold ms-4'>$active_complaint</b>" ?>
                        </button>
                    </h2>
                    <div id="collapsetwo" class="accordion-collapse collapse" aria-labelledby="headingtwo" data-bs-parent="#accordionExample2">
                        <?php $stmt_user_active->execute();
                        $index = 1;
                        while ($row_user_active = $stmt_user_active->fetch(PDO::FETCH_ASSOC)) {

                            extract($row_user_active);
                            echo "<div class='accordion-body row px-4 justify-content-center mt-2'>";
                            echo "<div class='col-1 p-3 border bg-light me-2 rounded text-center'> $index </div>";
                            echo "<div class='col-10 p-3 border bg-light rounded'> $active_title </div>";
                            echo " </div>";
                            $index++;
                        } ?>
                    </div>
                </div>
            </div>
            <div class="accordion mt-5" id="accordionExample3">
                <div class="accordion-item">
                    <h2 class="accordion-header" id="headingthree">
                        <button class="accordion-button collapsed fs-4 fw-semibold text-black text-opacity-75" type="button" data-bs-toggle="collapse" data-bs-target="#collapsethree" aria-expanded="true" aria-controls="collapsethree">
                            Keep In View <?php echo "<b class='w-bold ms-4'>$KIV_complaint</b>" ?>
                        </button>
                    </h2>
                    <div id="collapsethree" class="accordion-collapse collapse" aria-labelledby="headingthree" data-bs-parent="#accordionExample3">
                        <?php $stmt_user_KIV->execute();
                        $index = 1;
                        while ($row_user_KIV = $stmt_user_KIV->fetch(PDO::FETCH_ASSOC)) {

                            extract($row_user_KIV);
                            echo "<div class='accordion-body row px-4 justify-content-center mt-2'>";
                            echo "<div class='col-1 p-3 border bg-light me-2 rounded text-center'> $index </div>";
                            echo "<div class='col-10 p-3 border bg-light rounded'> $KIV_title </div>";
                            echo " </div>";
                            $index++;
                        } ?>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Content End -->

    <hr class="featurette-divider">

    <!-- FOOTER -->
    <footer class="container">
        <p class="float-end"><a class="text-decoration-none fw-bold" href="#">Back to top</a></p>
        <p class="text-muted fw-bold">&copy; 2022 Than Kai Xian</p>
    </footer>
    <!-- FOOTER END -->
</body>

</html>
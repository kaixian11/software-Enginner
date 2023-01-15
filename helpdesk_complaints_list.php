<!DOCTYPE HTML>
<html>

<?php
include 'check_session.php';
?>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Read Product</title>

    <link rel="stylesheet" href="css/styles.css" />

    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />

    <!-- Fontawesome -->
    <script src="https://kit.fontawesome.com/e0e2f315c7.js" crossorigin="anonymous"></script>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz9ATKxIep9tiCxS/Z9fNfEXiDAYTujMAeBAsjFuCZSmKbSSUnQlmh/jp3" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.min.js" integrity="sha384-IDwe1+LCz02ROU9k972gdyvl+AESN10+x7tBKgc9I5HFtuNz0wWnPclzo6p9vxnk" crossorigin="anonymous"></script>

</head>

<body>
    <!-- NAVBAR -->
    <?php
    include "helpdesk_navbar.php";
    ?>
    <!-- NAVBAR END -->

    <main class="mt-5">

        <!-- Content Start-->
        <!-- container -->
        <div class="container">
            <div class="page-header">
                <h1>Read Complaint</h1>
            </div>

            <!-- PHP code to read records will be here -->
            <?php
            $userID = $_SESSION['userID'];


            if ($_GET) {
                $message = isset($_GET['message']) ? $_GET['message'] : "";
                $id = isset($_GET['id']) ? $_GET['id'] : "";

                if ($message == "update_success" && $id != "") {
                    echo "<div class='alert alert-success'>Record with <b class='fs-2'> complaintID : $id </b> updated.</div>";
                } else if ($message == "update_success") {
                    echo "<div class='alert alert-success'>Record was updated.</div>";
                } else if ($message == "no_file_found") { // if it was redirected from delete.php
                    echo "<div class='alert alert-danger'>No file uploaded for this complaints.</div>";
                } else if ($message == "deleted") { // if it was redirected from delete.php
                    echo "<div class='alert alert-success'>Record was deleted.</div>";
                } else {
                    echo "<div class='alert alert-danger align-item-center'>Unknown error happened</div>";
                }
            }
            

            // include database connection
            include 'config/database.php';

            if ($_POST) {
                foreach ($_POST['complaintID'] as $newcomplaintID) {
                    $status_check = "Pending";
            
                    $query_check = "UPDATE complaint SET status=:status WHERE complaintID=:complaintID";
                    $stmt_check = $con->prepare($query_check);
    
                    $stmt_check->bindParam(':status', $status_check);
                    $stmt_check->bindParam(':complaintID', $newcomplaintID);
    
                    $stmt_check->execute();
                }    
            }
            
            ?>

            <form action="<?php echo $_SERVER["PHP_SELF"]; ?>" method="POST" enctype="multipart/form-data">

                <?php
                // select all data
                $query = "SELECT complaintID, title, complaint_date, status FROM complaint ORDER BY complaint_date DESC";
                $stmt = $con->prepare($query);

                $stmt->execute();

                // this is how to get number of rows returned
                $num = $stmt->rowCount();

                echo "<div class='table-responsive'>";
                //check if more than 0 record found
                if ($num > 0) {
                    echo "<table class='table table-hover table-bordered align-middle'>"; //start table

                    //creating our table heading
                    echo "<tr>";
                    echo "<th>Title</th>";
                    echo "<th>Complaint Date</th>";
                    echo "<th>Status</th>";
                    echo "<th>Action</th>";

                    // retrieve our table contents
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        // extract row
                        // this will make $row['firstname'] to just $firstname only
                        extract($row);

                        // creating new table row per record
                        echo "<tr>";
                        echo "<td>{$title}</td>";
                        echo "<td>{$complaint_date}</td>";
                        echo "<td style='width: 15%'>{$status}</td>";
                        echo "<td style='width: 35%'>";
                        // read one record
                        echo "<div class='d-flex align-items-center'>";
                        echo "<a href='helpdesk_complaints_read.php?id={$complaintID}' class='btn btn-info col m-auto me-lg-1'>Read</a>";
                        // we will use this links on next part of this post

                        $query_check = "SELECT file_name FROM complaint INNER JOIN files ON complaint.complaintID=files.complaintID WHERE files.complaintID = ?";
                        $stmt_check  = $con->prepare($query_check);

                        // this is the first question mark
                        $stmt_check->bindParam(1, $complaintID);

                        // execute our query
                        $stmt_check->execute();

                        // store retrieved row to a variable
                        $num_check = $stmt_check->rowCount();

                        if ($num_check > 0) {
                            echo "<a class='col-3 m-auto ms-3' href='download.php?id={$complaintID}' role='butto'><i class='fa-solid fa-file-arrow-down fa-xl'></i></a>";
                        } else {
                            echo "<i class='fa-solid fa-file-arrow-down fa-xl ms-3 text-muted col-3'></i>";
                        }

                        if ($status == "New") {
                            echo "<input class='form-check-input col-2 ms-3' type='checkbox' value='$complaintID' name='complaintID[]'>";
                        } else {
                            echo "<input class='form-check-input col-2 ms-3' type='checkbox' value='' id='flexCheckChecked' checked disabled>";
                        }
                        echo "</div>";
                        echo "</td>";
                        echo "</tr>";
                    }

                    // end table
                    echo "</table>";

                    echo "<div class='text-center'><input type='submit' value='Update New to Pending' class='btn btn-primary col-6 me-3'/></div>";
                    echo "<form>";
                } else {
                    echo "<div class='alert alert-danger'>No records found.</div>";
                }
                echo "</div>";
                ?>

        </div> <!-- end .container -->
        <!-- Content End -->

        <hr class="featurette-divider">

    </main>
    <!-- FOOTER -->
    <footer class="container">
        <p class="float-end"><a class="text-decoration-none fw-bold" href="#">Back to top</a></p>
        <p class="text-muted fw-bold">&copy; 2022 Than Kai Xian</p>
    </footer>
    <!-- FOOTER END -->
</body>

</html>
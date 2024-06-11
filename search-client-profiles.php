<?php
// Include database connection and utility functions
require_once("config.php");
include_once "functions.php";

// Start session and check user authentication
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role'];

$profile_found = false;
$profile_data = [];
$client_data = [];
$loan_data = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $national_id = trim($_POST['national_id']);

    if (!empty($national_id)) {
        // Fetch data from the clients table
        $client_query = $conn->prepare("SELECT * FROM clients WHERE national_id = ?");
        $client_query->bind_param("s", $national_id);
        if ($client_query->execute()) {
            $client_result = $client_query->get_result();
            $client_data = $client_result->fetch_assoc();
        }

        // Fetch data from the loan_info table
        $loan_query = $conn->prepare("SELECT * FROM `loan_info` WHERE national_id = ?");
        $loan_query->bind_param("s", $national_id);
        if ($loan_query->execute()) {
            $loan_result = $loan_query->get_result();
            while ($row = $loan_result->fetch_assoc()) {
                $loan_data[] = $row;
            }
        }

        if ($client_data && !empty($loan_data)) {
            $profile_found = true;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <?php require_once("head.php"); ?>
</head>
<body>
    <?php require_once("header.php"); ?>
    <?php require_once("right-sidebar.php"); ?>

    <?php
    // Include the appropriate sidebar based on the user role
    if ($_SESSION['role'] == 'admin') {
        include('left-sidebar-admin.php');
    } elseif ($_SESSION['role'] == 'manager') {
        include('left-sidebar-manager.php');
    } else {
        header("Location: login.php");
        exit();
    }
    ?>
    
    <div class="mobile-menu-overlay"></div>
    <div class="main-container">
        <div class="xs-pd-20-10 pd-ltr-20">

            <div class="search-icon-box card-box mb-30">
                <form method="post" action="">
                    <input
                        type="text"
                        class="border-radius-10"
                        name="national_id"
                        id="filter_input"
                        placeholder="Search Client Profile: Enter National ID..."
                        title="Type in a national ID"
                        required
                    />
                    <i class="search_icon dw dw-search" onclick="this.closest('form').submit();"></i>
                </form>
            </div>

            <?php if ($profile_found): ?>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-8 col-sm-12 mb-30">
                        <div class="pd-20 card-box height-100-p">
                            <h5 class="text-center h5 mb-0"><?php echo htmlspecialchars($client_data['first_name']) . ' ' . htmlspecialchars($client_data['last_name']); ?></h5>
                            <p class="text-center text-muted font-14">
                                Here are your client's details. If any details change, please contact the administrator.
                            </p>
                            <div class="profile-info-container">
                                <div class="profile-info">
                                    <h5 class="mb-20 h5 text-blue">Loan Information</h5>
                                    <ul>
                                        <?php foreach ($loan_data as $loan): ?>
                                            <li><span>Active Loan Principle:</span> <?php echo htmlspecialchars($loan['principle']); ?></li>
                                            <li><span>Paid Up EMI Payments:</span> <?php echo htmlspecialchars($loan['total_payments']); ?></li>
                                            <li><span>Loan Balance:</span> <?php echo htmlspecialchars($loan['balance']); ?></li>
                                            <li><span>Next EMI Payment:</span> <?php echo htmlspecialchars($loan['next_payment_date']); ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="profile-info">
                                    <h5 class="mb-20 h5 text-blue">Contact Information</h5>
                                    <ul>
                                        <li><span>Email Address:</span> <?php echo htmlspecialchars($client_data['email']); ?></li>
                                        <li><span>Phone Number:</span> <?php echo htmlspecialchars($client_data['phone_number']); ?></li>
                                        <li><span>County:</span> <?php echo htmlspecialchars($client_data['county']); ?></li>
                                        <li><span>Place of Residence:</span> <?php echo nl2br(htmlspecialchars($client_data['residence'])); ?></li>
                                    </ul>
                                </div>
                                <div class="profile-info">
                                    <h5 class="mb-20 h5 text-blue">KYC Information</h5>
                                    <ul>
                                        <li><span>On-Boarding Date:</span> <?php echo htmlspecialchars($client_data['onboarding_date']); ?></li>
                                        <li><span>Identification Number:</span> <?php echo htmlspecialchars($client_data['national_id']); ?></li>
                                        <li><span>Next of Kin Name:</span> <?php echo htmlspecialchars($client_data['next_of_kin_name']); ?></li>
                                        <li><span>Next of Kin Number:</span> <?php echo htmlspecialchars($client_data['next_of_kin_phone']); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="row">
                    <div class="col-md-12 col-sm-12">
                        <div class="alert alert-warning" role="alert">
                            Profile not found.
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <?php require_once("footer.php"); ?>
        </div>
    </div>

    <!-- js -->
    <script src="vendors/scripts/core.js"></script>
    <script src="vendors/scripts/script.min.js"></script>
    <script src="vendors/scripts/process.js"></script>
    <script src="vendors/scripts/layout-settings.js"></script>
    <script src="src/plugins/jQuery-Knob-master/jquery.knob.min.js"></script>
    <script src="src/plugins/highcharts-6.0.7/code/highcharts.js"></script>
    <script src="src/plugins/highcharts-6.0.7/code/highcharts-more.js"></script>
    <script src="src/plugins/jvectormap/jquery-jvectormap-2.0.3.min.js"></script>
    <script src="src/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="vendors/scripts/dashboard2.js"></script>
    <script src="src/plugins/datatables/js/jquery.dataTables.min.js"></script>
    <script src="src/plugins/datatables/js/dataTables.bootstrap4.min.js"></script>
    <script src="src/plugins/datatables/js/dataTables.responsive.min.js"></script>
    <script src="src/plugins/datatables/js/responsive.bootstrap4.min.js"></script>
    <!-- buttons for Export datatable -->
    <script src="src/plugins/datatables/js/dataTables.buttons.min.js"></script>
    <script src="src/plugins/datatables/js/buttons.bootstrap4.min.js"></script>
    <script src="src/plugins/datatables/js/buttons.print.min.js"></script>
    <script src="src/plugins/datatables/js/buttons.html5.min.js"></script>
    <script src="src/plugins/datatables/js/buttons.flash.min.js"></script>
    <script src="src/plugins/datatables/js/pdfmake.min.js"></script>
    <script src="src/plugins/datatables/js/vfs_fonts.js"></script>
    <!-- Datatable Setting js -->
    <script src="vendors/scripts/datatable-setting.js"></script>
</body>
</html>

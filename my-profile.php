<?php
// Include database connection file
require_once("config.php");
include_once "functions.php";

// Check if the user is logged in. Page can only be viewed when logged in.
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role'];
$user_email = $_SESSION['email'];

// Fetch data from the clients table
$client_query = $conn->prepare("SELECT * FROM clients WHERE email = ?");
$client_query->bind_param("s", $user_email);
$client_query->execute();
$client_result = $client_query->get_result();
$client_data = $client_result->fetch_assoc();

// Fetch data from the loan_info table
$loan_query = $conn->prepare("SELECT * FROM `loan_info` WHERE email = ?");
$loan_query->bind_param("s", $user_email);
$loan_query->execute();
$loan_result = $loan_query->get_result();
$loan_data = $loan_result->fetch_assoc();

$profile_found = $client_data && $loan_data;

if (!$client_data || !$loan_data) {
    echo "No profile data found.";
    exit();
}

?>

<!DOCTYPE html>
<html>
<?php require_once("head.php"); ?>
    <body>
        <?php require_once("header.php"); ?>
        <?php require_once("right-sidebar.php"); ?>

		<?php
		// Include the appropriate sidebar based on the user role
		if ($user_role == 'admin') {
			include('left-sidebar-admin.php');
		} elseif ($user_role == 'manager') {
			include('left-sidebar-manager.php');
		} elseif ($user_role == 'client') {
			include('left-sidebar-client.php');
		} else {
			// If the user role is neither admin, manager, nor client, redirect or show an error
			header("Location: login.php");
			exit();
		}
		?>
        
        <!-- Your content goes here -->

		<div class="mobile-menu-overlay"></div>

        <div class="main-container">
			<div class="xs-pd-20-10 pd-ltr-20">

			<div class="min-height-200px">
        <div class="page-header">
            <div class="row">
                <div class="col-md-12 col-sm-12">
                    <div class="title">
                        <h4>Profile</h4>
                    </div>
                    <nav aria-label="breadcrumb" role="navigation">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="index.html">Home</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Customer Profile
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

		<?php if ($profile_found): ?>

        <div class="row">
            <div class="col-xl-12 col-lg-12 col-md-8 col-sm-12 mb-30">
                <div class="pd-20 card-box height-100-p">
                    <h5 class="text-center h5 mb-0"><?php echo htmlspecialchars($client_data['first_name']) . ' ' . htmlspecialchars($client_data['last_name']); ?></h5>
                    <p class="text-center text-muted font-14">
                        Here are your details. If any details change please contact your loan officer.
                    </p>
                    <div class="profile-info-container">
                        <div class="profile-info">
                            <h5 class="mb-20 h5 text-blue">Loan Information</h5>
                            <ul>
                                <li>
                                    <span>Active Loan Principle:</span>
                                    <?php echo htmlspecialchars($loan_data['principle']); ?>
                                </li>
                                <li>
                                    <span>Paid Up EMI Payments:</span>
                                    <?php echo htmlspecialchars($loan_data['total_payments']); ?>
                                </li>
                                <li>
                                    <span>Loan Balance:</span>
                                    <?php echo htmlspecialchars($loan_data['balance']); ?>
                                </li>
                                <li>
                                    <span>Next EMI Payment:</span>
                                    <?php echo htmlspecialchars($loan_data['next_payment_date']); ?>
                                </li>
                            </ul>
                        </div>
                        <div class="profile-info">
                            <h5 class="mb-20 h5 text-blue">Contact Information</h5>
                            <ul>
                                <li>
                                    <span>Email Address:</span>
                                    <?php echo htmlspecialchars($client_data['email']); ?>
                                </li>
                                <li>
                                    <span>Phone Number:</span>
                                    <?php echo htmlspecialchars($client_data['phone_number']); ?>
                                </li>
                                <li>
                                    <span>County:</span>
                                    <?php echo htmlspecialchars($client_data['county']); ?>
                                </li>
                                <li>
                                    <span>Place of Residence:</span>
                                    <?php echo nl2br(htmlspecialchars($client_data['residence'])); ?>
                                </li>
                            </ul>
                        </div>
                        <div class="profile-info">
                            <h5 class="mb-20 h5 text-blue">KYC Information</h5>
                            <ul>
                                <li>
                                    <span>On-Boarding Date:</span>
                                    <?php echo htmlspecialchars($client_data['onboarding_date']); ?>
                                </li>
                                <li>
                                    <span>Identification Number:</span>
                                    <?php echo htmlspecialchars($client_data['national_id']); ?>
                                </li>
                                <li>
                                    <span>Next of Kin Name:</span>
                                    <?php echo htmlspecialchars($client_data['next_of_kin_name']); ?>
                                </li>
                                <li>
                                    <span>Next of Kin Number:</span>
                                    <?php echo htmlspecialchars($client_data['next_of_kin_phone']); ?>
                                </li>
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
                    No profile data found.
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

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

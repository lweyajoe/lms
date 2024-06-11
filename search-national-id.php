<?php
// Include database connection file
require_once("config.php");
include_once "functions.php";

// Start session and check user authentication
session_start();
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'manager'])) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role'];

// Initialize variables
$loans_found = false;
$national_id = '';
$loan_profiles = [];

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $national_id = $_POST['national_id'];

    // Retrieve loans by national ID
    $loan_query = $conn->prepare("SELECT * FROM loan_info WHERE national_id = ?");
    $loan_query->bind_param("s", $national_id);
    if ($loan_query->execute()) {
        $loan_result = $loan_query->get_result();
        while ($loan_info = $loan_result->fetch_assoc()) {
            $loan_status_info = getLoanStatusInfo($conn, $loan_info);

            $loan_details_info = [
                'ID/Passport Number' => $loan_info['national_id'],
                'Phone Number' => $loan_info['phone_number'],
                'Requested Loan' => number_format($loan_info['requested_amount'], 2),
                'Loan Purpose' => $loan_info['loan_purpose'],
                'Duration' => $loan_info['duration'],
                'Duration period in' => $loan_info['duration_period'],
                'Date of Applying' => $loan_info['date_applied'],
                'Interest Rate' => $loan_info['interest_rate'],
                'Interest Rate Period' => $loan_info['interest_rate_period'],
            ];

            $loan_security_info = [
                'Collateral Name' => $loan_info['collateral_name'],
                'Collateral Value' => number_format($loan_info['collateral_value'], 2),
                'Collateral Pic 1' => $loan_info['collateral_pic1'],
                'Collateral Pic 2' => $loan_info['collateral_pic2'],
                'Guarantor 1 Name' => $loan_info['guarantor1_name'],
                'Guarantor 1 Phone Number' => $loan_info['guarantor1_phone'],
                'Guarantor 2 Name' => $loan_info['guarantor2_name'],
                'Guarantor 2 Phone Number' => $loan_info['guarantor2_phone'],
                'Onboarding Officer' => $loan_info['onboarding_officer'],
            ];

            $loan_profiles[] = [
                'loan_info' => $loan_info,
                'loan_status_info' => $loan_status_info,
                'loan_details_info' => $loan_details_info,
                'loan_security_info' => $loan_security_info,
            ];

            $loans_found = true;
        }
    }
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
                                    <li class="breadcrumb-item"><a href="index.html">Home</a></li>
                                    <li class="breadcrumb-item active" aria-current="page">Loan Profiles</li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                </div>

                <div class="search-icon-box card-box mb-30">
                    <form method="post" action="">
                        <input
                            type="text"
                            class="border-radius-10"
                            name="national_id"
                            id="filter_input"
                            placeholder="Search Loan Profiles: Enter National ID..."
                            title="Type in the national ID"
                            required
                        />
                        <i class="search_icon dw dw-search" onclick="this.closest('form').submit();"></i>
                    </form>
                </div>

                <?php if ($loans_found): ?>
                    <?php foreach ($loan_profiles as $profile): ?>
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-8 col-sm-12 mb-30">
                                <div class="pd-20 card-box height-100-p">
                                    <h5 class="text-center h5 mb-0">LOAN ID: <?php echo htmlspecialchars($profile['loan_info']['loan_id']); ?></h5>
                                    <p class="text-center text-muted font-14">Here are the specifics of this loan.</p>
                                    <div class="profile-info-container" style="display: flex; justify-content: space-between; gap: 20px;">
                                        <!-- Loan Status Info -->
                                        <div class="profile-info" style="width: 30%">
                                            <h5 class="mb-20 h5 text-blue">Loan Status Info</h5>
                                            <ul>
                                                <?php foreach ($profile['loan_status_info'] as $label => $value): ?>
                                                    <li><span><?php echo htmlspecialchars($label); ?>:</span> <?php echo htmlspecialchars($value); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <!-- Loan Details Info -->
                                        <div class="profile-info" style="width: 30%">
                                            <h5 class="mb-20 h5 text-blue">Loan Details Info</h5>
                                            <ul>
                                                <?php foreach ($profile['loan_details_info'] as $label => $value): ?>
                                                    <li><span><?php echo htmlspecialchars($label); ?>:</span> <?php echo htmlspecialchars($value); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <!-- Loan Security Info -->
                                        <div class="profile-info" style="width: 30%">
                                            <h5 class="mb-20 h5 text-blue">Loan Security Info</h5>
                                            <ul>
                                                <?php foreach ($profile['loan_security_info'] as $label => $value): ?>
                                                    <li><span><?php echo htmlspecialchars($label); ?>:</span> <?php echo htmlspecialchars($value); ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php if ($_SERVER['REQUEST_METHOD'] == 'POST'): ?>
                        <div class="alert alert-danger" role="alert">
                            No loans found for the given National ID.
                        </div>
                    <?php endif; ?>
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

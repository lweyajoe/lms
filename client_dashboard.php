<?php
session_start();
require_once("config.php");
require_once("functions.php");

// Check if the user is logged in and is a client
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'client') {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role'];
$clientEmail = $_SESSION['email'];

// Fetch client data
$sqlClient = "SELECT * FROM clients WHERE email = ?";
$stmtClient = $conn->prepare($sqlClient);
$stmtClient->bind_param("s", $clientEmail);
$stmtClient->execute();
$clientData = $stmtClient->get_result()->fetch_assoc();

// Fetch active loan data
$sqlLoan = "SELECT * FROM active_loans WHERE client_id = ?";
$stmtLoan = $conn->prepare($sqlLoan);
$stmtLoan->bind_param("s", $clientData['client_id']);
$stmtLoan->execute();
$loanData = $stmtLoan->get_result()->fetch_assoc();

// Calculate EI
$ei = calculateEMI(
    $loanData['requested_amount'], 
    $loanData['interest_rate'], 
    $loanData['duration'], 
    $loanData['interest_rate_period'], 
    $loanData['duration_period']
);

// Fetch recent payments
$sqlPayments = "SELECT * FROM payments WHERE loan_id = ? ORDER BY payment_date DESC LIMIT 5";
$stmtPayments = $conn->prepare($sqlPayments);
$stmtPayments->bind_param("s", $loanData['loan_id']);
$stmtPayments->execute();
$paymentsData = $stmtPayments->get_result();

// Calculate total paid amount
$sqlTotalPaid = "SELECT SUM(amount) AS total_paid FROM payments WHERE loan_id = ?";
$stmtTotalPaid = $conn->prepare($sqlTotalPaid);
$stmtTotalPaid->bind_param("s", $loanData['loan_id']);
$stmtTotalPaid->execute();
$totalPaidData = $stmtTotalPaid->get_result()->fetch_assoc();
$totalPaid = $totalPaidData['total_paid'];

// Calculate total due amount (principal + interest over the duration)
$totalInterest = ($loanData['requested_amount'] * $loanData['interest_rate'] / 100) * $loanData['duration'];
$totalDue = $loanData['requested_amount'] + $totalInterest;

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

			<div class="title pb-20">
                <h2 class="h3 mb-0">Client Dashboard</h2>
            </div>

            <!-- Personal Information -->
            <div class="row pb-20">
                <div class="col-md-4 mb-20">
                    <div class="card-box height-100-p">
                        <div class="profile-photo">
                            <img src="<?php echo htmlspecialchars($clientData['id_photo_front']); ?>" alt="Profile Photo" class="avatar-photo">
                        </div>
                        <h5 class="text-center"><?php echo htmlspecialchars($clientData['first_name'] . " " . $clientData['last_name']); ?></h5>
                        <p class="text-center text-muted"><?php echo htmlspecialchars($clientData['email']); ?></p>
                        <p class="text-center text-muted"><?php echo htmlspecialchars($clientData['phone_number']); ?></p>
                    </div>
                </div>

                <!-- Account Summary -->
                <div class="col-md-8 mb-20">
                    <div class="card-box height-100-p">
                        <h4 class="text-blue h4">Account Summary</h4>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Current Loan ID: </strong><?php echo htmlspecialchars($loanData['loan_id']); ?></p>
                                <p><strong>Total Due: </strong><?php echo number_format($totalDue, 2); ?></p>
                                <p><strong>Total Paid: </strong><?php echo number_format($totalPaid, 2); ?></p>
                                <p><strong>Remaining Balance: </strong><?php echo number_format($totalDue - $totalPaid, 2); ?></p>
                                <p><strong>EI: </strong><?php echo number_format($ei, 2); ?></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Loan Amount: </strong><?php echo number_format($loanData['requested_amount'], 2); ?></p>
                                <p><strong>Interest Rate: </strong><?php echo htmlspecialchars($loanData['interest_rate']); ?>%</p>
                                <p><strong>Loan Purpose: </strong><?php echo htmlspecialchars($loanData['loan_purpose']); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="row pb-20">
                <div class="col-md-12 mb-20">
                    <div class="card-box">
                        <h4 class="text-blue h4">Recent Transactions</h4>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Amount</th>
                                    <th>Reference</th>
                                    <th>Payment Mode</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($payment = $paymentsData->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($payment['payment_date']); ?></td>
                                        <td><?php echo number_format($payment['amount'], 2); ?></td>
                                        <td><?php echo htmlspecialchars($payment['transaction_reference']); ?></td>
                                        <td><?php echo htmlspecialchars($payment['payment_mode']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Financial Tools -->
            <div class="row pb-20">
                <div class="col-md-12 mb-20">
                    <div class="card-box">
                        <h4 class="text-blue h4">Financial Tools</h4>
                        <a href="loan_calculator.php" class="btn btn-primary">Loan Calculator</a>
                    </div>
                </div>
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

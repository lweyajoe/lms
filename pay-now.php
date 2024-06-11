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


// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $loanId = htmlspecialchars($_POST["loan_id"]);
    $nationalId = htmlspecialchars($_POST["national_id"]);
    $paymentMode = htmlspecialchars($_POST["mode_of_payment"]);
    $paymentDate = htmlspecialchars($_POST["date_paid"]);
    $amount = htmlspecialchars($_POST["amount"]);
    $transactionRef = htmlspecialchars($_POST["transaction_reference"]);

    // Validate that loan_id and national_id exist in loan_info table
    $stmt = $conn->prepare("SELECT * FROM loan_info WHERE loan_id = ? AND national_id = ?");
    $stmt->bind_param("ss", $loanId, $nationalId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Insert payment record into the database
        $stmt = $conn->prepare("INSERT INTO payments (loan_id, national_id, transaction_reference, payment_mode, payment_date, amount) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssd", $loanId, $nationalId, $transactionRef, $paymentMode, $paymentDate, $amount);

        if ($stmt->execute()) {
            echo '<script>alert("Payment recorded successfully!");</script>';
        } else {
            echo '<script>alert("Error: ' . $stmt->error . '");</script>';
        }
    } else {
        echo '<script>alert("Error: Loan ID or National ID does not exist.");</script>';
    }

    $stmt->close();
    $conn->close();
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

			<div class="page-header">
						<div class="row">
							<div class="col-md-6 col-sm-12">
								<div class="title">
									<h4>Payments</h4>
								</div>
								<nav aria-label="breadcrumb" role="navigation">
									<ol class="breadcrumb">
										<li class="breadcrumb-item">
											<a href="">Home</a>
										</li>
										<li class="breadcrumb-item active" aria-current="page">
											Payments
										</li>
									</ol>
								</nav>
							</div>
						</div>
					</div>

					<!-- Form grid Start -->
					<div class="pd-20 card-box mb-30">
        <div class="clearfix">
            <div class="pull-left">
                <h4 class="text-blue h4">Payment Form</h4>
                <p class="mb-30">All payments recorded here</p>
            </div>
        </div>
        <form method="POST" action="pay-now.php">
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Loan ID</label>
                        <input type="text" class="form-control" name="loan_id" required />
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>National ID</label>
                        <input type="text" class="form-control" name="national_id" required />
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Payment Mode</label>
                        <select class="custom-select col-12" name="mode_of_payment" required>
                            <option selected disabled>Choose...</option>
                            <option value="MPESA">MPESA</option>
                            <option value="Cash">Cash</option>
                            <option value="BANK">BANK</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Payment Date</label>
                        <input type="text" class="form-control date-picker" placeholder="Select Date" name="date_paid" required />
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Amount</label>
                        <input type="text" class="form-control" name="amount" required />
                    </div>
                </div>
                <div class="col-md-4 col-sm-12">
                    <div class="form-group">
                        <label>Transaction Ref:</label>
                        <input type="text" class="form-control" name="transaction_reference" required />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 col-sm-12 text-right">
                    <div class="input-group mb-0">
                        <input class="btn btn-primary btn-lg btn-block" type="submit" value="RECORD PAYMENT">
                    </div>
                </div>
            </div>
        </form>
    </div>
					<!-- Form grid End -->

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

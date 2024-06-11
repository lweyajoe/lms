<?php
// Include database connection file
require_once("config.php");
include_once "functions.php";

// Check if the user is logged in as admin
session_start();
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'manager')) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role'];

// Handle loan approval or rejection
if ($_SERVER["REQUEST_METHOD"] == "POST" && (isset($_POST['approve_loan_id']) || isset($_POST['reject_loan_id']))) {
    if (isset($_POST['approve_loan_id'])) {
        $loanId = $_POST['approve_loan_id'];
        $targetTable = "active_loans";
        $loanStatus = 'approved';
    } elseif (isset($_POST['reject_loan_id'])) {
        $loanId = $_POST['reject_loan_id'];
        $targetTable = "rejected_loans";
        $loanStatus = 'rejected';
    }

    // Fetch loan details
    $loanQuery = "SELECT * FROM loan_applications WHERE loan_id = ?";
    $stmtLoan = $conn->prepare($loanQuery);
    $stmtLoan->bind_param("s", $loanId);
    $stmtLoan->execute();
    $loanResult = $stmtLoan->get_result();
    $loanData = $loanResult->fetch_assoc();

    if ($loanData) {
        // Transfer loan details to the target table
        $insertQuery = "INSERT INTO $targetTable (loan_id, client_id, national_id, phone_number, requested_amount, loan_purpose, duration, duration_period, date_applied, interest_rate, interest_rate_period, collateral_name, collateral_value, collateral_pic1, collateral_pic2, guarantor1_name, guarantor1_phone, guarantor2_name, guarantor2_phone, loan_status, onboarding_officer)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($insertQuery);
        $stmtInsert->bind_param("ssssdsisssssssssssss",
            $loanData['loan_id'],
            $loanData['client_id'],
            $loanData['national_id'],
            $loanData['phone_number'],
            $loanData['requested_amount'],
            $loanData['loan_purpose'],
            $loanData['duration'],
            $loanData['duration_period'],
            $loanData['date_applied'],
            $loanData['interest_rate'],
            $loanData['interest_rate_period'],
            $loanData['collateral_name'],
            $loanData['collateral_value'],
            $loanData['collateral_pic1'],
            $loanData['collateral_pic2'],
            $loanData['guarantor1_name'],
            $loanData['guarantor1_phone'],
            $loanData['guarantor2_name'],
            $loanData['guarantor2_phone'],
            $loanStatus,
            $loanData['onboarding_officer']
        );

        if ($stmtInsert->execute()) {
            // Delete from loan applications table
            $deleteLoanQuery = "DELETE FROM loan_applications WHERE loan_id = ?";
            $stmtDeleteLoan = $conn->prepare($deleteLoanQuery);
            $stmtDeleteLoan->bind_param("s", $loanId);
            $stmtDeleteLoan->execute();
            $stmtDeleteLoan->close();

            $actionMessage = "Loan " . ($loanStatus == 'approved' ? "approved" : "rejected") . " successfully.";
        } else {
            $actionError = "Error: " . $stmtInsert->error;
        }
        $stmtInsert->close();
    }
    $stmtLoan->close();
}

// Fetch all loan applications
$loansQuery = "SELECT l.*, c.first_name, c.last_name, c.email, c.phone_number AS client_phone, c.county
               FROM loan_applications l
               JOIN clients c ON l.client_id = c.client_id";
$loansResult = $conn->query($loansQuery);
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
                                <a href="">Home</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">
                                Applied Loans Profile
                            </li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <?php if (isset($actionMessage)): ?>
            <p style="color: green;"><?php echo $actionMessage; ?></p>
        <?php endif; ?>

        <?php if (isset($actionError)): ?>
            <p style="color: red;"><?php echo $actionError; ?></p>
        <?php endif; ?>

        <div class="row">
            <?php while ($loan = $loansResult->fetch_assoc()): ?>
                <div class="col-xl-12 col-lg-12 col-md-8 col-sm-12 mb-30">
                    <div class="pd-20 card-box height-100-p">
                        <h5 class="text-center h5 mb-0">Loan ID: <?php echo $loan['loan_id']; ?></h5>
                        <p class="text-center text-muted font-14">
                            Here are the loan application details. Please approve, but if any details are not satisfactory, reject the loan and proceed to the next one.
                        </p>
                        <div class="profile-info-container" style="display: flex; justify-content: space-between; gap: 20px;">
                            <div class="profile-info" style="width: 20%">
                                <h5 class="mb-20 h5 text-blue">Contact Information</h5>
                                <ul>
                                    <li><span>Name:</span> <?php echo $loan['first_name'] . ' ' . $loan['last_name']; ?></li>
                                    <li><span>Email Address:</span> <?php echo $loan['email']; ?></li>
                                    <li><span>Phone Number:</span> <?php echo $loan['client_phone']; ?></li>
                                    <li><span>County:</span> <?php echo $loan['county']; ?></li>
                                </ul>
                            </div>
                            <div class="profile-info" style="width: 20%">
                                <h5 class="mb-20 h5 text-blue">Loan In Numbers</h5>
                                <ul>
                                    <li><span>National ID:</span> <?php echo $loan['national_id']; ?></li>
                                    <li><span>Requested Amount:</span> <?php echo $loan['requested_amount']; ?></li>
                                    <li><span>Duration:</span> <?php echo $loan['duration'] . ' ' . $loan['duration_period']; ?></li>
                                    <li><span>Interest:</span> <?php echo $loan['interest_rate'] . ' ' . $loan['interest_rate_period']; ?></li>
                                </ul>
                            </div>
                            <div class="profile-info" style="width: 20%">
                                <h5 class="mb-20 h5 text-blue">Loan Profile Info</h5>
                                <ul>
                                    <li><span>Date of Applying:</span> <?php echo $loan['date_applied']; ?></li>
                                    <li><span>Loan Onboarding Officer:</span> <?php echo $loan['onboarding_officer']; ?></li>
                                    <li><span>Purpose:</span> <?php echo $loan['loan_purpose']; ?></li>
                                </ul>
                            </div>
                            <div class="profile-info" style="width: 20%">
                                <h5 class="mb-20 h5 text-blue">Security Information</h5>
                                <ul>
                                    <li><span>Collateral Name:</span> <?php echo $loan['collateral_name']; ?></li>
                                    <li><span>Collateral Value:</span> <?php echo $loan['collateral_value']; ?></li>
                                    <li><span>Collateral Pic 1:</span> <?php echo $loan['collateral_pic1']; ?></li>
                                    <li><span>Collateral Pic 2:</span> <?php echo $loan['collateral_pic2']; ?></li>
                                </ul>
                            </div>
                            <div class="profile-info" style="width: 20%">
                                <h5 class="mb-20 h5 text-blue">Guarantor Information</h5>
                                <ul>
                                    <li><span>Guarantor 1 Name:</span> <?php echo $loan['guarantor1_name']; ?></li>
                                    <li><span>Guarantor 1 Number:</span> <?php echo $loan['guarantor1_phone']; ?></li>
                                    <li><span>Guarantor 2 Name:</span> <?php echo $loan['guarantor2_name']; ?></li>
                                    <li><span>Guarantor 2 Number:</span> <?php echo $loan['guarantor2_phone']; ?></li>
                                </ul>
                            </div>
                        </div>
                        <form method="post" class="row" style="display: flex; justify-content: space-between;">
                            <input type="hidden" name="loan_id" value="<?php echo $loan['loan_id']; ?>">
                            <div class="form-group mb-0">
                                <button type="submit" name="reject_loan_id" value="<?php echo $loan['loan_id']; ?>" class="btn btn-primary">Reject</button>
                            </div>
                            <div class="form-group mb-0">
                                <button type="submit" name="approve_loan_id" value="<?php echo $loan['loan_id']; ?>" class="btn btn-primary">Approve</button>
                            </div>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
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
    </body>
</html>

<?php
// Close the database connection
$conn->close();
?>


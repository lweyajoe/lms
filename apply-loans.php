<?php
// Include database connection file
require_once("config.php");
include_once "functions.php";

// Check if the user is logged in as admin or manager

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'admin' && $_SESSION['role'] != 'manager')) {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role'];

// Form submission handling
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if client exists in the database
    $clientNationalId = $_POST['national_id']; // Assuming this is the client's ID/Passport number
    $checkClientQuery = "SELECT client_id, email FROM clients WHERE national_id = ?";
    $stmt = $conn->prepare($checkClientQuery);
    $stmt->bind_param("s", $clientNationalId);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows == 0) {
        // Client does not exist, show error message and prompt to onboard/register client first
        $clientExistError = "Client does not exist. Please onboard/register the client first.";
    } else {
        // Client exists, fetch client_id and email
        $stmt->bind_result($clientId, $email);
        $stmt->fetch();

        // Autofill onboarding officer
        $onboardingOfficer = getOnboardingOfficer($conn);

        // Insert loan application data into database
        $insertQuery = "INSERT INTO loan_applications (national_id, client_id, phone_number, requested_loan_amount, loan_purpose, duration, duration_period, date_applied, interest_rate, interest_rate_period, collateral_name, collateral_value, collateral_pic1, collateral_pic2, guarantor1_name, guarantor1_phone, guarantor2_name, guarantor2_phone, loan_status, onboarding_officer)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtInsert = $conn->prepare($insertQuery);
        $stmtInsert->bind_param("sissssssssssssssssss",
            $clientNationalId, 
            $clientId, 
            $_POST['phone_number'], 
            $_POST['requested_loan_amount'],
			$_POST['loan_purpose'], 
            $_POST['duration'], 
            $_POST['duration_period'], 
            $_POST['date_applied'], 
            $_POST['interest_rate'], 
            $_POST['interest_rate_period'], 
            $_POST['collateral_name'], 
            $_POST['collateral_value'], 
            $_POST['collateral_pic1'], 
            $_POST['collateral_pic2'], 
            $_POST['guarantor1_name'], 
            $_POST['guarantor1_phone'], 
            $_POST['guarantor2_name'], 
            $_POST['guarantor2_phone'], 
            $_POST['loan_status'], 
            $onboardingOfficer
        );

        if ($stmtInsert->execute()) {
            // Send email to client with details of the loan
            $to = $email;
            $subject = "Loan Status: Pending";
            $message = "Dear Client,\n\n";
            $message .= "Thank you for applying for a loan with us. Below are the details:\n";
            $message .= "Loan Amount: " . $_POST['requested_loan_amount'] . "\n";
            $message .= "Manager: " . $onboardingOfficer . "\n";
            $message .= "Please wait for approval. If approved, your loan will be sent to " . $_POST['phone_number'] . ".\n\n";
            $message .= "Best regards,\nYour Company Name";
            $headers = "From: yourcompany@example.com";
            mail($to, $subject, $message, $headers);

            // Success message
            $insertQuerySuccess = "Loan application submitted successfully.";
        } else {
            // Error message
            $insertQueryError = "Error: " . $stmtInsert->error;
        }
        $stmtInsert->close();
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
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
    <div class="main-container">
        <div class="xs-pd-20-10 pd-ltr-20">
            <!-- HTML content here -->
            <!-- Modify the HTML form to include PHP where necessary -->
            <div class="page-header">
					<div class="row">
						<div class="col-md-6 col-sm-12">
							<div class="title">
								<h4>Loan Application</h4>
							</div>
							<nav aria-label="breadcrumb" role="navigation">
								<ol class="breadcrumb">
									<li class="breadcrumb-item"><a href="index.html">Home</a></li>
									<li class="breadcrumb-item active" aria-current="page">
										New Loan
									</li>
								</ol>
							</nav>
						</div>
						<div class="col-md-6 col-sm-12 text-right">
						</div>
					</div>
				</div>

                <div class="pd-20 card-box mb-30">
					<div class="clearfix">
						<h4 class="text-blue h4">Apply For New Loan</h4>
						<p class="mb-30">All fields required: If not applicable enter "NA" in caps lock</p>
					</div>
					<div class="wizard-content">
                    <form class="tab-wizard wizard-circle wizard" method="post" action="<?php ($_SERVER["REQUEST_METHOD"]); ?>">
							<h5>Loan Details Info</h5>
							<section>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label>ID/Passport Number:</label>
											<input type="text" class="form-control" name="national_id" />
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Phone Number:</label>
											<input type="text" class="form-control" name="phone_number" />
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Requested Loan:</label>
											<input type="text" class="form-control" name="requested_loan_amount" />
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Loan Purpose:</label>
											<input type="text" class="form-control" name="loan_purpose" />
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Duration (Number only):</label>
											<select class="custom-select col-12" name="duration">
												<option selected="">Choose...</option>
												<option value="1">1</option>
												<option value="2">2</option>
												<option value="3">3</option>
												<option value="4">4</option>
												<option value="5">5</option>
												<option value="6">6</option>
												<option value="7">7</option>
												<option value="8">8</option>
												<option value="9">9</option>
												<option value="10">10</option>
												<option value="11">11</option>
												<option value="12">12</option>
											</select>
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Duration period in:</label>
											<select class="custom-select col-12" name="duration_period">
												<option selected="">Choose...</option>
												<option value="1">Week(s)</option>
												<option value="2">Month(s)</option>
												<option value="3">Year(s)</option>
											</select>
										</div>
									</div>
								</div>
								<div class="row">
									<div class="col-md-4">
										<div class="form-group">
											<label>Date of Applying (Enter today's date) :</label>
											<input
												type="text"
												class="form-control date-picker"
												placeholder="Select Date"
												name="date_applied"
											/>
										</div>
									</div>

									<div class="col-md-4">
										<div class="form-group">
											<label>Interest Rate :</label>
											<input type="text" class="form-control" name="interest_rate" />
										</div>
									</div>
									<div class="col-md-4">
										<div class="form-group">
											<label>Interest Rate Period:</label>
											<select class="custom-select col-12" name="interest_rate_period">
												<option selected="">Choose...</option>
												<option value="1">Week</option>
												<option value="2">Month</option>
												<option value="3">Year</option>
											</select>
										</div>
									</div>

								</div>
							</section>
							<!-- Step 2 -->
							<h5>Security Info</h5>
							<section>
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label>Collateral Name :</label>
											<input type="text" class="form-control" name="collateral_name" />
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Collateral Value :</label>
											<input type="text" class="form-control" name="collateral_value" />
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Attach Collateral Pic 1 :</label>
											<input type="file" class="form-control-file form-control height-auto" name="collateral_pic1" />
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Attach Collateral Pic 2 :</label>
											<input type="file" class="form-control-file form-control height-auto" name="collateral_pic2" />
										</div>
									</div>

								</div>
								<div class="row">
									<div class="col-md-3">
										<div class="form-group">
											<label>Guarantor 1 Name :</label>
											<input type="text" class="form-control" name="guarantor1_name" />
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Guarantor 1 Phone Number :</label>
											<input type="text" class="form-control" name="guarantor1_phone" />
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Guarantor 2 Name :</label>
											<input type="text" class="form-control" name="guarantor2_name" />
										</div>
									</div>
									<div class="col-md-3">
										<div class="form-group">
											<label>Guarantor 2 Phone Number :</label>
											<input type="text" class="form-control" name="guarantor2_phone" />
										</div>
									</div>
								</div>
							</section>

							<!-- Step 3 -->
							<h5>Loan Status Info</h5>
							<section>
								<div class="row">
									<div class="col-md-6">
										<div class="form-group">
											<label>Loan Status :</label>
											<select class="custom-select col-12" name="loan_status">
												<option selected="">Choose...</option>
												<option value="1">Pending</option>
											</select>
										</div>
									</div>
									<div class="col-md-6">
										<div class="form-group">
											<label>OnBoarding Officer</label>
											<input class="form-control" type="text" readonly value="<?php echo $onboardingOfficer; ?>" name="onboarding_officer" />
										</div>
									</div>
									</div>
							</section>

							<!-- Step 4 -->
							<h5>Done!!</h5>
							<section>
								<div class="row">
								<?php if (isset($clientExistError)) { ?> <!-- Display error message if client does not exist -->
                            <p class="text-danger"><?php echo $clientExistError; ?></p>
                        <?php } else if (isset($insertQuerySuccess)) { ?> <!-- Display success message if loan is applied successfully -->
                            <p class="text-danger"><?php echo $insertQuerySuccess; ?></p>
                        <?php } else if (isset($insertQueryError)) { ?> <!-- Display error message if loan is not applied successfully -->
                            <p class="text-danger"><?php echo $insertQueryError; ?></p>
							<?php } ?>
								</div>
								<button type="submit" class="btn btn-primary">Submit</button>
							</section>
						</form>
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
		<script src="src/plugins/jquery-steps/jquery.steps.js"></script>
		<script src="vendors/scripts/steps-setting.js"></script>
</body>
</html>

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

function calculateEMI($principal, $interest_rate, $duration, $interest_rate_period, $duration_period) {
    if ($interest_rate_period === 'Yearly') {
        $interest = $principal * ($interest_rate / 100);
    } elseif ($interest_rate_period === 'Monthly') {
        $interest = $principal * ($interest_rate / 100);
    } elseif ($interest_rate_period === 'Weekly') {
        $interest = $principal * ($interest_rate / 100);
    }

    if ($duration_period === 'Year') {
        $emi = ($principal / $duration) + $interest;
    } elseif ($duration_period === 'Month') {
        $emi = ($principal / $duration) + $interest;
    } elseif ($duration_period === 'Week') {
        $emi = ($principal / $duration) + $interest;
    }

    return $emi;
}

$sql = "
    SELECT
        loan_id,
        requested_amount AS principal,
        interest_rate,
        duration,
        duration_period,
        interest_rate_period,
        created_at AS activation_date
    FROM
        loan_info
    WHERE
        loan_status = 'Active';
";

$result = $conn->query($sql);
$loans = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $loan_id = $row['loan_id'];
        $emi = calculateEMI($row['principal'], $row['interest_rate'], $row['duration'], $row['interest_rate_period'], $row['duration_period']);
        
        // Fetch total payments made for this loan
        $payments_sql = "SELECT SUM(amount) as total_payments FROM payments WHERE loan_id = '$loan_id'";
        $payments_result = $conn->query($payments_sql);
        $payments_row = $payments_result->fetch_assoc();
        $total_payments = $payments_row['total_payments'] ? $payments_row['total_payments'] : 0;

        // Calculate the number of periods based on duration and duration_period
        $num_periods = $row['duration'];
        
        // Calculate the total EMIs
        $total_emis = $emi * $num_periods;

        // Calculate EMIs due to date
        $activation_date = new DateTime($row['activation_date']);
        $now = new DateTime();
        $interval = $now->diff($activation_date);
        $periods_due = 0;

        if ($row['duration_period'] === 'Year') {
            $periods_due = $interval->y;
        } elseif ($row['duration_period'] === 'Month') {
            $periods_due = $interval->m + ($interval->y * 12);
        } elseif ($row['duration_period'] === 'Week') {
            $periods_due = floor($interval->days / 7);
        }

        $emis_due_to_date = $emi * $periods_due;

        // Calculate balance
        $balance = $total_emis - $total_payments;

        $loans[] = [
            'loan_id' => $loan_id,
            'emi_per_period' => $emi,
            'num_periods' => $num_periods,
            'total_emis' => $total_emis,
            'emis_due_to_date' => $emis_due_to_date,
            'total_payments' => $total_payments,
            'balance' => $balance,
            'activation_date' => $row['activation_date']
        ];
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
            <div class="col-md-6 col-sm-12">
                <div class="title">
                    <h4>DataTable</h4>
                </div>
                <nav aria-label="breadcrumb" role="navigation">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="index.html">Home</a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            DataTable
                        </li>
                    </ol>
                </nav>
            </div>
            <div class="col-md-6 col-sm-12 text-right">
                <div class="dropdown">
                    <a class="btn btn-primary dropdown-toggle" href="#" role="button" data-toggle="dropdown">
                        January 2018
                    </a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item" href="#">Export List</a>
                        <a class="dropdown-item" href="#">Policies</a>
                        <a class="dropdown-item" href="#">View Assets</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Simple Datatable start -->
    <div class="card-box mb-30">
        <div class="pd-20">
            <h4 class="text-blue h4">Data Table Simple</h4>
        </div>
        <div class="pb-20">
            <table class="data-table table stripe hover nowrap">
                <thead>
                    <tr>
                        <th class="table-plus datatable-nosort">Loan ID</th>
                        <th>EI per period</th>
                        <th>No. of periods</th>
                        <th>Total EIs</th>
                        <th>EIs due to date</th>
                        <th>Total Payments</th>
                        <th>Balance</th>
                        <th>Activation Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($loans as $loan): ?>
                        <tr>
                            <td class="table-plus"><?php echo htmlspecialchars($loan['loan_id']); ?></td>
                            <td><?php echo number_format($loan['emi_per_period'], 2); ?></td>
                            <td><?php echo htmlspecialchars($loan['num_periods']); ?></td>
                            <td><?php echo number_format($loan['total_emis'], 2); ?></td>
                            <td><?php echo number_format($loan['emis_due_to_date'], 2); ?></td>
                            <td><?php echo number_format($loan['total_payments'], 2); ?></td>
                            <td><?php echo number_format($loan['balance'], 2); ?></td>
                            <td><?php echo htmlspecialchars($loan['activation_date']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <!-- Simple Datatable End -->
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

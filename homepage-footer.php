<?php
// Include database connection file
require_once("config.php");
include_once "functions.php";

// Check if the user is logged in as admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

$user_role = $_SESSION['role'];

// Fetch footer content from the database
$stmt = $conn->prepare("SELECT * FROM homepage_footer");
$stmt->execute();
$footerData = $stmt->get_result()->fetch_assoc();

// Handle form submission for updating footer content
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $address = htmlspecialchars($_POST['address']);
    $phone = htmlspecialchars($_POST['phone']);
    $email = htmlspecialchars($_POST['email']);
    $twitter = htmlspecialchars($_POST['twitter']);
    $facebook = htmlspecialchars($_POST['facebook']);
    $youtube = htmlspecialchars($_POST['youtube']);
    $linkedin = htmlspecialchars($_POST['linkedin']);

    $stmt = $conn->prepare("UPDATE homepage_footer SET address = ?, phone = ?, email = ?, twitter = ?, facebook = ?, youtube = ?, linkedin = ?");
    $stmt->bind_param("sssssss", $address, $phone, $email, $twitter, $facebook, $youtube, $linkedin);

    if ($stmt->execute()) {
        echo '<script>alert("Footer content updated successfully!");</script>';
    } else {
        echo '<script>alert("Error updating footer content: ' . $stmt->error . '");</script>';
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

			<div class="container">
        <h2>Edit Footer</h2>
        <form method="POST" action="homepage-footer.php">
            <div class="form-group">
                <label>Address</label>
                <input type="text" class="form-control" name="address" value="<?php echo $footerData['address']; ?>" required />
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" class="form-control" name="phone" value="<?php echo $footerData['phone']; ?>" required />
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo $footerData['email']; ?>" required />
            </div>
            <div class="form-group">
                <label>Twitter</label>
                <input type="text" class="form-control" name="twitter" value="<?php echo $footerData['twitter']; ?>" />
            </div>
            <div class="form-group">
                <label>Facebook</label>
                <input type="text" class="form-control" name="facebook" value="<?php echo $footerData['facebook']; ?>" />
            </div>
            <div class="form-group">
                <label>YouTube</label>
                <input type="text" class="form-control" name="youtube" value="<?php echo $footerData['youtube']; ?>" />
            </div>
            <div class="form-group">
                <label>LinkedIn</label>
                <input type="text" class="form-control" name="linkedin" value="<?php echo $footerData['linkedin']; ?>" />
            </div>
            <div class="form-group">
                <label>Services</label>
                <textarea class="form-control" name="services"><?php echo isset($footerData['services']) ? $footerData['services'] : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label>Quick Links</label>
                <textarea class="form-control" name="quick_links"><?php echo isset($footerData['quick_links']) ? $footerData['quick_links'] : ''; ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
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

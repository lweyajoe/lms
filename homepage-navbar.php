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

// Fetch existing navbar data
$stmt = $conn->prepare("SELECT * FROM homepage_navbar WHERE id = 1");
$stmt->execute();
$navbarData = $stmt->get_result()->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $address = htmlspecialchars($_POST["address"]);
    $hours = htmlspecialchars($_POST["hours"]);
    $email = htmlspecialchars($_POST["email"]);
    $phone = htmlspecialchars($_POST["phone"]);
    $facebookLink = htmlspecialchars($_POST["facebook_link"]);
    $twitterLink = htmlspecialchars($_POST["twitter_link"]);
    $linkedinLink = htmlspecialchars($_POST["linkedin_link"]);

    // Update navbar data in the database
    if ($navbarData) {
        $stmt = $conn->prepare("UPDATE homepage_navbar SET address = ?, hours = ?, email = ?, phone = ?, facebook_link = ?, twitter_link = ?, linkedin_link = ? WHERE id = 1");
        $stmt->bind_param("sssssss", $address, $hours, $email, $phone, $facebookLink, $twitterLink, $linkedinLink);
    } else {
        $stmt = $conn->prepare("INSERT INTO homepage_navbar (address, hours, email, phone, facebook_link, twitter_link, linkedin_link) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $address, $hours, $email, $phone, $facebookLink, $twitterLink, $linkedinLink);
    }

    if ($stmt->execute()) {
        echo '<script>alert("Navbar updated successfully!");</script>';
        header("Location: homepage-navbar.php");
        exit();
    } else {
        echo '<script>alert("Error: ' . $stmt->error . '");</script>';
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
        <h2>Edit Navbar</h2>
        <form method="POST" action="homepage-navbar.php">
            <div class="form-group">
                <label>Address</label>
                <input type="text" class="form-control" name="address" value="<?php echo $navbarData['address']; ?>" required />
            </div>
            <div class="form-group">
                <label>Hours</label>
                <input type="text" class="form-control" name="hours" value="<?php echo $navbarData['hours']; ?>" required />
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="email" value="<?php echo $navbarData['email']; ?>" required />
            </div>
            <div class="form-group">
                <label>Phone</label>
                <input type="text" class="form-control" name="phone" value="<?php echo $navbarData['phone']; ?>" required />
            </div>
            <div class="form-group">
                <label>Facebook Link</label>
                <input type="url" class="form-control" name="facebook_link" value="<?php echo $navbarData['facebook_link']; ?>" required />
            </div>
            <div class="form-group">
                <label>Twitter Link</label>
                <input type="url" class="form-control" name="twitter_link" value="<?php echo $navbarData['twitter_link']; ?>" required />
            </div>
            <div class="form-group">
                <label>LinkedIn Link</label>
                <input type="url" class="form-control" name="linkedin_link" value="<?php echo $navbarData['linkedin_link']; ?>" required />
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

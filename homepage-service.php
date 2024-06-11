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

// Fetch existing service data
$stmt = $conn->prepare("SELECT * FROM homepage_service WHERE id = 1");
$stmt->execute();
$serviceData = $stmt->get_result()->fetch_assoc();

// Handle form submission for updating service content
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $main_title = htmlspecialchars($_POST["main_title"]);
    $main_subtitle = htmlspecialchars($_POST["main_subtitle"]);
    $service1_title = htmlspecialchars($_POST["service1_title"]);
    $service1_text = htmlspecialchars($_POST["service1_text"]);
    $service1_image = htmlspecialchars($_POST["service1_image"]);
    $service2_title = htmlspecialchars($_POST["service2_title"]);
    $service2_text = htmlspecialchars($_POST["service2_text"]);
    $service2_image = htmlspecialchars($_POST["service2_image"]);
    $service3_title = htmlspecialchars($_POST["service3_title"]);
    $service3_text = htmlspecialchars($_POST["service3_text"]);
    $service3_image = htmlspecialchars($_POST["service3_image"]);
    $service4_title = htmlspecialchars($_POST["service4_title"]);
    $service4_text = htmlspecialchars($_POST["service4_text"]);
    $service4_image = htmlspecialchars($_POST["service4_image"]);

    $stmt = $conn->prepare("UPDATE homepage_service SET main_title = ?, main_subtitle = ?, service1_title = ?, service1_text = ?, service1_image = ?, service2_title = ?, service2_text = ?, service2_image = ?, service3_title = ?, service3_text = ?, service3_image = ?, service4_title = ?, service4_text = ?, service4_image = ? WHERE id = 1");
    $stmt->bind_param("ssssssssssssss", $main_title, $main_subtitle, $service1_title, $service1_text, $service1_image, $service2_title, $service2_text, $service2_image, $service3_title, $service3_text, $service3_image, $service4_title, $service4_text, $service4_image);

    if ($stmt->execute()) {
        echo '<script>alert("Service section updated successfully!");</script>';
        header("Location: homepage-service.php");
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
        <h2>Edit Service Section</h2>
        <form method="POST" action="homepage-service.php">
            <div class="form-group">
                <label>Main Title</label>
                <input type="text" class="form-control" name="main_title" value="<?php echo $serviceData['main_title']; ?>" required />
            </div>
            <div class="form-group">
                <label>Main Subtitle</label>
                <input type="text" class="form-control" name="main_subtitle" value="<?php echo $serviceData['main_subtitle']; ?>" required />
            </div>
            <div class="form-group">
                <label>Service 1 Title</label>
                <input type="text" class="form-control" name="service1_title" value="<?php echo $serviceData['service1_title']; ?>" required />
            </div>
            <div class="form-group">
                <label>Service 1 Text</label>
                <textarea class="form-control" name="service1_text" required><?php echo $serviceData['service1_text']; ?></textarea>
            </div>
            <div class="form-group">
                <label>Service 1 Image URL</label>
                <input type="text" class="form-control" name="service1_image" value="<?php echo $serviceData['service1_image']; ?>" required />
            </div>
            <div class="form-group">
                <label>Service 2 Title</label>
                <input type="text" class="form-control" name="service2_title" value="<?php echo $serviceData['service2_title']; ?>" required />
            </div>
            <div class="form-group">
                <label>Service 2 Text</label>
                <textarea class="form-control" name="service2_text" required><?php echo $serviceData['service2_text']; ?></textarea>
            </div>
            <div class="form-group">
                <label>Service 2 Image URL</label>
                <input type="text" class="form-control" name="service2_image" value="<?php echo $serviceData['service2_image']; ?>" required />
            </div>
            <div class="form-group">
                <label>Service 3 Title</label>
                <input type="text" class="form-control" name="service3_title" value="<?php echo $serviceData['service3_title']; ?>" required />
            </div>
            <div class="form-group">
                <label>Service 3 Text</label>
                <textarea class="form-control" name="service3_text" required><?php echo $serviceData['service3_text']; ?></textarea>
            </div>
            <div class="form-group">
                <label>Service 3 Image URL</label>
                <input type="text" class="form-control" name="service3_image" value="<?php echo $serviceData['service3_image']; ?>" required />
            </div>
            <div class="form-group">
                <label>Service 4 Title</label>
                <input type="text" class="form-control" name="service4_title" value="<?php echo $serviceData['service4_title']; ?>" required />
            </div>
            <div class="form-group">
                <label>Service 4 Text</label>
                <textarea class="form-control" name="service4_text" required><?php echo $serviceData['service4_text']; ?></textarea>
            </div>
            <div class="form-group">
                <label>Service 4 Image URL</label>
                <input type="text" class="form-control" name="service4_image" value="<?php echo $serviceData['service4_image']; ?>" required />
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

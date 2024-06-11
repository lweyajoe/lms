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

// Fetch existing about data
$stmt = $conn->prepare("SELECT * FROM homepage_about WHERE id = 1");
$stmt->execute();
$aboutData = $stmt->get_result()->fetch_assoc();

// Handle form submission for updating about content
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $about_title = htmlspecialchars($_POST["about_title"]);
    $about_text = htmlspecialchars($_POST["about_text"]);
    $story_text = htmlspecialchars($_POST["story_text"]);
    $mission_text = htmlspecialchars($_POST["mission_text"]);
    $vision_text = htmlspecialchars($_POST["vision_text"]);
    $feature1_title = htmlspecialchars($_POST["feature1_title"]);
    $feature1_text = htmlspecialchars($_POST["feature1_text"]);
    $feature1_icon = htmlspecialchars($_POST["feature1_icon"]);
    $feature2_title = htmlspecialchars($_POST["feature2_title"]);
    $feature2_text = htmlspecialchars($_POST["feature2_text"]);
    $feature2_icon = htmlspecialchars($_POST["feature2_icon"]);
    $feature3_title = htmlspecialchars($_POST["feature3_title"]);
    $feature3_text = htmlspecialchars($_POST["feature3_text"]);
    $feature3_icon = htmlspecialchars($_POST["feature3_icon"]);
    $image_url = htmlspecialchars($_POST["image_url"]);

    $stmt = $conn->prepare("UPDATE homepage_about SET about_title = ?, about_text = ?, story_text = ?, mission_text = ?, vision_text = ?, feature1_title = ?, feature1_text = ?, feature1_icon = ?, feature2_title = ?, feature2_text = ?, feature2_icon = ?, feature3_title = ?, feature3_text = ?, feature3_icon = ?, image_url = ? WHERE id = 1");
    $stmt->bind_param("sssssssssssssss", $about_title, $about_text, $story_text, $mission_text, $vision_text, $feature1_title, $feature1_text, $feature1_icon, $feature2_title, $feature2_text, $feature2_icon, $feature3_title, $feature3_text, $feature3_icon, $image_url);

    if ($stmt->execute()) {
        echo '<script>alert("About section updated successfully!");</script>';
        header("Location: homepage-about.php");
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
        <h2>Edit About Section</h2>
        <form method="POST" action="homepage-about.php">
            <div class="form-group">
                <label>About Title</label>
                <input type="text" class="form-control" name="about_title" value="<?php echo $aboutData['about_title']; ?>" required />
            </div>
            <div class="form-group">
                <label>About Text</label>
                <input type="text" class="form-control" name="about_text" value="<?php echo $aboutData['about_text']; ?>" required />
            </div>
            <div class="form-group">
                <label>Story Text</label>
                <textarea class="form-control" name="story_text" required><?php echo $aboutData['story_text']; ?></textarea>
            </div>
            <div class="form-group">
                <label>Mission Text</label>
                <textarea class="form-control" name="mission_text" required><?php echo $aboutData['mission_text']; ?></textarea>
            </div>
            <div class="form-group">
                <label>Vision Text</label>
                <textarea class="form-control" name="vision_text" required><?php echo $aboutData['vision_text']; ?></textarea>
            </div>
            <div class="form-group">
                <label>Feature 1 Title</label>
                <input type="text" class="form-control" name="feature1_title" value="<?php echo $aboutData['feature1_title']; ?>" required />
            </div>
            <div class="form-group">
                <label>Feature 1 Text</label>
                <input type="text" class="form-control" name="feature1_text" value="<?php echo $aboutData['feature1_text']; ?>" required />
            </div>
            <div class="form-group">
                <label>Feature 1 Icon</label>
                <input type="text" class="form-control" name="feature1_icon" value="<?php echo $aboutData['feature1_icon']; ?>" required />
            </div>
            <div class="form-group">
                <label>Feature 2 Title</label>
                <input type="text" class="form-control" name="feature2_title" value="<?php echo $aboutData['feature2_title']; ?>" required />
            </div>
            <div class="form-group">
                <label>Feature 2 Text</label>
                <input type="text" class="form-control" name="feature2_text" value="<?php echo $aboutData['feature2_text']; ?>" required />
            </div>
            <div class="form-group">
                <label>Feature 2 Icon</label>
                <input type="text" class="form-control" name="feature2_icon" value="<?php echo $aboutData['feature2_icon']; ?>" required />
            </div>
            <div class="form-group">
                <label>Feature 3 Title</label>
                <input type="text" class="form-control" name="feature3_title" value="<?php echo $aboutData['feature3_title']; ?>" required />
            </div>
            <div class="form-group">
                <label>Feature 3 Text</label>
                <input type="text" class="form-control" name="feature3_text" value="<?php echo $aboutData['feature3_text']; ?>" required />
            </div>
            <div class="form-group">
                <label>Feature 3 Icon</label>
                <input type="text" class="form-control" name="feature3_icon" value="<?php echo $aboutData['feature3_icon']; ?>" required />
            </div>
            <div class="form-group">
                <label>Image URL</label>
                <input type="text" class="form-control" name="image_url" value="<?php echo $aboutData['image_url']; ?>" required />
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

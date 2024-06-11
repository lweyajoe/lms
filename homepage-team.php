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

// Fetch existing team data
$teamData = [];
$stmt = $conn->prepare("SELECT * FROM homepage_team");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $teamData[] = $row;
}

// Handle form submission for updating team content
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    for ($i = 0; $i < count($_POST['id']); $i++) {
        $id = $_POST['id'][$i];
        $name = htmlspecialchars($_POST['name'][$i]);
        $role = htmlspecialchars($_POST['role'][$i]);
        $image = htmlspecialchars($_POST['image'][$i]);
        $facebook = htmlspecialchars($_POST['facebook'][$i]);
        $twitter = htmlspecialchars($_POST['twitter'][$i]);
        $instagram = htmlspecialchars($_POST['instagram'][$i]);

        $stmt = $conn->prepare("UPDATE homepage_team SET name = ?, role = ?, image = ?, facebook = ?, twitter = ?, instagram = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $name, $role, $image, $facebook, $twitter, $instagram, $id);

        if (!$stmt->execute()) {
            echo '<script>alert("Error: ' . $stmt->error . '");</script>';
        }
    }
    echo '<script>alert("Team section updated successfully!");</script>';
    header("Location: homepage-team.php");
    exit();
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
        <h2>Edit Team Section</h2>
        <form method="POST" action="homepage-team.php">
            <?php foreach ($teamData as $team) { ?>
                <div class="team-member">
                    <input type="hidden" name="id[]" value="<?php echo $team['id']; ?>">
                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" name="name[]" value="<?php echo $team['name']; ?>" required />
                    </div>
                    <div class="form-group">
                        <label>Role</label>
                        <input type="text" class="form-control" name="role[]" value="<?php echo $team['role']; ?>" required />
                    </div>
                    <div class="form-group">
                        <label>Image URL</label>
                        <input type="text" class="form-control" name="image[]" value="<?php echo $team['image']; ?>" required />
                    </div>
                    <div class="form-group">
                        <label>Facebook</label>
                        <input type="text" class="form-control" name="facebook[]" value="<?php echo $team['facebook']; ?>" />
                    </div>
                    <div class="form-group">
                        <label>Twitter</label>
                        <input type="text" class="form-control" name="twitter[]" value="<?php echo $team['twitter']; ?>" />
                    </div>
                    <div class="form-group">
                        <label>Instagram</label>
                        <input type="text" class="form-control" name="instagram[]" value="<?php echo $team['instagram']; ?>" />
                    </div>
                </div>
            <?php } ?>
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

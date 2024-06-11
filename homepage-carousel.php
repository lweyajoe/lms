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

// Fetch existing carousel data
$stmt = $conn->prepare("SELECT * FROM homepage_carousel");
$stmt->execute();
$carouselData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Handle form submission for adding/updating carousel items
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = isset($_POST["id"]) ? intval($_POST["id"]) : null;
    $image_url = htmlspecialchars($_POST["image_url"]);
    $caption_title = htmlspecialchars($_POST["caption_title"]);
    $caption_text = htmlspecialchars($_POST["caption_text"]);
    $button_text = htmlspecialchars($_POST["button_text"]);
    $button_link = htmlspecialchars($_POST["button_link"]);
    $active = isset($_POST["active"]) ? 1 : 0;

    if ($id) {
        // Update existing item
        $stmt = $conn->prepare("UPDATE homepage_carousel SET image_url = ?, caption_title = ?, caption_text = ?, button_text = ?, button_link = ?, active = ? WHERE id = ?");
        $stmt->bind_param("ssssssi", $image_url, $caption_title, $caption_text, $button_text, $button_link, $active, $id);
    } else {
        // Insert new item
        $stmt = $conn->prepare("INSERT INTO homepage_carousel (image_url, caption_title, caption_text, button_text, button_link, active) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssi", $image_url, $caption_title, $caption_text, $button_text, $button_link, $active);
    }

    if ($stmt->execute()) {
        echo '<script>alert("Carousel item saved successfully!");</script>';
        header("Location: homepage-carousel.php");
        exit();
    } else {
        echo '<script>alert("Error: ' . $stmt->error . '");</script>';
    }
}

// Handle deletion of carousel items
if (isset($_GET["delete"])) {
    $id = intval($_GET["delete"]);
    $stmt = $conn->prepare("DELETE FROM homepage_carousel WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: homepage-carousel.php");
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
        <h2>Edit Carousel</h2>
        <form method="POST" action="homepage-carousel.php">
            <input type="hidden" name="id" value="<?php echo isset($carouselData[0]['id']) ? $carouselData[0]['id'] : ''; ?>">
            <div class="form-group">
                <label>Image URL</label>
                <input type="text" class="form-control" name="image_url" value="<?php echo isset($carouselData[0]['image_url']) ? $carouselData[0]['image_url'] : ''; ?>" required />
            </div>
            <div class="form-group">
                <label>Caption Title</label>
                <input type="text" class="form-control" name="caption_title" value="<?php echo isset($carouselData[0]['caption_title']) ? $carouselData[0]['caption_title'] : ''; ?>" required />
            </div>
            <div class="form-group">
                <label>Caption Text</label>
                <input type="text" class="form-control" name="caption_text" value="<?php echo isset($carouselData[0]['caption_text']) ? $carouselData[0]['caption_text'] : ''; ?>" required />
            </div>
            <div class="form-group">
                <label>Button Text</label>
                <input type="text" class="form-control" name="button_text" value="<?php echo isset($carouselData[0]['button_text']) ? $carouselData[0]['button_text'] : ''; ?>" required />
            </div>
            <div class="form-group">
                <label>Button Link</label>
                <input type="text" class="form-control" name="button_link" value="<?php echo isset($carouselData[0]['button_link']) ? $carouselData[0]['button_link'] : ''; ?>" required />
            </div>
            <div class="form-group">
                <label>Active</label>
                <input type="checkbox" name="active" <?php echo isset($carouselData[0]['active']) && $carouselData[0]['active'] == 1 ? 'checked' : ''; ?>>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
        </form>
        <h3>Carousel Items</h3>
        <table class="table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image URL</th>
                    <th>Caption Title</th>
                    <th>Caption Text</th>
                    <th>Button Text</th>
                    <th>Button Link</th>
                    <th>Active</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($carouselData as $item): ?>
                <tr>
                    <td><?php echo $item['id']; ?></td>
                    <td><?php echo $item['image_url']; ?></td>
                    <td><?php echo $item['caption_title']; ?></td>
                    <td><?php echo $item['caption_text']; ?></td>
                    <td><?php echo $item['button_text']; ?></td>
                    <td><?php echo $item['button_link']; ?></td>
                    <td><?php echo $item['active'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <a href="homepage-carousel.php?edit=<?php echo $item['id']; ?>">Edit</a>
                        <a href="homepage-carousel.php?delete=<?php echo $item['id']; ?>" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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

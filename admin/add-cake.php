<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
error_reporting(0);
include ('includes/dbconnection.php');
if (strlen($_SESSION['fosaid'] == 0)) {    // checking if the user is logged in by verifying the existence of a session variable fosaid. 
    //If the variable is empty, the user is redirected to the logout page.
    header('location:logout.php');
} else {

    if (isset($_POST['submit'])) {
        $faid = $_SESSION['fosaid'];
        $fcat = $_POST['foodcategory'];
        $itemname = $_POST['itemname'];
        $description = $_POST['description'];
        $quantity = $_POST['quantity'];
        $price = $_POST['price'];
        $weight = $_POST['weight'];
        $targetDir = "itemimages/";

        // Check if the directory exists, create it if not, and ensure it is writable
        if (!is_dir($targetDir)) {
            if (!mkdir($targetDir, 0777, true)) {
                die("Failed to create upload directory.");
            }
        } elseif (!is_writable($targetDir)) {
            if (!chmod($targetDir, 0777)) {
                die("Upload directory is not writable.");
            }
        }

        // Get the uploaded file's name
        $itempic = $_FILES["itemimages"]["name"];
        // Extract the file extension
        $extension = strtolower(pathinfo($itempic, PATHINFO_EXTENSION));
        // Allowed extensions
        $allowed_extensions = array("jpg", "jpeg", "png", "gif");
        // Validation for allowed extensions .in_array() function searches an array for a specific value.


        // Validation for allowed extensions
        if (!in_array($extension, $allowed_extensions)) {
            echo "<script>alert('Invalid format. Only jpg / jpeg/ png /gif format allowed');</script>";
        } else {
            // Create a unique file name using md5
            $item = md5($itempic) . "." . $extension;

            // Move the uploaded file to the target directory
            if (move_uploaded_file($_FILES["itemimages"]["tmp_name"], $targetDir . $item)) {
                // Database connection
                $con = mysqli_connect("localhost", "root", "", "cbsdb");

                // Check connection
                if (mysqli_connect_errno()) {
                    echo "Failed to connect to MySQL: " . mysqli_connect_error();
                    exit();
                }

                // Prepare the query to insert the record into the database
                $query = mysqli_query($con, "INSERT INTO tblfood (CategoryName, ItemName, ItemPrice, ItemDes, ItemQty, Weight, Image) VALUES ('$fcat', '$itemname', '$price', '$description', '$quantity', '$weight', '$item')");

                // Check if the query was successful
                if ($query) {
                    echo '<script>alert("Cake has been added")</script>';
                    echo "<script>window.location.href ='add-cake.php'</script>";
                } else {
                    echo '<script>alert("Something Went Wrong. Please try again.")</script>';
                }
            } else {
                echo '<script>alert("Failed to move uploaded file.")</script>';
            }
        }
    }
    ?>
    <!DOCTYPE html>
    <html>

    <head>
        <title>Cake Bakery System|| Add Cake</title>

        <link href="css/bootstrap.min.css" rel="stylesheet">
        <link href="font-awesome/css/font-awesome.css" rel="stylesheet">
        <link href="css/plugins/iCheck/custom.css" rel="stylesheet">
        <link href="css/plugins/steps/jquery.steps.css" rel="stylesheet">
        <link href="css/animate.css" rel="stylesheet">
        <link href="css/style.css" rel="stylesheet">
        <script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
        <script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>
    </head>

    <body>

        <div id="wrapper">

            <?php include_once ('includes/leftbar.php'); ?>

            <div id="page-wrapper" class="gray-bg">
                <?php include_once ('includes/header.php'); ?>
                <div class="row border-bottom">

                </div>
                <div class="row wrapper border-bottom white-bg page-heading">
                    <div class="col-lg-10">
                        <h2>Cake Item</h2>
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="dashboard.php">Home</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a>Item Name</a>
                            </li>
                            <li class="breadcrumb-item active">
                                <strong>Add</strong>
                            </li>
                        </ol>
                    </div>
                </div>

                <div class="wrapper wrapper-content animated fadeInRight">

                    <div class="row">
                        <div class="col-lg-12">
                            <div class="ibox">

                                <div class="ibox-content">


                                    <form id="submit" action="#" class="wizard-big" method="post" name="submit"
                                        enctype="multipart/form-data">
                                        <fieldset>
                                            <div class="form-group row"><label class="col-sm-2 col-form-label">Cake
                                                    Category:</label>
                                                <div class="col-sm-10"><select name='foodcategory' id="foodcategory"
                                                        class="form-control white_bg" required="true">

                                                        <?php

                                                        $query = mysqli_query($con, "select * from  tblcategory");
                                                        while ($row = mysqli_fetch_array($query)) {
                                                            ?>
                                                            <option value="<?php echo $row['CategoryName']; ?>">
                                                                <?php echo $row['CategoryName']; ?></option>
                                                        <?php } ?>
                                                    </select></div>
                                            </div>
                                            <div class="form-group row"><label class="col-sm-2 col-form-label">Item
                                                    Name:</label>
                                                <div class="col-sm-10"><input type="text" class="form-control"
                                                        name="itemname" required="true"></div>
                                            </div>

                                            <div class="form-group row"><label
                                                    class="col-sm-2 col-form-label">Description:</label>
                                                <div class="col-sm-10">
                                                    <textarea type="text" class="form-control" name="description" row="8"
                                                        cols="12" required="true">
                                                         </textarea>
                                                </div>
                                            </div>
                                            <div class="form-group row"><label class="col-sm-2 col-form-label">Image</label>
                                                <div class="col-sm-10"><input type="file" name="itemimages" required="true">
                                                </div>
                                            </div>
                                            <div class="form-group row"><label
                                                    class="col-sm-2 col-form-label">Quantity:</label>
                                                <div class="col-sm-10"><input type="text" class="form-control"
                                                        name="quantity" required="true"></div>
                                            </div>
                                            <div class="form-group row"><label class="col-sm-2 col-form-label">Cake
                                                    Weight:</label>
                                                <div class="col-sm-10"><select class="form-control white_bg" required="true"
                                                        name="weight">
                                                        <option value="">Choose Weight</option>
                                                        <option value="500 gm">500 gm</option>
                                                        <option value="1 kg">1 kg</option>
                                                        <option value="1.5 kg">1.5 kg</option>
                                                        <option value="2 kg">2 kg</option>
                                                        <option value="2.5 kg">2.5 kg</option>
                                                        <option value="3 kg">3 kg</option>
                                                        <option value="3.5 kg">3.5 kg</option>
                                                        <option value="4 kg">4 kg</option>
                                                    </select> </div>
                                            </div>
                                            <div class="form-group row"><label
                                                    class="col-sm-2 col-form-label">Price:</label>
                                                <div class="col-sm-10"><input type="text" class="form-control" name="price"
                                                        required="true"></div>
                                            </div>

                                        </fieldset>

                                        </fieldset>




                                        <p style="text-align: center;"><button type="submit" name="submit"
                                                class="btn btn-primary">Submit</button></p>



                                    </form>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <?php include_once ('includes/footer.php'); ?>

            </div>
        </div>



        <!-- Mainly scripts -->
        <script src="js/jquery-3.1.1.min.js"></script>
        <script src="js/popper.min.js"></script>
        <script src="js/bootstrap.js"></script>
        <script src="js/plugins/metisMenu/jquery.metisMenu.js"></script>
        <script src="js/plugins/slimscroll/jquery.slimscroll.min.js"></script>

        <!-- Custom and plugin javascript -->
        <script src="js/inspinia.js"></script>
        <script src="js/plugins/pace/pace.min.js"></script>

        <!-- Steps -->
        <script src="js/plugins/steps/jquery.steps.min.js"></script>

        <!-- Jquery Validate -->
        <script src="js/plugins/validate/jquery.validate.min.js"></script>


        <script>
            $(document).ready(function () {
                $("#wizard").steps();
                $("#form").steps({
                    bodyTag: "fieldset",
                    onStepChanging: function (event, currentIndex, newIndex) {
                        // Always allow going backward even if the current step contains invalid fields!
                        if (currentIndex > newIndex) {
                            return true;
                        }

                        // Forbid suppressing "Warning" step if the user is to young
                        if (newIndex === 3 && Number($("#age").val()) < 18) {
                            return false;
                        }

                        var form = $(this);

                        // Clean up if user went backward before
                        if (currentIndex < newIndex) {
                            // To remove error styles
                            $(".body:eq(" + newIndex + ") label.error", form).remove();
                            $(".body:eq(" + newIndex + ") .error", form).removeClass("error");
                        }

                        // Disable validation on fields that are disabled or hidden.
                        form.validate().settings.ignore = ":disabled,:hidden";

                        // Start validation; Prevent going forward if false
                        return form.valid();
                    },
                    onStepChanged: function (event, currentIndex, priorIndex) {
                        // Suppress (skip) "Warning" step if the user is old enough.
                        if (currentIndex === 2 && Number($("#age").val()) >= 18) {
                            $(this).steps("next");
                        }

                        // Suppress (skip) "Warning" step if the user is old enough and wants to the previous step.
                        if (currentIndex === 2 && priorIndex === 3) {
                            $(this).steps("previous");
                        }
                    },
                    onFinishing: function (event, currentIndex) {
                        var form = $(this);

                        // Disable validation on fields that are disabled.
                        // At this point it's recommended to do an overall check (mean ignoring only disabled fields)
                        form.validate().settings.ignore = ":disabled";

                        // Start validation; Prevent form submission if false
                        return form.valid();
                    },
                    onFinished: function (event, currentIndex) {
                        var form = $(this);

                        // Submit form input
                        form.submit();
                    }
                }).validate({
                    errorPlacement: function (error, element) {
                        element.before(error);
                    },
                    rules: {
                        confirm: {
                            equalTo: "#password"
                        }
                    }
                });
            });
        </script>

    </body>

    </html>
<?php } ?>
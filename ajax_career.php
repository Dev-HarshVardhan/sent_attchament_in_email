<?php 
include("admin/include/connection.php"); 
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once 'vendor_msg/autoload.php';
require 'vendor_msg/PHPMailer/src/Exception.php';
require 'vendor_msg/PHPMailer/src/PHPMailer.php';
require 'vendor_msg/PHPMailer/src/SMTP.php';

if ($_GET['ajax_value'] == 'img_post') {

    $message = "";
    $success = false;

    $position = $_POST['position'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $mobile = $_POST['mobile'];
    $address = $_POST['address'];

    $img = $_FILES['img']['name'];
    $img_tmp_name = $_FILES['img']['tmp_name'];

    $img_new_name = rand(11, 1000000) . $img;
    $img_ext = pathinfo($img_new_name, PATHINFO_EXTENSION);
    $img_size = $_FILES['img']['size'] / 1024;

    if ($img_size > 5000) {
        $message .= "Image size is greater than 5 MB";
        $success = false;
    } else {
        if ($img_ext == 'pdf' || $img_ext == 'PDF' || $img_ext == 'jpg' || $img_ext == 'jpeg' || $img_ext == 'docs' || $img_ext == 'png') {
            $path = "career_uploads/$img_new_name";
            $store = move_uploaded_file($img_tmp_name, $path);

            if ($store) {
                $insert = "INSERT INTO career(position, name, email, mobile, address, img) 
                           VALUES('$position', '$name', '$email', '$mobile', '$address', '$img_new_name')";

                $result = mysqli_query($conn, $insert);

                if ($result) {
                    
                    $mail = new PHPMailer();
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'hs360863@gmail.com';
                    $mail->Password = 'cjibhfyesqbflcwn'; 
                    $mail->SMTPSecure = 'tls';
                    $mail->Port = 587;

                    $mail->setFrom('hs360863@gmail.com', 'Bharat Ram Global School');
                    $mail->addAddress('hs360863@gmail.com'); 
                    $mail->Subject = 'Career Application Received';

					$txt = "
					<h2>Contact Details </h2>
					Position: {$position},<br><br>
					Name: {$name},<br><br>
					Email Id: {$email} <br><br>
					Mobile: {$mobile} <br><br>
					Message: {$address} <br><br>
					<strong>Best regards,</strong> <br>
					Bharat Ram Global School";
                    
                    $html_body = $txt;
                    $mail->msgHTML($html_body);

                    
                    if ($store) {
                        $mail->addAttachment($path); 
                    } else {
                        echo "<h1>Error uploading file</h1>";
                    }

                    if ($mail->send()) {
                        $message .= "Your request has been sent successfully, and a confirmation email has been sent.";
                    } else {
                        $message .= "Your request has been sent, but we couldn't send the email.";
                    }
                    $success = true;
                } else {
                    $message .= "Sorry, something went wrong.";
                    $success = false;
                }
            }
        } else {
            $message .= "Only PNG, JPEG & JPG files are accepted";
            $success = false;
        }
    }

    $array = array('message' => $message, 'success' => $success);
    echo json_encode($array);
}
?>

<?php

$conn = new mysqli('localhost', 'root', '', 'login');

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$id = isset($_REQUEST['id']) ? $_REQUEST['id'] : null;
$successMessage = $errorMessage = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['id'])) {
    $employeeId = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $number = $_POST['number'];
    $address = $_POST['address'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $image = $_FILES["image"]["name"];
    $temp = $_FILES["image"]["tmp_name"]; 
    $file = "images/" . $image;

    move_uploaded_file($temp, $file);

    if (!preg_match('/^[a-zA-Z ]+$/', $name)) {
        echo "Invalid name format. Name should contain only letters and spaces.";
        $conn->close();
        exit();
    }

    if (!is_numeric($number)) {
        echo "Invalid phone number format. Please enter only numeric characters.";
        $conn->close();
        exit();
    }

    if ($result->num_rows > 0) {
        echo "Error: Email already exists. Please choose a different email.";
        $conn->close();
        exit();
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !preg_match('/^[^0-9]*[a-zA-Z0-9._-]*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/', $email)) {
        echo "Invalid email format";
        $conn->close();
        exit();
    }
 
    $sql = "UPDATE employee SET
            name = '$name',
            email = '$email',
            number = '$number',
            address = '$address',
            dob = '$dob',
            gender = '$gender',
            image = '$image'
            WHERE id = $employeeId";

    if ($conn->query($sql) === TRUE) {
        header("Location: home.php");
    } else {
        $errorMessage = 'Error updating record: ' . $conn->error;
    }
}

$sql = "SELECT * FROM employee WHERE id = $id";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Update Employee</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    </head>
    <body>
    <?php
    if (!empty($successMessage)) {
        echo '<p style="color: green;">' . $successMessage . '</p>';
    }
    if (!empty($errorMessage)) {
        echo '<p style="color: red;">' . $errorMessage . '</p>';
    }
    ?> 

<form action="" method="post" enctype="multipart/form-data" class="m-5 me-5 pe-5">
    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
    <div class="mb-3">
        <label for="name" class="form-label" >Name</label>
        <input type="text" class="form-control" name="name" value="<?php echo $row['name']; ?>" >
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" name="email" value="<?php echo $row['email']; ?>">
    </div>
    <div class="mb-3">
        <label for="number" class="form-label">Phone Number</label>
        <input type="text" class="form-control" name="number" value="<?php echo $row['number']; ?>">
    </div>
    <div class="mb-3">
        <label for="address" class="form-label">Address</label>
        <input type="text" class="form-control" name="address" value="<?php echo $row['address']; ?>">
    </div>
    <div class="mb-3">
        <label for="dob" class="form-label">Date of Birth</label>
        <input type="date" class="form-control" name="dob" value="<?php echo $row['dob']; ?>" >
    </div>
    <div class="mb-3">
        <label class="form-label">Gender</label>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="male" name="gender" value="Male" <?php echo ($row['gender'] === 'male') ? 'selected' : ''; ?> >
            <label class="form-check-label" for="male">Male</label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" id="female" name="gender" value="Female" <?php echo ($row['gender'] === 'female') ? 'selected' : ''; ?> >
            <label class="form-check-label" for="female">Female</label>
        </div>
    </div>
    <div class="mb-3 text-center">
        <label class="form-label">Employee Image</label>
        <input type="file" class="form-control" name="image" accept="image/*" value="<?php echo $row['image']; ?>">
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-secondary" onclick="window.location.href='home.php'">Close</button>
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>
</form>
    </body>
    </html>

 <?php

} else {
    echo 'Record not found.';
}

$conn->close();

?>
<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "login";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["submit"])) {
        $nameArray = $_POST["name"];

    foreach ($nameArray as $index => $name) {
        $email = $_POST["email"][$index];
        $number = $_POST["number"][$index];
        $address = $_POST["address"][$index];
        $dob = $_POST["dob"][$index];
        $gender = $_POST["gender"][$index];
        $image = $_FILES["image"]["name"][$index];
        $temp = $_FILES["image"]["tmp_name"][$index];
        $file = "images/" . $image;

        move_uploaded_file($temp, $file);

        $sql = "INSERT INTO employee (name, email, number,  address, dob, gender, image) 
            VALUES ('$name', '$email', '$number', '$address', '$dob', '$gender', '$image')";

        if ($conn->query($sql) !== TRUE) {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    header("Location: home.php");
    }
}

$conn->close();

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="m-5">
<h1>Employee Information Table</h1>

    <div class="d-flex justify-content-end">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
            Add Employee 
        </button>

        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
            <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Enter Employee Info</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <form id="employeeForm" method="post" action="home.php" enctype="multipart/form-data" onsubmit="return validateAndSubmit()">
                <div id="employeeForms" class="mt-4">
                    <div class="mb-3">
                        <label for="name[]" class="form-label">Name</label>
                        <input type="text" class="form-control" name="name[]" >
                    </div>
                    <div class="mb-3">
                        <label for="email[]" class="form-label">Email</label>
                        <input type="email" class="form-control" name="email[]">
                    </div>
                    <div class="mb-3">
                        <label for="number[]" class="form-label">Phone Number</label>
                        <input type="text" class="form-control" name="number[]" >
                    </div>
                    <div class="mb-3">
                        <label for="address[]" class="form-label">Address</label>
                        <input type="text" class="form-control" name="address[]">
                    </div>
                    <div class="mb-3">
                        <label for="dob[]" class="form-label">Date of Birth</label>
                        <input type="date" class="form-control" name="dob[]">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Gender</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="male" name="gender[]" value="Male" required>
                            <label class="form-check-label" for="male">Male</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" id="female" name="gender[]" value="Female" required >
                            <label class="form-check-label" for="female">Female</label>
                        </div>
                    </div>
                    <div class="mb-3 text-center">
                        <label class="form-label">Select Image</label>
                        <input type="file" class="form-control" name="image[]" accept="image/*">
                    </div>
                    <div class="d-flex">
                        <button type="button" class="btn btn-primary" onclick="addEmployeeForm()">Add more employee</button>
                        <button type="button" class="btn btn-danger" onclick="removeEmployeeForm()">Remove</button>
                    </div>
                </div>
                <button type="submit" class="btn btn-success mt-3" name="submit">Submit</button>
                
            </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
            </div>
        </div>
        </div>
    </div>

    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Address</th>
                <th>Date of Birth</th>
                <th>Gender</th>
                <th>Image</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>

        <?php
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "login";
            
            $conn = new mysqli($servername, $username, $password, $dbname);
            
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }

            $result = $conn->query("SELECT * FROM employee");

            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['name']}</td>";
                echo "<td>{$row['email']}</td>";
                echo "<td>{$row['number']}</td>";
                echo "<td>{$row['address']}</td>";
                echo "<td>{$row['dob']}</td>";
                echo "<td>{$row['gender']}</td>";
                echo "<td><img src='images/{$row['image']}' style='max-width: 100px; max-height: 100px;'></td>";
                echo "<td>
                        <a href='update.php?id={$row['id']}' class='btn btn-primary btn-sm'>Edit</a>
                        <a href='delete.php?id={$row['id']}' class='btn btn-danger btn-sm'>Delete</a>
                      </td>";
                echo "</tr>";
            }
            
        ?>

        </tbody>
    </table>

    <script>
        let formIndex = 0;

        function addEmployeeForm() {
            formIndex++;
            const formDiv = document.getElementById("employeeForms");

            const newForm = document.createElement("div");
            
            newForm.innerHTML = `
            <div id="employeeForms" class="mt-4">
                <div class="mb-3">
                    <label for="name[]" class="form-label">Name</label>
                    <input type="text" class="form-control" name="name[]" >
                </div>
                <div class="mb-3">
                    <label for="email[]" class="form-label">Email</label>
                    <input type="email" class="form-control" name="email[]">
                </div>
                <div class="mb-3">
                    <label for="number[]" class="form-label">Phone Number</label>
                    <input type="text" class="form-control" name="number[]" >
                </div>
                <div class="mb-3">
                    <label for="address[]" class="form-label">Address</label>
                    <input type="text" class="form-control" name="address[]">
                </div>
                <div class="mb-3">
                    <label for="dob[]" class="form-label">Date of Birth</label>
                    <input type="date" class="form-control" name="dob[]">
                </div>
                <div class="mb-3">
                    <label class="form-label">Gender</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="male" name="gender[]" value="Male">
                        <label class="form-check-label" for="male">Male</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="female" name="gender[]" value="Female" >
                        <label class="form-check-label" for="female">Female</label>
                    </div>
                </div>
                <div class="mb-3 text-center">
                    <label class="form-label">Employee Image</label>
                    <input type="file" class="form-control" name="image[]" accept="image/*">
                </div>
                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-success" onclick="addEmployeeForm()">Add More Employee</button>
                    <button type="button" class="btn btn-danger" onclick="removeEmployeeForm()">Remove</button>
                </div>
            </div>
            `;
            formDiv.appendChild(newForm);
        }

        function removeEmployeeForm() {
            const formDiv = document.getElementById("employeeForms");
            if (formIndex > 0) {
                formDiv.removeChild(formDiv.lastChild);
                formIndex--;
            }
        }

    

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('submitBtn').addEventListener('click', function (event) {
            event.preventDefault();
            
            validateAndSubmit();
        });
    });

    function validateAndSubmit() {
        var valid = true;

        var nameInputs = document.querySelectorAll('input[name="name[]"]');
        var existingEmails = [];

        nameInputs.forEach(function (nameInput) {
            var name = nameInput.value.trim();

            if (!/^[a-zA-Z ]+$/.test(name)) {
                alert('Invalid name format: ' + name);
                valid = false;
            }
        });

        var numberInputs = document.querySelectorAll('input[name="number[]"]');

        numberInputs.forEach(function (numberInput) {
            var number = numberInput.value.trim();

            if (!/^\d+$/.test(number)) {
                alert('Invalid phone number format. Please enter only numeric characters.');
                valid = false;
            }

            if (number.length > 12) {
                alert('Phone number cannot be more than 12 digits.');
                valid = false;
            }
        });

        var emailInputs = document.querySelectorAll('input[name="email[]"]');
        var existingEmails = [];

        emailInputs.forEach(function (emailInput) {
            var email = emailInput.value.trim();

            if (!/^[^0-9]*[a-zA-Z0-9._-]*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/.test(email)) {
                alert('Invalid email format: ' + email);
                valid = false;
            }

            if (existingEmails.includes(email)) {
                alert('Error: Email ' + email + ' already exists. Please choose a different email.');
                valid = false;
            }

            existingEmails.push(email);
        });

        var dobInputs = document.querySelectorAll('input[name="dob[]"]');

        dobInputs.forEach(function (dobInput) {
            var dob = dobInput.value.trim();

            var dateRegex = /^\d{4}-\d{2}-\d{2}$/;
            if (!dateRegex.test(dob)) {
                alert('Invalid Date of Birth format: ' + dob);
                valid = false;
            }

            var dateTime = new Date(dob);
            if (isNaN(dateTime.getTime())) {
                alert('Invalid Date of Birth format: ' + dob);
                valid = false;
            }
        });

        var imageInputs = document.querySelectorAll('input[name="image[]"]');

        imageInputs.forEach(function (imageInput) {
            var imageFile = imageInput.files[0];

            if (!imageFile) {
                alert('Please select an image for each employee.');
                valid = false;
            }
            var allowedImageTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (allowedImageTypes.indexOf(imageFile.type) === -1) {
                alert('Invalid image file type. Please select a JPEG, PNG, or GIF file.');
                valid = false;
            }
        });

        if (!valid) {
        alert('Validation failed. Please correct the errors before submitting.');
        return false; 
    }

    return true;
      
    }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
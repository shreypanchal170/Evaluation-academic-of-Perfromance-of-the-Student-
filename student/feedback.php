<!DOCTYPE html>
<html>
<head>
  <title>Feedback Form</title>
</head>
<body>
  <form id="feedback-form">
    <input type="text" name="username" placeholder="Username" required>
    <textarea name="feedback" placeholder="Feedback" required></textarea>
    <button type="submit">Submit</button>
  </form>

  <script>
    const form = document.getElementById('feedback-form');

    form.addEventListener('submit', (e) => {
      e.preventDefault();
      const formData = new FormData(form);
      fetch('process-feedback.php', {
        method: 'POST',
        body: formData,
      })
      .then(response => response.text())
      .then(data => alert(data));
    });
  </script>
</body>
</html>
<?php
// Connect to database
$conn = new mysqli('localhost', 'root', '', 'evaluation_db');

// Check connection
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

// Escape user inputs
$username = $conn->real_escape_string($_POST['username']);
$feedback = $conn->real_escape_string($_POST['feedback']);

// Generate token (e.g. UUID)
$token = uniqid();

// Store feedback and user data in database
$query = "INSERT INTO feedback (user_id, feedback, token) VALUES (
    (SELECT id FROM users WHERE username='$username' LIMIT 1),
    '$feedback', 
    '$token'
)";
if ($conn->query($query) === TRUE) {
    echo 'Feedback submitted successfully!';
} else {
    echo 'Error: ' . $conn->error;
}

// Retrieve feedback using token
if (isset($_GET['token'])) {
    $token = $conn->real_escape_string($_GET['token']);
    $query = "SELECT feedback FROM feedback WHERE token = '$token'";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo $row['feedback'];
        }
    } else {
        echo 'No feedback found!';
    }
}
// Close database connection
$conn->close();
?>

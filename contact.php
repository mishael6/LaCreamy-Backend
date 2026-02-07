<?php
// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_once 'db.php';
    
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $message = trim($_POST['message']);
    
    $errors = [];
    
    if (empty($name)) {
        $errors[] = 'Name is required';
    }
    
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Valid email is required';
    }
    
    if (empty($phone)) {
        $errors[] = 'Phone number is required';
    }
    
    if (empty($message)) {
        $errors[] = 'Message is required';
    }
    
    if (empty($errors)) {
        $stmt = $conn->prepare("INSERT INTO contact_submissions (full_name, email, phone_number, message) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $name, $email, $phone, $message);
        
        if ($stmt->execute()) {
            $success_message = "Thank you! Your message has been sent successfully.";
        } else {
            $error_message = "Failed to save your message. Please try again.";
        }
        
        $stmt->close();
    } else {
        $error_message = implode(', ', $errors);
    }
    
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>LA CREAMY CAKEIST - Contact</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #fff9f3;
      color: #3a2a1a;
      margin: 0;
      padding: 0;
    }
    header {
      background: #fffaf4;
      padding: 15px 30px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      border-bottom: 2px solid #d4b47f;
    }
    .logo-container {
      display: flex;
      align-items: center;
    }
    .logo {
      height: 60px;
      width: auto;
      border-radius: 8px;
      margin-right: 10px;
    }
    nav ul {
      list-style: none;
      margin: 0;
      padding: 0;
    }
    nav ul li {
      display: inline-block;
      margin: 0 10px;
    }
    nav ul li a {
      text-decoration: none;
      color: #3a2a1a;
      font-weight: 500;
      transition: color 0.3s ease;
    }
    nav ul li a:hover {
      color: #b08e58;
    }
    nav ul li a.active {
      color: #c5a46d;
      font-weight: 600;
    }
    .contact-section {
      padding: 60px 20px;
      background: linear-gradient(to bottom, #fffdf8, #fff9f3);
      text-align: center;
    }
    .contact-section h2 {
      font-size: 2.5em;
      color: #3a2a1a;
      margin-bottom: 20px;
    }
    .contact-section p {
      font-size: 1.1em;
      color: #6b5440;
      margin-bottom: 40px;
    }
    .contact-form {
      max-width: 600px;
      margin: 0 auto;
      background: #fffaf4;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .form-group {
      margin-bottom: 20px;
      text-align: left;
    }
    label {
      display: block;
      font-weight: 500;
      color: #3a2a1a;
      margin-bottom: 8px;
    }
    input, textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #d4b47f;
      border-radius: 8px;
      font-size: 1em;
      font-family: 'Poppins', sans-serif;
    }
    textarea {
      resize: none;
      height: 120px;
    }
    button {
      padding: 12px 25px;
      background-color: #c5a46d;
      border: none;
      color: white;
      border-radius: 8px;
      cursor: pointer;
      font-weight: 600;
      transition: background 0.3s ease, transform 0.2s ease;
    }
    button:hover {
      background-color: #b08e58;
      transform: scale(1.05);
    }
    .contact-info {
      margin-top: 40px;
      text-align: center;
      color: #6b5440;
    }
    .contact-info h3 {
      margin-bottom: 10px;
      color: #3a2a1a;
    }
    .social-media {
      margin-top: 30px;
    }
    .social-media a {
      display: inline-block;
      margin: 0 10px;
      text-decoration: none;
      color: #c5a46d;
      font-size: 1.5em;
      transition: color 0.3s ease, transform 0.3s ease;
    }
    .social-media a:hover {
      color: #b08e58;
      transform: scale(1.1);
    }
    footer {
      background: #fffaf4;
      padding: 20px;
      text-align: center;
      border-top: 2px solid #d4b47f;
      margin-top: 40px;
      font-size: 0.9em;
    }
    .alert {
      padding: 15px;
      margin-bottom: 20px;
      border-radius: 8px;
      font-weight: 500;
    }
    .alert-success {
      background-color: #d4edda;
      color: #155724;
      border: 1px solid #c3e6cb;
    }
    .alert-error {
      background-color: #f8d7da;
      color: #721c24;
      border: 1px solid #f5c6cb;
    }
  </style>
</head>
<body>
  <header>
    <div class="logo-container">
      <img src="photo_2022-11-27_15-40-41.jpg" alt="LA CREAMY CAKEIST Logo" class="logo">
      <h1>LA CREAMY CAKEIST</h1>
    </div>
    <nav>
      <ul>
        <li><a href="index.html">Home</a></li>
        <li><a href="menu.html">Menu</a></li>
        <li><a href="about.html">About</a></li>
        <li><a href="blog.html">Blog</a></li>
      </ul>
    </nav>
  </header>

  <section class="contact-section">
    <h2>Contact Us</h2>
    <p>We'd love to hear from you! Reach out for custom cake orders, inquiries, or feedback.</p>

    <form class="contact-form" method="POST" action="contact.php">
      <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?php echo $success_message; ?></div>
      <?php endif; ?>
      
      <?php if (isset($error_message)): ?>
        <div class="alert alert-error"><?php echo $error_message; ?></div>
      <?php endif; ?>
      
      <div class="form-group">
        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" placeholder="Enter your full name" required>
      </div>
      <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required>
      </div>
      <div class="form-group">
        <label for="phone">Phone Number</label>
        <input type="tel" id="phone" name="phone" placeholder="Enter your phone number" required>
      </div>
      <div class="form-group">
        <label for="message">Message</label>
        <textarea id="message" name="message" placeholder="Write your message here..." required></textarea>
      </div>
      <button type="submit">Send Message</button>
    </form>

    <div class="contact-info">
      <h3>Our Location</h3>
      <p>LA CREAMY CAKEIST Bakery, Kumasi - Ghana</p>
      <p>Phone: +233 246480618</p>
      <p>Email: info@mblstwrt@gmail.com</p>
      <p>Open Hours: Mon - Sat (8:00 AM - 8:00 PM)</p>

      <div class="social-media">
        <a href="https://facebook.com/lacreamycakeist" target="_blank">🌐 Facebook</a>
        <a href="https://instagram.com/lacreamycakeist" target="_blank">📸 Instagram</a>
        <a href="https://twitter.com/lacreamycakeist" target="_blank">🐦 Twitter</a>
        <a href="https://tiktok.com/@lacreamycakeist" target="_blank">🎵 TikTok</a>
      </div>
    </div>
  </section>

  <footer>
    <p>© 2025 LA CREAMY CAKEIST | All Rights Reserved</p>
  </footer>
</body>
</html>
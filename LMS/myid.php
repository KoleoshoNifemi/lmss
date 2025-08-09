
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>myID Login Options</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f4f4;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .login-container {
      width: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
    }
    .login-card {
      background: #fff;
      padding: 32px 24px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.08);
      text-align: center;
      min-width: 320px;
    }
    .login-heading {
      margin-bottom: 24px;
      color: #333;
    }
    .button-group {
      display: flex;
      flex-direction: column;
      gap: 16px;
      margin-bottom: 20px;
    }
    .login-btn {
      display: flex;
      align-items: center;
      justify-content: flex-start;
      gap: 16px;
      padding: 12px 20px;
      background:rgb(34, 176, 16);
      color: #fff;
      border: none;
      border-radius: 6px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.2s;
      text-align: left;
    }
    .login-btn:hover {
      background: #0056b3;
    }
    .btn-icon-rounded {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #fff;
      padding: 4px;
      object-fit: contain;
    }
    .support-link {
      font-size: 14px;
      color: #555;
    }
    .support-link a {
      color:rgb(13, 175, 48);
      text-decoration: none;
    }
    .support-link a:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <!-- Main Content -->
  <main class="login-container">
    <div class="login-card" id="login-card">
      <h2 class="login-heading">Choose Login Method</h2>
      <div class="button-group">
        <button class="login-btn" onclick="loginWithMyIDOnSameDevice()">
          <img src="images/myid-logo.png" alt="myID" class="btn-icon-rounded" />
          <div>
            <span><strong>myID on the same device</strong></span>
            <span style="color:rgb(80, 80, 190)"> <br>(Deep Link)</span>
          </div>
        </button>
        <button class="login-btn" onclick="generateQRCodeForAnotherDevice()">
          <img src="images/myid-logo.png" alt="myID" class="btn-icon-rounded" />
          <div>
            <span><strong>myID on a different device</strong></span>
            <span style="color:rgb(83, 83, 184)"> <br>(QR Code)</span>
          </div>
        </button>
      </div>
      <p class="support-link">
        Having trouble? <a href="#">Contact support</a>
      </p>
    </div>
  </main>
</body>
</html>
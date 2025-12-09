<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Session Expired</title>
<style>
    /* Reset */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: 'Arial', sans-serif;
        background: linear-gradient(to right, #f8f9fa, #e9ecef);
        height: 100vh;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .container {
        background-color: #fff;
        padding: 40px 30px;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        text-align: center;
        max-width: 90%;
        width: 400px;
        animation: fadeIn 0.8s ease-in-out;
    }

    .container h2 {
        color: #dc3545;
        font-size: 1.8rem;
        margin-bottom: 15px;
    }

    .container p {
        color: #6c757d;
        margin-bottom: 25px;
        font-size: 1rem;
    }

    .container a {
        text-decoration: none;
        color: #fff;
        background-color: #007bff;
        padding: 12px 25px;
        border-radius: 6px;
        font-weight: bold;
        display: inline-block;
        transition: background-color 0.3s ease;
    }

    .container a:hover {
        background-color: #0056b3;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Responsive adjustments */
    @media (max-width: 500px) {
        .container {
            padding: 30px 20px;
            width: 90%;
        }
        .container h2 {
            font-size: 1.5rem;
        }
        .container p {
            font-size: 0.95rem;
        }
        .container a {
            padding: 10px 20px;
            font-size: 0.95rem;
        }
    }
</style>
</head>
<body>

<div class="container">
    <h2>Session Expired!</h2>
    <p>Your session has timed out due to inactivity.</p>
    <a href="admin-login.php">Login Again</a>
</div>

</body>
</html>

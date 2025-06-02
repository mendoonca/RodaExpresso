<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Roda Expresso - Login</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #007bff;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                margin: 0;
            }
            .container {
                text-align: center;
                margin-bottom: 150px;
            }
            h1 {
                color: white;
                font-size: 50px;
                margin-bottom: 150px;
                font-style: italic;
                font-weight: bold;
            }
            .input-container {
                display: flex;
                align-items: center;
                background: white;
                padding: 10px;
                border-radius: 8px;
                margin: 15px auto;
                width: 450px;
                box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.2);
            }
            .input-container img {
                width: 25px;
                margin-right: 15px;
            }
            .input-container input {
                border: none;
                outline: none;
                font-size: 18px;
                width: 100%;
                background: transparent;
                padding: 5px 0;
            }
            .buttons {
                margin-top: 25px;
            }
            .btn {
                background-color: white;
                border: 2px solid #ffffff;
                color: #3498db;
                padding: 12px 25px;
                border-radius: 6px;
                font-size: 16px;
                font-weight: bold;
                cursor: pointer;
                margin: 5px;
                width: 150px;
                transition: 0.3s;
            }
            .btn:hover {
                background-color: #ffffff;
                color: #2980b9;
                border-color: #ffffff;
                transform: scale(1.03);
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h1>Roda Expresso</h1>
            <form action="login.php" method="POST">
                <div class="input-container">
                    <label for="email"><img src="../images/email.png"></label>
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </div>
                <div class="input-container">
                    <label for="password"><img src="../images/password.png"></label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="buttons">
                    <button type="submit" class="btn">Login</button>
                    <a href="PgRegisto.php"><button type="button" class="btn">Registar</button></a>
                </div>
            </form>
        </div>
    </body>
</html>
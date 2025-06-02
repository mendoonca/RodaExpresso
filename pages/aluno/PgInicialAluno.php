<?php
    session_start();

    // Verificar se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) {
        // Se não estiver autenticado, redirecionar para a página de login
        header("Location: ../PgLogin.php");
    }
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Roda Expresso</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 0;
                background-color: #f4f4f4;
            }
            .header {
                background-color: #007bff;
                color: white;
                padding: 20px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .header h1 {
                margin: 0;
                padding-left: 50px;
                font-weight: bold;
                font-style: italic;
                font-size: 36px;
            }
            .menu {
                display: flex;
                gap: 50px;
                padding-right: 150px;
                font-size: 25px;
            }
            .menu a {
                text-decoration: none;
                color: white;
            }
            .banner {
                background-color: #007bff;
                color: white;
                padding: 30px;
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            .banner-text {
                text-align: left;
                padding-left: 100px;
                font-size: 20px;
            }
            .banner img {
                width: 400px;
                height: auto;
                margin-right: 110px;
            }
            .section-title {
                text-align: left;
                padding-left: 70px;
                margin-top: 20px;
            }
            .ofertas {
                display: flex;
                justify-content: center;
                gap: 100px;
                padding: 20px;
            }
            .oferta {
                background-color: #007bff;
                padding: 20px;
                border-radius: 10px;
                box-shadow: 0 0 10px rgba(0,0,0,0.1);
                width: 300px;
                font-weight: bold;
                color:rgb(255, 255, 255);
            }
            .oferta img {
                width: 100%;
                height: 225px;
                border-radius: 10px;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <a href="PgInicialAluno.php" style="text-decoration: none; color: white;">
                <h1>Roda Expresso</h1>
            </a>
            <div class="menu">
                <a href="horarios.php">Horários</a>
                <a href="avaliacoes/avaliacoes.php">Avaliações</a>
                <a href="ofertas/ofertas.php">Ofertas</a>
                <a href="conta/conta.php">Conta</a>
            </div>
        </div>
        <div class="banner">
            <div class="banner-text">
                <h2>Somos Roda Nº1</h2>
                <p>Obrigado pela sua preferência e confiança! Juntos, estamos a<br>construir uma rede cada vez mais extensa, económica e sustentável.</p>
            </div>
            <img src="../../images/panda.png" alt="Carro">
        </div>
        <h2 class="section-title">Melhores ofertas do mês</h2>
        <div class="ofertas">
            <div class="oferta">
                <img src="../../images/peugeot.jpeg" alt="Carro 1">
                <p>Condutor: Miguel Martins</p>
            </div>
            <div class="oferta">
                <img src="../../images/fiat500.jpg" alt="Carro 2">
                <p>Condutor: João Pereira</p>
            </div>
            <div class="oferta">
                <img src="../../images/autocarro.jpg" alt="Carro 3">
                <p>Condutor: Sergio Conceição</p>
            </div>
        </div>
    </body>
</html>

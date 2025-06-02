<?php
    session_start();

    include '../../base_dados/basedados.h';
    include '../ConstUtilizadores.php';

    // Verificar se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) {
        // Se não estiver autenticado, redirecionar para a página de login
        header("Location: ../PgLogin.php");
    }
    
    // Verificar avaliações pendentes
    $pendentes = 0;
    
    $sql = "SELECT COUNT(*) as total FROM avaliacoes WHERE aprovado = '0'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $pendentes = $row['total'];
    }
    $conn->close();
?>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Roda Expresso</title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                position: relative;
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
                align-items: center;
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
            
            /* Estilos para o sino de notificações */
            .notification-icon {
                position: relative;
                cursor: pointer;
                font-size: 25px;
                margin-left: 20px;
            }
            
            .notification-badge {
                position: absolute;
                top: -10px;
                right: -10px;
                background-color: red;
                color: white;
                border-radius: 50%;
                padding: 2px 6px;
                font-size: 12px;
            }
            
            .notification-dropdown {
                display: none;
                position: absolute;
                right: 20px;
                top: 80px;
                background-color: white;
                border: 1px solid #ddd;
                border-radius: 5px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                width: 300px;
                z-index: 1000;
            }
            
            .notification-dropdown.show {
                display: block;
            }
            
            .notification-header {
                padding: 10px;
                border-bottom: 1px solid #ddd;
                font-weight: bold;
                background-color: #f8f9fa;
                color:rgb(0, 0, 0);
            }
            
            .notification-item {
                padding: 10px;
                border-bottom: 1px solid #eee;
                color:rgb(0, 0, 0);
            }
            
            .notification-item:last-child {
                border-bottom: none;
            }
            
            .view-all {
                display: block;
                text-align: center;
                padding: 10px;
                background-color: #f8f9fa;
                font-weight: bold;
                color: #007bff;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="header">
            <a href="PgInicialGestor.php" style="text-decoration: none; color: white;">
                <h1>Roda Expresso</h1>
            </a>
            <div class="menu">
                <a href="horarios/horarios.php">Horários</a>
                <a href="avaliacoes.php">Avaliações</a>
                <a href="dashboard.php">Dashboard</a>
                <a href="conta/conta.php">Conta</a>
                <div class="notification-icon" onclick="toggleNotifications()">
                    <i class="fas fa-bell"></i>
                    <?php if ($pendentes > 0): ?>
                        <span class="notification-badge"><?php echo $pendentes; ?></span>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="notification-dropdown" id="notificationDropdown">
                <div class="notification-header">Notificações</div>
                <?php if ($pendentes > 0): ?>
                    <div class="notification-item">
                        Tem <?php echo $pendentes; ?> nova(s) avaliação(ões) pendente(s) de revisão.
                    </div>
                    <a href="avaliacoes.php" class="view-all">Ver todas</a>
                <?php else: ?>
                    <div class="notification-item">Nenhuma nova notificação</div>
                <?php endif; ?>
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
        
        <script>
            function toggleNotifications() {
                const dropdown = document.getElementById('notificationDropdown');
                dropdown.classList.toggle('show');
            }
            
            // Fechar o dropdown quando clicar fora dele
            window.onclick = function(event) {
                if (!event.target.matches('.notification-icon') && !event.target.matches('.notification-icon *')) {
                    const dropdowns = document.getElementsByClassName("notification-dropdown");
                    for (let i = 0; i < dropdowns.length; i++) {
                        const openDropdown = dropdowns[i];
                        if (openDropdown.classList.contains('show')) {
                            openDropdown.classList.remove('show');
                        }
                    }
                }
            }
            
            // Atualizar notificações a cada 30 segundos
            setInterval(function() {
                fetch('check_avaliacoes_pendentes.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.count > 0) {
                            const badge = document.querySelector('.notification-badge');
                            if (badge) {
                                badge.textContent = data.count;
                            } else {
                                const icon = document.querySelector('.notification-icon');
                                icon.innerHTML = '<i class="fas fa-bell"></i><span class="notification-badge">' + data.count + '</span>';
                            }
                            
                            // Atualizar conteúdo do dropdown
                            const dropdown = document.getElementById('notificationDropdown');
                            dropdown.innerHTML = `
                                <div class="notification-header">Notificações</div>
                                <div class="notification-item">
                                    Você tem ${data.count} nova(s) avaliação(ões) pendente(s) de revisão.
                                </div>
                                <a href="avaliacoes.php" class="view-all">Ver todas</a>
                            `;
                        }
                    });
            }, 30000); // 30 segundos
        </script>
    </body>
</html>
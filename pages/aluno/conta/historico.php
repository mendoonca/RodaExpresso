<?php
    session_start();

    // Verificar se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) {
        // Se não estiver autenticado, redirecionar para a página de login
        header("Location: ../../PgLogin.php");
        exit();
    }

    // Incluir a configuração de conexão com a base de dados
    include '../../../base_dados/basedados.h';

    $user_id = $_SESSION['user_id'];

    // Consultar o histórico do utilizador (viagens e ofertas)
    $sql_historico = "SELECT h.id, h.tipo_evento, h.nome_completo, h.email, h.descricao, h.data, h.resultado
                      FROM historico h
                      WHERE h.user_id = ?
                      ORDER BY h.data DESC";
    $stmt = mysqli_prepare($conn, $sql_historico);
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result_historico = mysqli_stmt_get_result($stmt);

    if (!$result_historico) {
        echo "Erro ao consultar o histórico: " . mysqli_error($conn);
        exit();
    }
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico | Roda Expresso</title>
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
        .negrito {
            font-weight: bold;
            font-size: 26px;
        }
        .container {
            width: 85%;
            margin: auto;
            margin-top: 50px;
            display: flex;
            gap: 30px;
        }
        .painel-principal {
            flex: 1;
            background-color: #007bff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-direction: column;
        }
        .titulo-painel {
            color: white;
            font-size: 28px;
            margin-top: 0;
            margin-bottom: 25px;
            text-align: center;
            border-bottom: 2px solid white;
            padding-bottom: 10px;
        }
        .item-historico {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            text-align: center;
        }
        .item-historico:last-child {
            margin-bottom: 0;
        }
        .info-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            text-align: center;
        }
        .info-item p {
            margin: 0;
            font-size: 20px;
            color: #333;
            width: 100%;
            text-align: center;
        }
        .info-item {
            display: block;
        }
        .info-item .destaque {
            font-weight: bold;
            color: #007bff;
        }
        .sem-registros {
            color: white;
            text-align: center;
            font-size: 20px;
            padding: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="../PgInicialAluno.php" style="text-decoration: none; color: white;">
            <h1>Roda Expresso</h1>
        </a>
        <div class="menu">
            <a href="../horarios.php">Horários</a>
            <a href="../avaliacoes/avaliacoes.php">Avaliações</a>
            <a href="../ofertas/ofertas.php">Ofertas</a>
            <a href="../conta/conta.php">Conta</a>
        </div>
    </div>

    <div class="container">
        <!-- Painel de Viagens -->
        <div class="painel-principal">
            <h2 class="titulo-painel">HISTÓRICO DE VIAGENS E OFERTAS</h2>

            <?php
                // Verificar se há resultados no histórico
                if (mysqli_num_rows($result_historico) > 0) {
                    while ($historico = mysqli_fetch_assoc($result_historico)) {
                        // Exibir apenas viagens ou ofertas dependendo do tipo_evento
                        if ($historico['tipo_evento'] == 'Reserva') {
                            echo '<div class="item-historico">';
                            echo '<div class="info-item">';
                            echo '<p><span class="destaque">Condutor:</span> ' . htmlspecialchars($historico['nome_completo']) . '</p>';
                            echo '</div>';
                            echo '<div class="info-item">';
                            echo '<p><span class="destaque">Viagem:</span> ' . htmlspecialchars($historico['descricao']) . '</p>';
                            echo '</div>';
                            echo '<div class="info-item">';
                            echo '<p><span class="destaque">Data:</span> ' . htmlspecialchars($historico['data']) . '</p>';
                            echo '</div>';
                            echo '<div class="info-item">';
                            echo '<p><span class="destaque">Reserva:</span> ' . htmlspecialchars($historico['resultado']) . '</p>';
                            echo '</div>';
                            echo '</div>';
                        } elseif ($historico['tipo_evento'] == 'oferta') {
                            echo '<div class="item-historico">';
                            echo '<div class="info-item">';
                            echo '<p><span class="destaque">Hora:</span> ' . htmlspecialchars($historico['descricao']) . '</p>';
                            echo '</div>';
                            echo '<div class="info-item">';
                            echo '<p><span class="destaque">Data:</span> ' . htmlspecialchars($historico['data']) . '</p>';
                            echo '</div>';
                            echo '<div class="info-item">';
                            echo '<p><span class="destaque">Lugares:</span> ' . htmlspecialchars($historico['resultado']) . '</p>';
                            echo '</div>';
                            echo '</div>';
                        }
                    }
                } else {
                    echo '<div class="sem-registros">Ainda não há viagens ou ofertas registradas no seu histórico.</div>';
                }
            ?>
        </div>
    </div>
</body>
</html>
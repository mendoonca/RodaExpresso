<?php
    session_start();

    // Verificar se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) {
        // Se não estiver autenticado, redirecionar para a página de login
        header("Location: ../PgLogin.php");
    }

    // Incluir a configuração de conexão com a base de dados
    include '../../base_dados/basedados.h';

    // Buscar histórico do utilizador autenticado (ou todos, se preferires)
    // Aqui vou buscar o histórico do utilizador logado:
    $user_id = (int)$_SESSION['user_id'];

    // // Consulta SQL para obter os dados das aprovações
    // $sql = "
    // SELECT
    //     nome_completo,
    //     email,
    //     data,
    //     resultado
    // FROM historico
    // WHERE user_id = $user_id
    // ORDER BY data DESC, id DESC
    // ";

    $sql = "SELECT nome_completo, email, data, resultado FROM historico WHERE user_id = $user_id ORDER BY data DESC";
    $result = mysqli_query($conn, $sql);

    // Verificar se a consulta foi bem-sucedida
    if (!$result) {
        die("Erro na consulta: " . mysqli_error($conn));
    }
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Aprovações | Roda Expresso</title>
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
            font-size: 22px;
        }
        .container {
            width: 65%;
            margin: auto;
            margin-top: 50px;
        }
        .titulo-pagina {
            font-size: 32px;
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .item-historico {
            background-color: #007bff;
            border-radius: 8px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
            position: relative;
        }
        .info-usuario {
            flex: 1;
        }
        .info-usuario p {
            margin: 8px 0;
            font-size: 18px;
            color: white;
        }
        .info-usuario .nome {
            font-weight: bold;
            font-size: 22px;
            color: white;
        }
        .resultado {
            background-color: white;
            color: #007bff;
            padding: 15px 25px;
            border-radius: 5px;
            font-weight: bold;
            font-size: 20px;
            text-align: center;
            width: 215px;
            margin-left: 20px;
            border: 2px solid white;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .voltar {
            display: block;
            text-align: center;
            margin-top: 40px;
            margin-bottom: 60px;
        }
        .voltar a {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
            font-size: 18px;
            transition: background-color 0.3s;
        }
        .voltar a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="PgInicialAdministrador.php" style="text-decoration: none; color: white;">
            <h1>Roda Expresso</h1>
        </a>
        <div class="menu">
            <a href="aprovacaoRegisto.php">Aprovação de Registo</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="conta.php">Conta</a>
        </div>
    </div>

    <div class="container">
        <h1 class="titulo-pagina">Histórico de Aprovações</h1>
        
        <?php
            // Verificar se há resultados e exibir os itens do histórico
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    // Exibir cada item do histórico
                    echo '<div class="item-historico">';
                    echo '<div class="info-usuario">';
                    echo '<p><span class="negrito">Nome Completo:</span> ' . $row['nome_completo'] . '</p>';
                    echo '<p><span class="negrito">Email:</span> ' . $row['email'] . '</p>';
                    echo '<p><span class="negrito">Data:</span> ' . $row['data'] . '</p>';
                    echo '</div>';
                    echo '<div class="resultado">';
                    echo 'Resultado: ' . $row['resultado'];
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo '<p>Nenhum histórico encontrado.</p>';
            }

            // Fechar a conexão
            $conn->close();
        ?>
    </div>
</body>
</html>
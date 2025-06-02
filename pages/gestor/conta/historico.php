<?php
    session_start();

    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../PgLogin.php");
        exit();
    }

    // Incluir ligação à base de dados
    include '../../../base_dados/basedados.h';

    // Buscar histórico do utilizador autenticado (ou todos, se preferires)
    // Aqui vou buscar o histórico do utilizador logado:
    $user_id = (int)$_SESSION['user_id'];

    $query = "
    SELECT 
        tipo_evento,
        nome_completo,
        descricao,
        data,
        resultado
    FROM historico
    WHERE user_id = $user_id
    ORDER BY data DESC, id DESC
    ";

    $result = mysqli_query($conn, $query);
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
        .linha-nome-condutor {
            display: flex;
            gap: 100px;
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
        <a href="../PgInicialGestor.php" style="text-decoration: none; color: white;">
            <h1>Roda Expresso</h1>
        </a>
        <div class="menu">
            <a href="../horarios/horarios.php">Horários</a>
            <a href="../avaliacoes.php">Avaliações</a>
            <a href="../dashboard.php">Dashboard</a>
            <a href="conta.php">Conta</a>
        </div>
    </div>

    <div class="container">
        <h1 class="titulo-pagina">Histórico de Aprovações</h1>

        <?php
        if (mysqli_num_rows($result) === 0) {
            echo "<p>Não há histórico para mostrar.</p>";
        }

        while ($row = mysqli_fetch_assoc($result)) {
            // Formatar a data para formato português (ex: 27/05/2025)
            $data_formatada = date("d/m/Y", strtotime($row['data']));
        ?>
        <div class="item-historico">
            <div class="info-usuario">
                <p><span class="negrito">Evento:</span> <?php echo htmlspecialchars($row['tipo_evento']); ?></p>
                <p><span class="negrito">Nome Completo:</span> <?php echo htmlspecialchars($row['nome_completo']); ?></p>
                <p><span class="negrito">Descrição:</span> <?php echo nl2br(htmlspecialchars($row['descricao'])); ?></p>
                <p><span class="negrito">Data:</span> <?php echo $data_formatada; ?></p>
            </div>
            <div class="resultado">
                Resultado: <?php echo htmlspecialchars($row['resultado']); ?>
            </div>
        </div>
        <?php } ?>
    </div>
</body>
</html>
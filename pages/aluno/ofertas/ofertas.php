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

    // Consultar as ofertas da base de dados
    $sql_ofertas = "SELECT o.id_sugestao, o.origem, o.destino, o.data_hora, o.lugares_disponiveis, 
                       t.tipo_veiculo, t.matricula, c.nome_condutor, c.id_condutor, c.classificacao
                FROM ofertatransporte o
                JOIN transporte t ON o.id_transporte = t.id_transporte
                JOIN condutor c ON o.id_condutor = c.id_condutor
                WHERE o.lugares_disponiveis > 0";

    $result_ofertas = mysqli_query($conn, $sql_ofertas);
    if (!$result_ofertas) {
        echo "Erro ao consultar as ofertas: " . mysqli_error($conn);
        exit();
    }
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ofertas | Roda Expresso</title>
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
            width: 80%;
            margin: auto;
            margin-top: 50px;
        }
        .oferta-container {
            background-color: #007bff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 30px;
        }
        .oferta-header {
            display: flex;
            align-items: center;
            gap: 30px;
        }
        .oferta-header img {
            width: 400px;
            height: 250px;
            object-fit: cover;
            border: 3px solid white;
        }
        .info-condutor {
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: left;
        }
        .info-condutor p {
            font-size: 21px;
            margin-left: 50px;
            color: white;
        }
        .oferta-info {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: center;
        }
        .oferta-info h2 {
            margin: 0 0 10px 0;
            font-size: 28px;
        }
        .oferta-info p {
            margin: 8px 0;
            font-size: 20px;
            color: white;
        }
        .oferta-info .oferta {
            font-size: 22px;
        }
        .oferta-info .botao-servico {
            display: inline-block;
            padding: 12px 24px;
            margin-top: 15px;
            border: 2px solid white;
            color: white;
            background-color: transparent;
            font-size: 18px;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            align-self: center;
        }
        .nova-oferta {
            background-color: #007bff;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 290px;
            font-size: 100px;
            color: white;
            cursor: pointer;
        }
        .link-mais {
            color: inherit;
            text-decoration: none;
            font-size: inherit;
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
            <a href="ofertas.php" class="negrito">Ofertas</a>
            <a href="../conta/conta.php">Conta</a>
        </div>
    </div>

    <div class="container">
        <?php
            // Verificar se há ofertas para exibir
            if (mysqli_num_rows($result_ofertas) > 0) {
                // Exibir todas as ofertas
                while ($oferta = mysqli_fetch_assoc($result_ofertas)) {
                    $nome_condutor = $oferta['nome_condutor'];
                    $tipo_veiculo = $oferta['tipo_veiculo'];
                    $matricula = $oferta['matricula'];
                    $lugares_disponiveis = $oferta['lugares_disponiveis'];
                    $id_condutor = $oferta['id_condutor'];
                    $classificacao = $oferta['classificacao'];

                    $rating_display = $classificacao ? number_format($classificacao, 1) : 'N/A';

                    // Exibir a oferta
                    echo '
                    <div class="oferta-container">
                        <div class="oferta-header">
                            <img src="../../../images/veiculo.png" alt="Veículo">
                            <div class="info-condutor">
                                <h2>' . htmlspecialchars($nome_condutor) . '</h2>
                                <div class="linha-superior" style="display: flex; justify-content: space-between;">
                                    <p><span class="negrito">Veículo:</span> ' . htmlspecialchars($tipo_veiculo) . '</p>
                                    <p><span class="negrito">Matrícula:</span> ' . htmlspecialchars($matricula) . '</p>
                                </div>
                                <div class="linha-inferior" style="display: flex; justify-content: space-between;">
                                    <p><span class="negrito">Avaliação:</span> ' . htmlspecialchars($rating_display) . '/5.0</p>
                                </div>
                            </div>
                        </div>
                        <div class="oferta-info">
                            <p><strong>Lugares disponíveis:</strong> ' . htmlspecialchars($lugares_disponiveis) . '</p>
                            <button class="botao-servico">
                                <a href="servico.php?id_condutor=' . $id_condutor . '" style="text-decoration: none; color: inherit;">Serviço</a>
                            </button>
                        </div>
                    </div>';
                }
            }
        ?>

        <!-- Nova Oferta -->
        <div class="nova-oferta">
            <a href="criarOferta.php" class="link-mais">+</a>
        </div>
    </div>
</body>
</html>
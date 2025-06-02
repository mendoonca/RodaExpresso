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

    // Obter o ID da oferta ou condutor passado pela URL
    if (isset($_GET['id_condutor'])) {
        $id_condutor = $_GET['id_condutor'];
    } else {
        echo "ID do condutor não especificado.";
        exit();
    }

    // Consultar os detalhes do serviço com base no ID do condutor
    $sql_servico = "SELECT o.id_sugestao, o.origem, o.destino, o.data_hora, o.lugares_disponiveis, 
                        t.tipo_veiculo, t.matricula, c.nome_condutor, c.id_condutor, c.classificacao
                    FROM ofertatransporte o
                    JOIN transporte t ON o.id_transporte = t.id_transporte
                    JOIN condutor c ON o.id_condutor = c.id_condutor
                    WHERE c.id_condutor = ?";
    $stmt = mysqli_prepare($conn, $sql_servico);
    mysqli_stmt_bind_param($stmt, "i", $id_condutor);
    mysqli_stmt_execute($stmt);
    $result_servico = mysqli_stmt_get_result($stmt);

    if (!$result_servico) {
        echo "Erro ao consultar o serviço: " . mysqli_error($conn);
        exit();
    }

    $servico = mysqli_fetch_assoc($result_servico);
    if (!$servico) {
        echo "Serviço não encontrado.";
        exit();
    }

    // Extrair as variáveis
    $nome_condutor = $servico['nome_condutor'];  // Changed from nome_utilizador to nome_condutor
    $tipo_veiculo = $servico['tipo_veiculo'];
    $matricula = $servico['matricula'];
    $origem = $servico['origem'];
    $destino = $servico['destino'];
    $data_hora = $servico['data_hora'];
    $lugares_disponiveis = $servico['lugares_disponiveis'];
    $id_condutor = $servico['id_condutor'];
    $classificacao = $servico['classificacao'];

    $rating_display = $classificacao ? number_format($classificacao, 1) : 'N/A';
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Serviço | Roda Expresso</title>
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
            display: flex;
            gap: 40px;
        }
        .imagem-condutor {
            flex: 1;
        }
        .imagem-condutor img {
            width: 100%;
            height: auto;
            max-height: 600px;
            object-fit: cover;
            border-radius: 10px;
            border: 3px solid rgb(0, 0, 0);
        }
        .detalhes-servico {
            flex: 1;
            background-color: #007bff;
            padding: 30px;
            border-radius: 10px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .detalhes-servico h2 {
            font-size: 32px;
            margin-top: 0;
            margin-bottom: 30px;
            text-align: center;
        }
        .info-item {
            margin-bottom: 20px;
            font-size: 22px;
            display: flex;
            justify-content: space-between;
        }
        .info-label {
            font-weight: bold;
        }
        .botoes-acao {
            display: flex;
            justify-content: space-between;
            margin-top: 40px;
            gap: 20px;
        }
        .botao {
            padding: 15px 30px;
            font-size: 20px;
            border-radius: 5px;
            cursor: pointer;
            flex: 1;
            text-align: center;
            text-decoration: none;
            background-color: transparent;
            border: 2px solid white;
            color: white;
            transition: all 0.3s ease;
        }
        .botao:hover {
            background-color: rgba(255, 255, 255, 0.1);
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
        <div class="imagem-condutor">
            <img src="../../../images/veiculo.png" alt="Veículo">
        </div>
        
        <div class="detalhes-servico">
            <h2>Detalhes do Serviço</h2>
            
            <div class="info-item">
                <span class="info-label">Condutor:</span>
                <span><?php echo htmlspecialchars($nome_condutor); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Veículo:</span>
                <span><?php echo htmlspecialchars($tipo_veiculo); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Matrícula:</span>
                <span><?php echo htmlspecialchars($matricula); ?></span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Avaliação:</span>
                <span><?php echo htmlspecialchars($rating_display); ?>/5.0</span>
            </div>
            
            <div class="info-item">
                <span class="info-label">Horário:</span>
                <span><?php echo htmlspecialchars($data_hora); ?></span>
            </div>
            
            <form action="processar_reserva.php" method="POST">
            <div class="info-item">
                <span class="info-label">Escolher lugar(es):</span>
                <select name="lugares_reservados" id="lugares_reservados">
                    <?php
                        // Mostrar opções de 1 até o número de lugares disponíveis
                        for ($i = 1; $i <= $lugares_disponiveis; $i++) {
                            echo "<option value='$i'>$i lugar" . ($i > 1 ? "es" : "") . "</option>";
                        }
                    ?>
                </select>
            </div>
                
                <input type="hidden" name="id_servico" value="<?php echo htmlspecialchars($servico['id_sugestao']); ?>">
                <input type="hidden" name="id_condutor" value="<?php echo htmlspecialchars($id_condutor); ?>">
                <input type="hidden" name="data_hora" value="<?php echo htmlspecialchars($data_hora); ?>">
                <input type="hidden" name="origem" value="<?php echo htmlspecialchars($origem); ?>">
                <input type="hidden" name="destino" value="<?php echo htmlspecialchars($destino); ?>">
                <input type="hidden" name="tipo_veiculo" value="<?php echo htmlspecialchars($tipo_veiculo); ?>">
                <input type="hidden" name="matricula" value="<?php echo htmlspecialchars($matricula); ?>">

                <div class="botoes-acao">
                    <a href="ofertas.php" class="botao">Cancelar</a>
                    <button type="submit" class="botao">Confirmar</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

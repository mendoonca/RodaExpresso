<?php
    session_start();

    // Verificar se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) {
        // Se não estiver autenticado, redirecionar para a página de login
        header("Location: ../PgLogin.php");
        exit();
    }

    // Incluir a configuração de conexão com a base de dados
    include '../../base_dados/basedados.h';

    // Verificar se a conexão foi estabelecida
    if (!isset($conn) || !($conn instanceof mysqli)) {
        die("Erro: Conexão com o banco de dados não está disponível.");
    }

    // Data selecionada (por padrão, hoje)
    $dataSelecionada = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');
    
    // Obter o dia da semana (em português)
    $diasSemana = [
        'Sunday' => 'Domingo',
        'Monday' => 'Segunda',
        'Tuesday' => 'Terça',
        'Wednesday' => 'Quarta',
        'Thursday' => 'Quinta',
        'Friday' => 'Sexta',
        'Saturday' => 'Sábado'
    ];
    $diaSemanaIngles = date('l', strtotime($dataSelecionada));
    $diaSemana = $diasSemana[$diaSemanaIngles];
    
    // Consulta atualizada para usar a tabela condutor
    $query = "
        SELECT h.*, c.nome_condutor, c.classificacao 
        FROM horariostransporte h
        JOIN condutor c ON h.id_condutor = c.id_condutor
        WHERE h.data_viagem = ? OR h.dia_semana = ?
        ORDER BY h.hora_partida ASC
    ";
    
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        die("Erro na preparação da consulta: " . $conn->error);
    }
    
    $stmt->bind_param("ss", $dataSelecionada, $diaSemana);
    $stmt->execute();
    $result = $stmt->get_result();
    $horarios = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    $datasNavegacao = [];
    for ($i = -3; $i <= 3; $i++) {
        $data = date('Y-m-d', strtotime($dataSelecionada . " $i days"));
        $datasNavegacao[] = [
            'data' => $data,
            'dia_semana_abrev' => substr($diasSemana[date('l', strtotime($data))], 0, 3),
            'dia_mes' => date('d', strtotime($data)),
            'selecionado' => ($data == $dataSelecionada)
        ];
    }
    
    // Formatar mês e ano para exibição
    $mesAno = ucfirst(strtolower(date('F Y', strtotime($dataSelecionada))));
    $mesAno = str_replace(
        ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
        ['Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'],
        $mesAno
    );
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Roda Expresso</title>
    <style>
        /* Mantido o mesmo CSS que você forneceu */
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
        .dias-mes-container {
            background-color: #007bff;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 30px;
            border-radius: 10px;
        }
        .dias {
            display: flex;
            justify-content: center;
            gap: 100px;
            margin-top: 10px;
            font-size: 18px;
        }
        .dia {
            padding: 10px;
            background-color: #0056b3;
            border-radius: 5px;
            color: white;
            cursor: pointer;
        }
        .dia.selecionado {
            background-color: white;
            color: #007bff;
            font-weight: bold;
        }
        .retangulo-azul {
            background-color: #007bff;
            padding: 30px;
            border-radius: 10px;
            margin-top: 30px;
            margin-bottom: 30px;
        }
        .horario-container {
            background-color:rgb(0, 77, 160);
            color: white;
            padding: 50px;
            margin: 20px 0;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
        }
        .horario-info {
            font-size: 18px;
            font-style: italic;
        }
        .horario-detalhes {
            text-align: right;
            font-size: 20px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="PgInicialAluno.php" style="text-decoration: none; color: white;">
            <h1>Roda Expresso</h1>
        </a>
        <div class="menu">
            <a href="horarios.php" class="negrito">Horários</a>
            <a href="avaliacoes/avaliacoes.php">Avaliações</a>
            <a href="ofertas/ofertas.php">Ofertas</a>
            <a href="conta/conta.php">Conta</a>
        </div>
    </div>

    <div class="container">
        <div class="dias-mes-container">
            <p><strong><?php echo $mesAno; ?></strong></p>

            <div class="dias">
                <?php foreach ($datasNavegacao as $dataNav): ?>
                    <div class="dia <?php echo $dataNav['selecionado'] ? 'selecionado' : ''; ?>" 
                         onclick="window.location.href='?data=<?php echo $dataNav['data']; ?>'">
                        <?php echo $dataNav['dia_semana_abrev']; ?> <?php echo $dataNav['dia_mes']; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Retângulo azul envolvendo os horários -->
        <div class="retangulo-azul">
            <?php if (empty($horarios)): ?>
                <div class="horario-container">
                    <div class="horario-info">
                        <h2>Não há horários disponíveis</h2>
                        <p>Não foram encontrados horários para a data selecionada.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($horarios as $horario): ?>
                    <div class="horario-container">
                        <div class="horario-info">
                            <h2>Roda Expresso</h2>
                            <p><strong>Condutor:</strong> <?php echo htmlspecialchars($horario['nome_condutor']); ?></p>
                            <?php if (!empty($horario['origem']) && !empty($horario['destino'])): ?>
                                <p><strong>Rota:</strong> <?php echo htmlspecialchars($horario['origem']); ?> → <?php echo htmlspecialchars($horario['destino']); ?></p>
                            <?php endif; ?>
                        </div>
                        <div class="horario-detalhes">
                            <p><strong><?php echo date('H:i', strtotime($horario['hora_partida'])); ?> ➝ <?php echo date('H:i', strtotime($horario['hora_chegada'])); ?></strong></p>
                            <p><strong><?php echo $horario['lugares_disponiveis']; ?> lugares restantes</strong></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
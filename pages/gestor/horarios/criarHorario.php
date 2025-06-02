<?php
    session_start();

    // Incluir a configuração de conexão com a base de dados
    include '../../../base_dados/basedados.h';

    // Verificar se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../PgLogin.php");
        exit();
    }

    // Obter a data passada por parâmetro (se existir)
    $dataSelecionada = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');

    // Obter lista de condutores disponíveis
    $queryCondutores = "SELECT id_condutor, nome_condutor FROM condutor ORDER BY nome_condutor";
    $resultCondutores = mysqli_query($conn, $queryCondutores);
    if (!$resultCondutores) {
        die("Erro ao obter condutores: " . mysqli_error($conn));
    }
    $condutores = mysqli_fetch_all($resultCondutores, MYSQLI_ASSOC);

    // Processar o formulário quando for submetido
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Validar e sanitizar os dados de entrada
        $id_condutor = mysqli_real_escape_string($conn, $_POST['id_condutor']);
        $data_viagem = mysqli_real_escape_string($conn, $_POST['data_viagem']);
        $hora_partida = mysqli_real_escape_string($conn, $_POST['hora_partida']);
        $hora_chegada = mysqli_real_escape_string($conn, $_POST['hora_chegada']);
        $lugares_disponiveis = intval($_POST['lugares_disponiveis']);
        $origem = mysqli_real_escape_string($conn, $_POST['origem']);
        $destino = mysqli_real_escape_string($conn, $_POST['destino']);

        // Validar os dados
        if (empty($id_condutor) || empty($data_viagem) || empty($hora_partida) || empty($hora_chegada)) {
            $erro = "Por favor, preencha todos os campos obrigatórios!";
        } else {
            // Inserir o novo horário na base de dados
            $query = "INSERT INTO horariostransporte 
                    (id_condutor, data_viagem, hora_partida, hora_chegada, lugares_disponiveis, origem, destino) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conn, $query);
            if (!$stmt) {
                die("Erro na preparação da consulta: " . mysqli_error($conn) . "<br>Consulta: " . $query);
            }
            
            if (!mysqli_stmt_bind_param($stmt, "isssiss", $id_condutor, $data_viagem, $hora_partida, $hora_chegada, $lugares_disponiveis, $origem, $destino)) {
                die("Erro ao vincular parâmetros: " . mysqli_stmt_error($stmt));
            }
            
            if (mysqli_stmt_execute($stmt)) {
                // Redirecionar para a página de horários com mensagem de sucesso
                header("Location: horarios.php?data=$data_viagem&sucesso=1");
                exit();
            } else {
                $erro = "Erro ao criar horário: " . mysqli_stmt_error($stmt);
            }
        }
    }
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Novo Horário - Roda Expresso</title>
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
        .form-container {
            background-color: #007bff;
            padding: 30px;
            border-radius: 10px;
            color: white;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            border-radius: 5px;
            border: none;
        }
        .btn-submit {
            background-color: white;
            color: #007bff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: bold;
            font-size: 16px;
        }
        .btn-submit:hover {
            background-color: #f0f0f0;
        }
        .erro {
            color: #ffcccc;
            margin-bottom: 20px;
        }
        .voltar-link {
            display: inline-block;
            margin-top: 20px;
            color: white;
            text-decoration: none;
        }
        .voltar-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="../PgInicialGestor.php" style="text-decoration: none; color: white;">
            <h1>Roda Expresso</h1>
        </a>
        <div class="menu">
            <a href="horarios.php" class="negrito">Horários</a>
            <a href="../avaliacoes.php">Avaliações</a>
            <a href="../dashboard.php">Dashboard</a>
            <a href="../conta/conta.php">Conta</a>
        </div>
    </div>

    <div class="container">
        <div class="form-container">
            <h2>Criar Novo Horário</h2>
            
            <?php if (isset($erro)): ?>
                <div class="erro"><?php echo $erro; ?></div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="id_condutor">Condutor:</label>
                    <select id="id_condutor" name="id_condutor" required>
                        <option value="">Selecione um condutor</option>
                        <?php foreach ($condutores as $condutor): ?>
                            <option value="<?php echo $condutor['id_condutor']; ?>">
                                <?php echo htmlspecialchars($condutor['nome_condutor']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="data_viagem">Data da Viagem:</label>
                    <input type="date" id="data_viagem" name="data_viagem" 
                           value="<?php echo htmlspecialchars($dataSelecionada); ?>" required>
                </div>

                <div class="form-group">
                    <label for="hora_partida">Hora de Partida:</label>
                    <input type="time" id="hora_partida" name="hora_partida" required>
                </div>

                <div class="form-group">
                    <label for="hora_chegada">Hora de Chegada:</label>
                    <input type="time" id="hora_chegada" name="hora_chegada" required>
                </div>

                <div class="form-group">
                    <label for="lugares_disponiveis">Lugares Disponíveis:</label>
                    <input type="number" id="lugares_disponiveis" name="lugares_disponiveis" 
                           min="1" value="4" required>
                </div>

                <div class="form-group">
                    <label for="origem">Origem:</label>
                    <input type="text" id="origem" name="origem" placeholder="Local de partida">
                </div>

                <div class="form-group">
                    <label for="destino">Destino:</label>
                    <input type="text" id="destino" name="destino" placeholder="Local de chegada">
                </div>

                <button type="submit" class="btn-submit">Criar Horário</button>
            </form>

            <a href="horarios.php?data=<?php echo $dataSelecionada; ?>" class="voltar-link">
                ← Voltar para a lista de horários
            </a>
        </div>
    </div>

    <script>
        // Validação adicional no cliente
        document.querySelector('form').addEventListener('submit', function(e) {
            const horaPartida = document.getElementById('hora_partida').value;
            const horaChegada = document.getElementById('hora_chegada').value;
            
            if (horaPartida >= horaChegada) {
                alert('A hora de chegada deve ser posterior à hora de partida!');
                e.preventDefault();
            }
        });
    </script>
</body>
</html>
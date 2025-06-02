<?php
session_start();
include '../../../base_dados/basedados.h';

// Verificar se o utilizador está autenticado
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../PgLogin.php");
    exit();
}

// Verificar se o ID foi passado
if (!isset($_GET['id_horario'])) {
    die("ID do horário não fornecido.");
}

$idHorario = $_GET['id_horario'];

// Se foi feito um POST, atualizar o horário
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $novaData = $_POST['data'];
    $novaHoraPartida = $_POST['hora_partida'];
    $novaHoraChegada = $_POST['hora_chegada'];
    $novosLugares = $_POST['lugares'];

    $updateQuery = "UPDATE horariostransporte 
                SET data_viagem = ?, hora_partida = ?, hora_chegada = ?, lugares_disponiveis = ? 
                WHERE id_horario = ?";

    $stmtUpdate = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($stmtUpdate, "sssii", $novaData, $novaHoraPartida, $novaHoraChegada, $novosLugares, $idHorario);

    if (mysqli_stmt_execute($stmtUpdate)) {
        header("Location: horarios.php");
        exit();
    } else {
        echo "Erro ao atualizar horário: " . mysqli_error($conn);
    }
}

// Buscar os dados do horário atual
$query = "SELECT h.*, c.nome_condutor 
          FROM horariostransporte h 
          JOIN condutor c ON h.id_condutor = c.id_condutor
          WHERE h.id_horario = ?";

$stmt = mysqli_prepare($conn, $query);
if (!$stmt) {
    die("Erro na preparação da consulta: " . mysqli_error($conn));
}
mysqli_stmt_bind_param($stmt, "i", $idHorario);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$horario = mysqli_fetch_assoc($result);

if (!$horario) {
    die("Horário não encontrado.");
}
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Horário - Roda Expresso</title>
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
        .container {
            width: 30%;
            margin: auto;
            margin-top: 50px;
        }
        .editar-horario-container {
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
            font-size: 18px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .form-group input, .form-group select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border-radius: 5px;
            border: none;
            box-sizing: border-box;
            height: 40px;
        }
        .botoes {
            display: flex;
            justify-content: center;
            gap: 20px;
            margin-top: 30px;
        }
        .botao {
            padding: 12px 30px;
            font-size: 18px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            font-weight: bold;
            background-color: white;
            color: #007bff;
            border: 2px solid white;
            flex: 1;
            max-width: 150px;
            text-align: center;
        }
        .botao:hover {
            opacity: 0.9;
        }
        .titulo-edicao {
            font-size: 34px;
            margin-bottom: 30px;
            text-align: center;
            font-weight: bold;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="../PgInicialGestor.php" style="text-decoration: none; color: white;">
            <h1>Roda Expresso</h1>
        </a>
        <div class="menu">
            <a href="horarios.php">Horários</a>
            <a href="../avaliacoes.php">Avaliações</a>
            <a href="../dashboard.php">Dashboard</a>
            <a href="../conta/conta.php">Conta</a>
        </div>
    </div>

    <div class="container">
        <div class="editar-horario-container">
            <div class="titulo-edicao">Editar Horário</div>

            <form method="POST">
                <div class="form-group">
                    <label for="condutor">Condutor:</label>
                    <input type="text" id="condutor" value="<?= htmlspecialchars($horario['nome_condutor']) ?>" disabled>
                </div>

                <div class="form-group">
                    <label for="hora_partida">Hora de Partida:</label>
                    <input type="time" id="hora_partida" name="hora_partida" value="<?= htmlspecialchars(substr($horario['hora_partida'], 0, 5)) ?>" required>
                </div>

                <div class="form-group">
                    <label for="hora_chegada">Hora de Chegada:</label>
                    <input type="time" id="hora_chegada" name="hora_chegada" value="<?= htmlspecialchars(substr($horario['hora_chegada'], 0, 5)) ?>" required>
                </div>

                <div class="form-group">
                    <label for="lugares">Lugares:</label>
                    <select id="lugares" name="lugares">
                        <?php for ($i = 1; $i <= 14; $i++): ?>
                            <option value="<?= $i ?>" <?= $horario['lugares_disponiveis'] == $i ? 'selected' : '' ?>>
                                <?= $i ?> Lugar<?= $i > 1 ? 'es' : '' ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="data">Data:</label>
                    <input type="date" id="data" name="data" value="<?= htmlspecialchars($horario['data_viagem']) ?>" required>
                </div>

                <div class="botoes">
                    <button type="submit" class="botao botao-salvar">Salvar</button>
                    <button type="button" class="botao botao-cancelar">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.querySelector('.botao-cancelar').addEventListener('click', function () {
            if (confirm('Tem certeza que deseja cancelar as alterações?')) {
                window.location.href = 'horarios.php';
            }
        });
    </script>
</body>
</html>

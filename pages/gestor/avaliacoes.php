<?php
session_start();

include '../../base_dados/basedados.h';
include '../ConstUtilizadores.php';

// Verificar se o utilizador está autenticado e é gestor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != GESTOR) {
    header("Location: ../PgLogin.php");
    exit();
}

// Processar ações de aprovar/recusar
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['acao']) && isset($_POST['id_avaliacao'])) {
        $id_avaliacao = intval($_POST['id_avaliacao']);
        $acao = $_POST['acao'];

        // Aprovar (1) ou recusar (2) a avaliação
        $novo_status = ($acao == 'aprovar') ? 1 : -1;

        // Atualizar a avaliação
        $stmt = mysqli_prepare($conn, "UPDATE avaliacoes SET aprovado = ? WHERE id_avaliacao = ?");
        mysqli_stmt_bind_param($stmt, "ii", $novo_status, $id_avaliacao);

        if (!mysqli_stmt_execute($stmt)) {
            die("Erro ao atualizar avaliação: " . mysqli_error($conn));
        }

        // Obter informações da avaliação para registar no histórico
        $queryInfo = "
            SELECT a.id_utilizador, a.id_condutor, a.nota, a.comentario, u.nome_utilizador, c.nome_condutor
            FROM avaliacoes a
            JOIN utilizador u ON a.id_utilizador = u.id_utilizador
            JOIN condutor c ON a.id_condutor = c.id_condutor
            WHERE a.id_avaliacao = ?
        ";
        $stmtInfo = mysqli_prepare($conn, $queryInfo);
        mysqli_stmt_bind_param($stmtInfo, "i", $id_avaliacao);
        mysqli_stmt_execute($stmtInfo);
        $resultInfo = mysqli_stmt_get_result($stmtInfo);
        $avaliacaoInfo = mysqli_fetch_assoc($resultInfo);

        if ($avaliacaoInfo) {
            $user_id = (int)$_SESSION['user_id']; // ID do gestor que faz a alteração
            $tipo_evento = 'Aprovação de Avaliação';
            $nome_completo = $avaliacaoInfo['nome_utilizador'];
            $email = ''; // Se tiver email no utilizador, podes ir buscar (não está no select)
            $descricao = "Avaliação do condutor " . $avaliacaoInfo['nome_condutor'] . 
                         " com nota " . $avaliacaoInfo['nota'];
            $data = date('Y-m-d');
            $resultado = ($novo_status == 1) ? 'Aprovado' : 'Reprovado';

            // Inserir no histórico
            $stmtHist = mysqli_prepare($conn, "
                INSERT INTO historico (user_id, tipo_evento, nome_completo, email, descricao, data, resultado)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            mysqli_stmt_bind_param($stmtHist, "issssss", $user_id, $tipo_evento, $nome_completo, $email, $descricao, $data, $resultado);
            mysqli_stmt_execute($stmtHist);
        }

        // Redirecionar para evitar reenvio do formulário
        header("Location: avaliacoes.php");
        exit();
    }
}

// Consultar avaliações pendentes (aprovado = 0) com informações dos utilizadores e condutores
$sql = "SELECT a.id_avaliacao, a.id_utilizador, a.id_condutor, a.nota, a.comentario, 
               u.nome_utilizador AS nome_utilizador, c.nome_condutor AS nome_condutor
        FROM avaliacoes a
        JOIN utilizador u ON a.id_utilizador = u.id_utilizador
        JOIN condutor c ON a.id_condutor = c.id_condutor
        WHERE a.aprovado = 0";

$resultado = mysqli_query($conn, $sql);

if (!$resultado) {
    die("Erro ao consultar avaliações: " . mysqli_error($conn));
}

$avaliacoes_pendentes = mysqli_fetch_all($resultado, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avaliações Pendentes | Roda Expresso</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* Mantém o teu CSS original */
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
        .registro-container {
            background-color: #007bff;
            padding: 40px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            color: white;
        }
        .registro-header {
            display: flex;
            align-items: flex-start;
            gap: 30px;
            position: relative;
        }
        .registro-header img {
            width: 200px;
            height: 200px;
            object-fit: cover;
            border: 3px solid white;
        }
        .info-utilizador {
            display: flex;
            flex-direction: column;
            justify-content: center;
            text-align: left;
            color: white;
            flex-grow: 1;
        }
        .info-utilizador p {
            margin: 0 0 10px 0;
            font-size: 26px;
            font-weight: normal;
        }
        .nome-condutor {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }
        .nome-condutor p:last-child {
            margin-right: 25%;
        }
        .botoes-acao {
            display: flex;
            gap: 100px;
            justify-content: center;
            width: 100%;
            position: absolute;
            bottom: 0;
            left: 0;
        }
        .botao-acao {
            padding: 12px 24px;
            border: 2px solid white;
            color: white;
            background-color: transparent;
            font-size: 18px;
            cursor: pointer;
            border-radius: 20px;
            text-align: center;
            text-decoration: none;
            white-space: nowrap;
        }
        .botao-acao:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .content-wrapper {
            position: relative;
            width: 100%;
            padding-bottom: 80px;
        }
        form {
            display: inline;
        }
        .sem-avaliacoes {
            text-align: center;
            font-size: 24px;
            color: #333;
            margin-top: 50px;
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
            <a href="avaliacoes.php" class="negrito">Avaliações</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="conta/conta.php">Conta</a>
        </div>
    </div>

    <div class="container">
        <?php if (empty($avaliacoes_pendentes)): ?>
            <div class="sem-avaliacoes">
                <p>Não há avaliações pendentes de aprovação.</p>
            </div>
        <?php else: ?>
            <?php foreach ($avaliacoes_pendentes as $avaliacao): ?>
                <div class="registro-container">
                    <div class="registro-header">
                        <img src="../../images/homem.png" alt="Foto do utilizador">
                        <div class="content-wrapper">
                            <div class="info-utilizador">
                                <div class="nome-condutor">
                                    <p><span class="negrito">Nome:</span> <?php echo htmlspecialchars($avaliacao['nome_utilizador']); ?></p>
                                    <p><span class="negrito">Condutor:</span> <?php echo htmlspecialchars($avaliacao['nome_condutor']); ?></p>
                                </div>
                                <p><span class="negrito">Avaliação:</span> <?php echo htmlspecialchars($avaliacao['nota']); ?></p>
                                <p><span class="negrito">Comentário:</span> <?php echo nl2br(htmlspecialchars($avaliacao['comentario'])); ?></p>
                            </div>
                            <div class="botoes-acao">
                                <form method="post" action="avaliacoes.php">
                                    <input type="hidden" name="id_avaliacao" value="<?php echo $avaliacao['id_avaliacao']; ?>">
                                    <input type="hidden" name="acao" value="aprovar">
                                    <button type="submit" class="botao-acao">Aprovar</button>
                                </form>
                                <form method="post" action="avaliacoes.php">
                                    <input type="hidden" name="id_avaliacao" value="<?php echo $avaliacao['id_avaliacao']; ?>">
                                    <input type="hidden" name="acao" value="recusar">
                                    <button type="submit" class="botao-acao">Recusar</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</body>
</html>
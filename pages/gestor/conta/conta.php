<?php
    session_start();
    include '../../../base_dados/basedados.h'; // ligação à base de dados

    // Verificar se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../../PgLogin.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Atualizar dados caso o formulário seja submetido
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        if (isset($_POST['idade'])) {
            $nova_idade = intval($_POST['idade']);
            mysqli_query($conn, "UPDATE Utilizador SET idade = $nova_idade WHERE id_utilizador = $user_id");
        }
        if (isset($_POST['telefone'])) {
            $novo_telefone = mysqli_real_escape_string($conn, $_POST['telefone']);
            mysqli_query($conn, "UPDATE Utilizador SET telefone = '$novo_telefone' WHERE id_utilizador = $user_id");
        }
        if (isset($_POST['email'])) {
            $novo_email = mysqli_real_escape_string($conn, $_POST['email']);
            mysqli_query($conn, "UPDATE Utilizador SET email = '$novo_email' WHERE id_utilizador = $user_id");
        }
    }

    // Obter dados do utilizador autenticado
    $sql = "SELECT nome_utilizador, idade, telefone, email FROM Utilizador WHERE id_utilizador = $user_id";
    $resultado = mysqli_query($conn, $sql);

    if (!$resultado || mysqli_num_rows($resultado) == 0) {
        echo "Erro ao obter dados do utilizador.";
        exit;
    }

    $dados = mysqli_fetch_assoc($resultado);
    $nome = $dados['nome_utilizador'];
    $idade = $dados['idade'] ?? '??';
    $telefone = $dados['telefone'] ?? 'Não definido';
    $email = $dados['email'];
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conta | Roda Expresso</title>
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
        .conta-container {
            background-color: #007bff;
            padding: 40px;
            border-radius: 10px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 50px;
        }
        .foto-perfil {
            width: 300px;
            height: 300px;
            object-fit: cover;
            border: 5px solid white;
            border-radius: 10px;
        }
        .info-utilizador {
            flex-grow: 1;
            color: white;
        }
        .info-utilizador h2 {
            font-size: 36px;
            margin-top: 0;
            margin-bottom: 30px;
        }
        .detalhes-utilizador {
            font-size: 24px;
            line-height: 1.6;
        }
        .detalhes-utilizador p {
            margin: 15px 0;
        }
        .botao-historico {
            display: block;
            width: 300px;
            padding: 15px;
            margin: 40px auto 0;
            border: 2px solid white;
            color: white;
            background-color: transparent;
            font-size: 22px;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
        }
        .botao-historico:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        .botao-logout {
            display: block;
            width: 300px;
            padding: 15px;
            margin: 20px auto;
            border: 2px solid white;
            color: white;
            background-color: transparent;
            font-size: 22px;
            cursor: pointer;
            border-radius: 5px;
            text-align: center;
            text-decoration: none;
        }
        .botao-logout:hover {
            background-color: rgba(255, 255, 255, 0.1);
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
                <a href="conta.php" class="negrito">Conta</a>
        </div>
    </div>

    <div class="container">
        <div class="conta-container">
            <img src="../../../images/homem.png" alt="Foto do Utilizador" class="foto-perfil">
            
            <div class="info-utilizador">
                <h2><?php echo htmlspecialchars($nome); ?></h2>

                <div class="detalhes-utilizador">
                    <form method="POST" style="display:inline;">
                        <p>
                            <span class="negrito">Idade:</span>
                            <span id="idade_texto"><?php echo $idade ? $idade . ' anos' : 'Não definida'; ?></span>
                            <input type="number" name="idade" id="idade_input" value="<?php echo htmlspecialchars($idade); ?>" style="display:none;" min="0">
                            <button type="button" onclick="editarCampo('idade')" id="btn_editar_idade">✏️</button>
                            <button type="submit" style="display:none;" id="btn_guardar_idade">💾</button>
                            <button type="button" onclick="cancelarEdicao('idade')" style="display:none;" id="btn_cancelar_idade">❌</button>
                        </p>
                    </form>

                    <form method="POST" style="display:inline;">
                        <p>
                            <span class="negrito">Telefone:</span>
                            <span id="telefone_texto"><?php echo htmlspecialchars($telefone); ?></span>
                            <input type="text" name="telefone" id="telefone_input" value="<?php echo htmlspecialchars($telefone); ?>" style="display:none;">
                            <button type="button" onclick="editarCampo('telefone')" id="btn_editar_telefone">✏️</button>
                            <button type="submit" style="display:none;" id="btn_guardar_telefone">💾</button>
                            <button type="button" onclick="cancelarEdicao('telefone')" style="display:none;" id="btn_cancelar_telefone">❌</button>
                        </p>
                    </form>

                    <form method="POST" style="display:inline;">
                        <p>
                            <span class="negrito">Email:</span>
                            <span id="email_texto"><?php echo htmlspecialchars($email); ?></span>
                            <input type="email" name="email" id="email_input" value="<?php echo htmlspecialchars($email); ?>" style="display:none;">
                            <button type="button" onclick="editarCampo('email')" id="btn_editar_email">✏️</button>
                            <button type="submit" style="display:none;" id="btn_guardar_email">💾</button>
                            <button type="button" onclick="cancelarEdicao('email')" style="display:none;" id="btn_cancelar_email">❌</button>
                        </p>
                    </form>
                </div>

                <a href="historico.php" class="botao-historico">Ver Histórico</a>
                <a href="../../logout.php" class="botao-logout">Terminar Sessão</a>
            </div>
        </div>
    </div>

    <script>
        function editarCampo(campo) {
            document.getElementById(`${campo}_texto`).style.display = 'none';
            document.getElementById(`${campo}_input`).style.display = 'inline';
            document.getElementById(`btn_editar_${campo}`).style.display = 'none';
            document.getElementById(`btn_guardar_${campo}`).style.display = 'inline';
            document.getElementById(`btn_cancelar_${campo}`).style.display = 'inline';
        }

        function cancelarEdicao(campo) {
            document.getElementById(`${campo}_texto`).style.display = 'inline';
            document.getElementById(`${campo}_input`).style.display = 'none';
            document.getElementById(`btn_editar_${campo}`).style.display = 'inline';
            document.getElementById(`btn_guardar_${campo}`).style.display = 'none';
            document.getElementById(`btn_cancelar_${campo}`).style.display = 'none';
        }
    </script>

</body>
</html>
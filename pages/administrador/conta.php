<?php
    session_start();
    include '../../base_dados/basedados.h'; // ligação à BD

    // Verificar se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../PgLogin.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nova_idade = isset($_POST['idade']) ? intval($_POST['idade']) : null;
        $novo_telefone = isset($_POST['telefone']) ? mysqli_real_escape_string($conn, $_POST['telefone']) : null;
    
        if ($nova_idade !== null) {
            mysqli_query($conn, "UPDATE Utilizador SET idade = $nova_idade WHERE id_utilizador = $user_id");
            $idade = $nova_idade;
        }
        if ($novo_telefone !== null) {
            mysqli_query($conn, "UPDATE Utilizador SET telefone = '$novo_telefone' WHERE id_utilizador = $user_id");
            $telefone = $novo_telefone;
        }
    }

    // Ir buscar dados do utilizador autenticado
    $user_id = $_SESSION['user_id'];

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
        <a href="PgInicialAdministrador.php" style="text-decoration: none; color: white;">
            <h1>Roda Expresso</h1>
        </a>
        <div class="menu">
            <a href="aprovacaoRegisto.php">Aprovação de Registo</a>
            <a href="dashboard.php">Dashboard</a>
            <a href="conta.php" class="negrito">Conta</a>
        </div>
    </div>

    <div class="container">
        <div class="conta-container">
            <img src="../../images/homem.png" alt="Foto do Utilizador" class="foto-perfil">
            
            <div class="info-utilizador">
                <h2><?php echo htmlspecialchars($nome); ?></h2>

                <div class="detalhes-utilizador">
                    <!-- Idade -->
                    <form method="POST" id="form_idade" style="display:inline;">
                        <p>
                            <span class="negrito">Idade:</span>
                            <span id="idade_texto"><?php echo $idade ? $idade . ' anos' : 'Não definido'; ?></span>
                            <input type="number" name="idade" id="idade_input" value="<?php echo htmlspecialchars($idade); ?>" style="display:none;" min="0">
                            <button type="button" onclick="editarCampo('idade')" id="btn_editar_idade">✏️</button>
                            <button type="submit" style="display:none;" id="btn_guardar_idade">💾</button>
                            <button type="button" style="display:none;" id="btn_cancelar_idade" onclick="cancelarEdicao('idade')">❌</button>
                        </p>
                    </form>

                    <!-- Telefone -->
                    <form method="POST" id="form_telefone" style="display:inline;">
                        <p>
                            <span class="negrito">Telefone:</span>
                            <span id="telefone_texto"><?php echo htmlspecialchars($telefone); ?></span>
                            <input type="text" name="telefone" id="telefone_input" value="<?php echo htmlspecialchars($telefone); ?>" style="display:none;">
                            <button type="button" onclick="editarCampo('telefone')" id="btn_editar_telefone">✏️</button>
                            <button type="submit" style="display:none;" id="btn_guardar_telefone">💾</button>
                            <button type="button" style="display:none;" id="btn_cancelar_telefone" onclick="cancelarEdicao('telefone')">❌</button>
                        </p>
                    </form>

                    <p><span class="negrito">Email:</span> <?php echo htmlspecialchars($email); ?></p>
                </div>
                
                <a href="historico.php" class="botao-historico">Ver Histórico</a>
                <a href="../logout.php" class="botao-logout">Terminar Sessão</a>
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
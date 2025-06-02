<?php
    session_start();
    include '../base_dados/basedados.h'; // ligação à base de dados

    // Verificar se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) {
        header("Location: PgLogin.php");
        exit;
    }

    $user_id = $_SESSION['user_id'];

    // Obter dados do utilizador autenticado
    $sql = "SELECT nome_utilizador, idade, telefone, email, tipo_utilizador FROM Utilizador WHERE id_utilizador = $user_id";
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
    $tipo_utilizador = $dados['tipo_utilizador'];

    // Exibir a notificação de registo completo
    if (isset($_SESSION['registo_completo'])) {
        echo "<script>alert('Registo concluído com sucesso! Faça login para saber se o seu registo foi validado.');</script>";
        unset($_SESSION['registo_completo']); // Limpar a variável de sessão após exibir o alerta
    }

    // Verificar se o utilizador foi aprovado
    if ($tipo_utilizador == 1) { // 1 é o código para "UTILIZADOR" aprovado
        echo "<script>alert('Registo aprovado! Você agora pode aceder à sua conta.'); window.location.href='aluno/PgInicialAluno.php';</script>";
        exit;
    }
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
        <a href="" style="text-decoration: none; color: white;">
            <h1>Roda Expresso</h1>
        </a>
        <div class="menu">
            <a href="" class="negrito">Conta</a>
        </div>
    </div>

    <div class="container">
        <div class="conta-container">
            <img src="../images/homem.png" alt="Foto do Utilizador" class="foto-perfil">
            
            <div class="info-utilizador">
                <h2><?php echo htmlspecialchars($nome); ?></h2>

                <div class="detalhes-utilizador">
                    <p><span class="negrito">Nº utilizador:</span> <?php echo $user_id; ?></p>

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

                    <p><span class="negrito">Email:</span> <?php echo htmlspecialchars($email); ?></p>
                </div>

                <a href="logout.php" class="botao-logout">Terminar Sessão</a>
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
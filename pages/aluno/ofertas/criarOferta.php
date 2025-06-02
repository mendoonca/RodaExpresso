<?php
    session_start();

    // Verificar se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) {
        // Se não estiver autenticado, redirecionar para a página de login
        header("Location: ../../PgLogin.php");
    }
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Oferta | Roda Expresso</title>
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
        .imagem-veiculo {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .imagem-veiculo img {
            width: 80%;
            height: auto;
            max-height: 500px;
            object-fit: cover;
            border-radius: 10px;
            border: 3px solid rgb(0, 0, 0);
            margin-bottom: 20px;
        }
        .upload-btn {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 18px;
        }
        .detalhes-oferta {
            flex: 1;
            background-color: #007bff;
            padding: 30px;
            border-radius: 10px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .detalhes-oferta h2 {
            font-size: 32px;
            margin-top: 0;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            font-size: 22px;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .form-group input, 
        .form-group select {
            width: 100%;
            padding: 12px;
            font-size: 18px;
            border-radius: 5px;
            border: none;
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
        <div class="imagem-veiculo">
            <img src="../../../images/veiculo.png" alt="Imagem do Veículo" id="preview-img">
            <button class="upload-btn" onclick="document.getElementById('file-input').click()">Carregar Imagem</button>
            <input type="file" id="file-input" style="display:none" accept="image/*" onchange="previewImage(this)">
        </div>
        
        <div class="detalhes-oferta">
            <h2>Criar Oferta</h2>
            
            <form action="criar_oferta.php" method="POST">
                <div class="form-group">
                    <label for="veiculo">Veículo</label>
                    <input type="text" id="veiculo" name="veiculo" placeholder="Ex: Honda Civic" required>
                </div>
                
                <div class="form-group">
                    <label for="matricula">Matrícula</label>
                    <input type="text" id="matricula" name="matricula" placeholder="Ex: 44-29-UC" required>
                </div>
                
                <div class="form-group">
                    <label for="horario">Horário</label>
                    <input type="time" id="horario" name="horario" required>
                </div>
                
                <div class="form-group">
                    <label for="data">Data</label>
                    <input type="date" id="data" name="data" required>
                </div>
                
                <div class="form-group">
                    <label for="lugares">Lugares disponíveis</label>
                    <select id="lugares" name="lugares" required>
                        <option value="1">1</option>
                        <option value="2">2</option>
                        <option value="3">3</option>
                        <option value="4">4</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="origem">Origem</label>
                    <input type="text" id="origem" name="origem" placeholder="Ex: ESTCB" required>
                </div>

                <div class="form-group">
                    <label for="destino">Destino</label>
                    <input type="text" id="destino" name="destino" placeholder="Ex: Bordados" required>
                </div>

                <div class="botoes-acao">
                    <a href="ofertas.php" class="botao">Cancelar</a>
                    <button type="submit" class="botao">Publicar Oferta</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function previewImage(input) {
            const file = input.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('preview-img').src = e.target.result;
                }
                reader.readAsDataURL(file);
            }
        }
    </script>
</body>
</html>
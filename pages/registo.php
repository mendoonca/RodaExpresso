<?php
    session_start();
    include '../base_dados/basedados.h';

    include 'ConstUtilizadores.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $nome = trim($_POST['nome']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];

        // Extrair domínio do email
        $email_parts = explode('@', $email);
        if (count($email_parts) != 2) {
            echo "<script>alert('Email inválido!'); window.location.href='PgRegisto.php';</script>";
            exit;
        }
        $dominio = '@' . $email_parts[1];

        // Verificar se o domínio está permitido
        $sql_dominio = "SELECT * FROM DominiosPermitidos WHERE dominio = '$dominio'";
        $resultado_dominio = mysqli_query($conn, $sql_dominio);

        if (mysqli_num_rows($resultado_dominio) == 0) {
            echo "<script>alert('Domínio de email não autorizado, use apenas @ipcbcampus.pt para registo.'); window.location.href='PgRegisto.php';</script>";
            exit;
        }

        if (!$conn) {
            die("Erro de conexão: " . mysqli_connect_error());
        }

        // Verificar se as passwords coincidem
        if ($password !== $confirm_password) {
            echo "<script>alert('As passwords não coincidem!'); window.location.href='PgRegisto.php';</script>";
            exit;
        }

        // Verificar se o email já existe
        $sql_verificar = "SELECT * FROM Utilizador WHERE email = '$email'";
        $resultado = mysqli_query($conn, $sql_verificar);
        if (mysqli_num_rows($resultado) > 0) {
            echo "<script>alert('O email já está registado!'); window.location.href='PgRegisto.php';</script>";
            exit;
        }

        // Hash da password usando MD5
        $passwordHash = md5($password);

        // Inserir novo utilizador com data de registo
        $sql = "INSERT INTO Utilizador (nome_utilizador, email, password, data_registo, tipo_utilizador) 
                VALUES ('$nome', '$email', '$passwordHash', NOW(), ".UTILIZADOR_POR_VALIDAR.")";

        if (mysqli_query($conn, $sql)) {
            $sql_login = "SELECT * FROM Utilizador WHERE email = '$email' AND password = '$passwordHash'";
            $resultado_login = mysqli_query($conn, $sql_login);

            if (mysqli_num_rows($resultado_login) > 0) {
                $utilizador = mysqli_fetch_assoc($resultado_login);
                $_SESSION['user_id'] = $utilizador['id_utilizador'];
                $_SESSION['user_nome'] = $utilizador['nome_utilizador'];
                $_SESSION['user_email'] = $utilizador['email'];
                $_SESSION['registo_completo'] = true;

                echo "<script>window.location.href = 'PgNaoValidados.php';</script>";
                exit;
            } else {
                echo "<script>alert('Erro ao realizar login. Tente novamente.'); window.location.href='PgRegisto.php';</script>";
                exit;
            }
        } else {
            echo "Erro ao registar: " . mysqli_error($conn);
        }

        mysqli_close($conn);
    } else {
        session_destroy();
        header("Location: PgRegisto.php");
        exit;
    }
?>
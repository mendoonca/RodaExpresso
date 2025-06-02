<?php
    session_start();

    // Verificar se o utilizador está autenticado
    if (!isset($_SESSION['user_id'])) {
        // Se não estiver autenticado, redirecionar para a página de login
        header("Location: ../PgLogin.php");
    }
?>

<!DOCTYPE html>
<html lang="pt-PT">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | Roda Expresso</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            width: 90%;
            margin: 30px auto;
            display: flex;
            flex-wrap: wrap;
            gap: 30px;
            justify-content: center;
        }
        .grafico-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            width: 45%;
            min-width: 500px;
            transition: transform 0.3s;
            cursor: pointer;
        }
        .grafico-container:hover {
            transform: scale(1.02);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        .grafico-container h2 {
            color: #007bff;
            margin-top: 0;
            text-align: center;
        }
        .grafico-wrapper {
            position: relative;
            height: 350px;
            width: 100%;
        }
        /* Modal para gráfico ampliado */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            overflow: auto;
        }
        .modal-content {
            background-color: white;
            margin: 5% auto;
            padding: 30px;
            border-radius: 10px;
            width: 80%;
            max-width: 900px;
            position: relative;
        }
        .close {
            position: absolute;
            top: 15px;
            right: 25px;
            color: #aaa;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover {
            color: #333;
        }
        .modal-grafico {
            height: 600px;
            width: 100%;
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
                <a href="avaliacoes.php">Avaliações</a>
                <a href="dashboard.php" class="negrito">Dashboard</a>
                <a href="conta/conta.php">Conta</a>
        </div>
    </div>

    <div class="container">
        <!-- Gráfico de Média das Idades -->
        <div class="grafico-container" id="graficoAtendimentos">
            <h2>Média das Idades</h2>
            <div class="grafico-wrapper">
                <canvas id="barChart"></canvas>
            </div>
        </div>

        <!-- Gráfico de Média de Avaliações -->
        <div class="grafico-container" id="graficoHorarios">
            <h2>Média de Avaliações</h2>
            <div class="grafico-wrapper">
                <canvas id="lineChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Modal para gráfico ampliado -->
    <div id="graficoModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 id="modalTitle"></h2>
            <div class="modal-grafico">
                <canvas id="modalChart"></canvas>
            </div>
        </div>
    </div>

    <script>
        // Dados aleatórios para os gráficos
        const meses = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        const horarios = ['8h', '10h', '12h', '14h', '16h', '18h', '20h'];
        
        // Gerar dados aleatórios para o gráfico de barras
        const atendimentosData = meses.map(() => Math.floor(Math.random() * 15) + 18);
        
        // Gerar dados aleatórios para o gráfico de linha
        const movimentoData = horarios.map(() => Math.floor(Math.random() * 5) + 1);

        // Cores dos gráficos
        const barColors = Array(12).fill('#007bff');
        const lineColor = '#ff6384';

        // Criar gráfico de barras
        const barCtx = document.getElementById('barChart').getContext('2d');
        const barChart = new Chart(barCtx, {
            type: 'bar',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Média das Idades',
                    data: atendimentosData,
                    backgroundColor: barColors,
                    borderColor: barColors,
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Número de Idades'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Meses do Ano'
                        }
                    }
                }
            }
        });

        // Criar gráfico de linha
        const lineCtx = document.getElementById('lineChart').getContext('2d');
        const lineChart = new Chart(lineCtx, {
            type: 'line',
            data: {
                labels: horarios,
                datasets: [{
                    label: 'Média de Avaliações',
                    data: movimentoData,
                    borderColor: lineColor,
                    backgroundColor: 'rgba(255, 99, 132, 0.1)',
                    borderWidth: 3,
                    tension: 0.3,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Nota da Avaliação'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Horários do Dia'
                        }
                    }
                }
            }
        });

        // Funcionalidade para ampliar gráficos
        const modal = document.getElementById('graficoModal');
        const modalTitle = document.getElementById('modalTitle');
        const modalChartCanvas = document.getElementById('modalChart');
        let modalChart = null;

        document.querySelectorAll('.grafico-container').forEach(container => {
            container.addEventListener('click', function() {
                const title = this.querySelector('h2').textContent;
                modalTitle.textContent = title;
                
                // Destruir gráfico anterior se existir
                if (modalChart) {
                    modalChart.destroy();
                }
                
                // Criar novo gráfico no modal baseado no que foi clicado
                const modalCtx = modalChartCanvas.getContext('2d');
                if (this.id === 'graficoAtendimentos') {
                    modalChart = new Chart(modalCtx, {
                        type: 'bar',
                        data: barChart.data,
                        options: {
                            ...barChart.options,
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                } else {
                    modalChart = new Chart(modalCtx, {
                        type: 'line',
                        data: lineChart.data,
                        options: {
                            ...lineChart.options,
                            responsive: true,
                            maintainAspectRatio: false
                        }
                    });
                }
                
                modal.style.display = 'block';
            });
        });

        // Fechar modal
        document.querySelector('.close').addEventListener('click', function() {
            modal.style.display = 'none';
        });

        // Fechar modal ao clicar fora
        window.addEventListener('click', function(event) {
            if (event.target === modal) {
                modal.style.display = 'none';
            }
        });
    </script>
</body>
</html>
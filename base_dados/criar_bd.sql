-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 30-Maio-2025 às 04:57
-- Versão do servidor: 10.4.32-MariaDB
-- versão do PHP: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `roda_expresso`
--
CREATE DATABASE IF NOT EXISTS `roda_expresso` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `roda_expresso`;

-- --------------------------------------------------------

--
-- Estrutura da tabela `avaliacoes`
--

CREATE TABLE `avaliacoes` (
  `id_avaliacao` int(11) NOT NULL,
  `aprovado` int(11) DEFAULT NULL,
  `id_utilizador` int(11) DEFAULT NULL,
  `id_condutor` int(11) DEFAULT NULL,
  `nota` int(11) DEFAULT NULL CHECK (`nota` between 1 and 5),
  `comentario` text DEFAULT NULL,
  `data_avaliacao` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `avaliacoes`
--

INSERT INTO `avaliacoes` (`id_avaliacao`, `aprovado`, `id_utilizador`, `id_condutor`, `nota`, `comentario`, `data_avaliacao`) VALUES
(1, 0, 2, 1, 4, 'Bom condutor!', '2025-05-30 09:30:00'),
(2, 0, 1, 3, 3, 'Um pouco desconfortável.', '2025-05-20 17:30:00');

-- --------------------------------------------------------

--
-- Estrutura da tabela `condutor`
--

CREATE TABLE `condutor` (
  `id_condutor` int(11) NOT NULL,
  `nome_condutor` varchar(100) NOT NULL,
  `idade` int(11) NOT NULL,
  `nacionalidade` varchar(50) NOT NULL,
  `classificacao` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `condutor`
--

INSERT INTO `condutor` (`id_condutor`, `nome_condutor`, `idade`, `nacionalidade`, `classificacao`) VALUES
(1, 'Miguel Martins', 24, 'Portuguesa', 4),
(2, 'João Pereira', 41, 'Portuguesa', 4),
(3, 'Sérgio Conceição', 50, 'Portuguesa', 4);

-- --------------------------------------------------------

--
-- Estrutura da tabela `dominiospermitidos`
--

CREATE TABLE `dominiospermitidos` (
  `id` int(11) NOT NULL,
  `dominio` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `dominiospermitidos`
--

INSERT INTO `dominiospermitidos` (`id`, `dominio`) VALUES
(2, '@ipcb.pt'),
(1, '@ipcbcampus.pt');

-- --------------------------------------------------------

--
-- Estrutura da tabela `historico`
--

CREATE TABLE `historico` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `tipo_evento` varchar(50) NOT NULL,
  `nome_completo` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `descricao` text DEFAULT NULL,
  `data` date NOT NULL,
  `resultado` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `historico`
--

INSERT INTO `historico` (`id`, `user_id`, `tipo_evento`, `nome_completo`, `email`, `descricao`, `data`, `resultado`) VALUES
(1, 1, 'Reserva', 'João Pereira', '', 'Viagem de ESTCB para Bordados', '2025-06-03', 'Confirmado para 1 lugares.'),
(2, 2, 'Reserva', 'João Pereira', '', 'Viagem de ESTCB para Bordados', '2025-06-03', 'Confirmado para 1 lugares.'),
(3, 4, 'Aprovação de Avaliação', 'João Borralho', '', 'Avaliação do condutor João Pereira com nota 5', '2025-06-03', 'Aprovado'),
(4, 3, 'Aprovação de Registo', 'Tiago Grila', 'sai.cavalinho@ipcbcampus.pt', 'Aprovação de registo de utilizador', '2025-06-03', 'Aprovado');

-- --------------------------------------------------------

--
-- Estrutura da tabela `horariostransporte`
--

CREATE TABLE `horariostransporte` (
  `id_horario` int(11) NOT NULL,
  `id_condutor` int(11) DEFAULT NULL,
  `id_rota` int(11) DEFAULT NULL,
  `dia_semana` enum('Segunda','Terça','Quarta','Quinta','Sexta','Sábado','Domingo') NOT NULL,
  `hora_partida` time NOT NULL,
  `hora_chegada` time NOT NULL,
  `data_viagem` date DEFAULT NULL,
  `lugares_disponiveis` int(11) DEFAULT NULL,
  `origem` varchar(255) DEFAULT NULL,
  `destino` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `horariostransporte`
--

INSERT INTO `horariostransporte` (`id_horario`, `id_condutor`, `id_rota`, `dia_semana`, `hora_partida`, `hora_chegada`, `data_viagem`, `lugares_disponiveis`, `origem`, `destino`) VALUES
(1, 1, NULL, 'Terça', '10:15:00', '10:30:00', NULL, 1, 'Alcains', 'ESTCB'),
(2, 2, NULL, 'Terça', '12:30:00', '13:00:00', NULL, 4, 'ESTCB', 'Bordados'),
(3, 3, NULL, 'Terça', '18:30:00', '20:00:00', NULL, 14, 'ESTCB', 'Senhora de Mércoles');

-- --------------------------------------------------------

--
-- Estrutura da tabela `ofertatransporte`
--

CREATE TABLE `ofertatransporte` (
  `id_sugestao` int(11) NOT NULL,
  `id_transporte` int(11) DEFAULT NULL,
  `id_condutor` int(11) DEFAULT NULL,
  `nome_condutor` varchar(100) NOT NULL,
  `origem` varchar(100) NOT NULL,
  `destino` varchar(100) NOT NULL,
  `data_hora` datetime NOT NULL,
  `lugares_disponiveis` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `ofertatransporte`
--

INSERT INTO `ofertatransporte` (`id_sugestao`, `id_transporte`, `id_condutor`, `nome_condutor`, `origem`, `destino`, `data_hora`, `lugares_disponiveis`) VALUES
(1, 1, 1, 'Miguel Martins', 'Câmera Municipal', 'ESTCB', '2025-06-04 10:30:00', 1),
(2, 2, 2, 'João Pereira', 'Biblioteca Egas Moniz', 'ESTCB', '2025-06-05 08:30:00', 4),
(3, 3, 3, 'Sérgio Conceição', 'ESTCB', 'Estação de Autocarros', '2025-06-05 17:15:00', 14);

-- --------------------------------------------------------

--
-- Estrutura da tabela `tipoutilizador`
--

CREATE TABLE `tipoutilizador` (
  `id_tipo` int(11) NOT NULL,
  `descricao` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `tipoutilizador`
--

INSERT INTO `tipoutilizador` (`id_tipo`, `descricao`) VALUES
(-1, 'aluno apagado'),
(0, 'utilizador por validar'),
(1, 'utilizador'),
(2, 'administrador'),
(3, 'gestor');

-- --------------------------------------------------------

--
-- Estrutura da tabela `transporte`
--

CREATE TABLE `transporte` (
  `id_transporte` int(11) NOT NULL,
  `tipo_veiculo` varchar(50) NOT NULL,
  `consumo` float NOT NULL,
  `lotacao_maximo` int(11) NOT NULL,
  `matricula` varchar(15) NOT NULL,
  `id_condutor` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `transporte`
--

INSERT INTO `transporte` (`id_transporte`, `tipo_veiculo`, `consumo`, `lotacao_maximo`, `matricula`, `id_condutor`) VALUES
(1, 'Peugeot 206', 0, 1, '89-23-UI', 1),
(2, 'Fiat 500', 0, 4, '26-14-AA', 2),
(3, 'Autocarro Bombástico', 0, 14, '63-XO-02', 3);

-- --------------------------------------------------------

--
-- Estrutura da tabela `utilizador`
--

CREATE TABLE `utilizador` (
  `id_utilizador` int(11) NOT NULL,
  `nome_utilizador` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `data_registo` timestamp NOT NULL DEFAULT current_timestamp(),
  `tipo_utilizador` int(11) NOT NULL,
  `idade` int(11) DEFAULT NULL,
  `telefone` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Extraindo dados da tabela `utilizador`
--

INSERT INTO `utilizador` (`id_utilizador`, `nome_utilizador`, `email`, `password`, `data_registo`, `tipo_utilizador`, `idade`, `telefone`) VALUES
(1, 'Diogo Santos', 'diogo.santos@ipcbcampus.pt', '36d3407b60d590fc5f3d4d6e104b5b11', '2025-04-09 21:57:25', 1, 20, '964272344'),
(2, 'João Borralho', 'joao.borralho@ipcbcampus.pt', '8b66ae5b03addfd5663c5902c82d5720', '2025-04-09 21:57:25', 1, 20, '969876543'),
(3, 'João Mendonça', 'joao.mendonca@ipcb.pt', '6b8fd068e58645ef65317cea67f627a4', '2025-04-09 21:57:25', 2, 19, '964272344'),
(4, 'Martim Carvalho', 'martim.carvalho@ipcb.pt', 'acccb87d280daa65cfae64bb7453e43c', '2025-04-09 21:57:25', 3, 19, '969873245'),
(5, 'Maria Oliveira Costa', 'maria.costa52@ipcbcampus.pt', 'e4c1dcc487fac7daff176085cf2eaaa7', '2025-04-10 00:22:41', 0, NULL, NULL),
(6, 'Carlos Montanha Mendes', 'monta.carlos@ipcbcampus.pt', '2b87b0cb062769de580d3404ff947d89', '2025-04-10 00:26:32', 0, NULL, NULL);

--
-- Índices para tabelas despejadas
--

--
-- Índices para tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD PRIMARY KEY (`id_avaliacao`),
  ADD KEY `id_utilizador` (`id_utilizador`),
  ADD KEY `id_condutor` (`id_condutor`);

--
-- Índices para tabela `condutor`
--
ALTER TABLE `condutor`
  ADD PRIMARY KEY (`id_condutor`);

--
-- Índices para tabela `dominiospermitidos`
--
ALTER TABLE `dominiospermitidos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `dominio` (`dominio`);

--
-- Índices para tabela `historico`
--
ALTER TABLE `historico`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Índices para tabela `horariostransporte`
--
ALTER TABLE `horariostransporte`
  ADD PRIMARY KEY (`id_horario`),
  ADD KEY `id_condutor` (`id_condutor`);

--
-- Índices para tabela `ofertatransporte`
--
ALTER TABLE `ofertatransporte`
  ADD PRIMARY KEY (`id_sugestao`),
  ADD KEY `id_transporte` (`id_transporte`),
  ADD KEY `id_condutor` (`id_condutor`);

--
-- Índices para tabela `tipoutilizador`
--
ALTER TABLE `tipoutilizador`
  ADD PRIMARY KEY (`id_tipo`);

--
-- Índices para tabela `transporte`
--
ALTER TABLE `transporte`
  ADD PRIMARY KEY (`id_transporte`),
  ADD KEY `fk_transporte_condutor` (`id_condutor`);

--
-- Índices para tabela `utilizador`
--
ALTER TABLE `utilizador`
  ADD PRIMARY KEY (`id_utilizador`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  MODIFY `id_avaliacao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `condutor`
--
ALTER TABLE `condutor`
  MODIFY `id_condutor` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `dominiospermitidos`
--
ALTER TABLE `dominiospermitidos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de tabela `historico`
--
ALTER TABLE `historico`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de tabela `horariostransporte`
--
ALTER TABLE `horariostransporte`
  MODIFY `id_horario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `ofertatransporte`
--
ALTER TABLE `ofertatransporte`
  MODIFY `id_sugestao` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `transporte`
--
ALTER TABLE `transporte`
  MODIFY `id_transporte` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de tabela `utilizador`
--
ALTER TABLE `utilizador`
  MODIFY `id_utilizador` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restrições para despejos de tabelas
--

--
-- Limitadores para a tabela `avaliacoes`
--
ALTER TABLE `avaliacoes`
  ADD CONSTRAINT `avaliacoes_ibfk_2` FOREIGN KEY (`id_utilizador`) REFERENCES `utilizador` (`id_utilizador`),
  ADD CONSTRAINT `avaliacoes_ibfk_3` FOREIGN KEY (`id_condutor`) REFERENCES `condutor` (`id_condutor`);

--
-- Limitadores para a tabela `historico`
--
ALTER TABLE `historico`
  ADD CONSTRAINT `historico_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `utilizador` (`id_utilizador`) ON DELETE CASCADE;

--
-- Limitadores para a tabela `ofertatransporte`
--
ALTER TABLE `ofertatransporte`
  ADD CONSTRAINT `ofertatransporte_ibfk_1` FOREIGN KEY (`id_transporte`) REFERENCES `transporte` (`id_transporte`),
  ADD CONSTRAINT `ofertatransporte_ibfk_2` FOREIGN KEY (`id_condutor`) REFERENCES `condutor` (`id_condutor`);

--
-- Limitadores para a tabela `transporte`
--
ALTER TABLE `transporte`
  ADD CONSTRAINT `fk_transporte_condutor` FOREIGN KEY (`id_condutor`) REFERENCES `condutor` (`id_condutor`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;

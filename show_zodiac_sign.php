<?php include 'header.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4 text-center">Resultado do Seu Signo</h1>

    <?php
    // Verificar se é uma requisição POST válida
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['data_nascimento'])) {
        $dataNascimento = $_POST['data_nascimento']; // formato: YYYY-MM-DD
        
        // Validar formato da data
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $dataNascimento)) {
            echo '<div class="alert alert-danger text-center" role="alert">';
            echo '<i class="fas fa-exclamation-triangle me-2"></i>';
            echo 'Formato de data inválido. Use o formato DD/MM/AAAA.';
            echo '</div>';
            echo '<div class="text-center"><a href="index.php" class="btn btn-secondary">Voltar</a></div>';
            exit;
        }

        // Carregar XML com tratamento de erro
        $signos = simplexml_load_file('signos.xml');
        
        if ($signos === false) {
            echo '<div class="alert alert-danger text-center" role="alert">';
            echo '<i class="fas fa-exclamation-triangle me-2"></i>';
            echo 'Erro ao carregar dados dos signos. Tente novamente mais tarde.';
            echo '</div>';
            echo '<div class="text-center"><a href="index.php" class="btn btn-secondary">Voltar</a></div>';
            exit;
        }

        // Extrair mês e dia da data de nascimento
        $dataObj = DateTime::createFromFormat('Y-m-d', $dataNascimento);
        $mesDia = $dataObj->format('m-d');

        // Função para converter data do XML (dd-mm) para DateTime com ano fixo
        function converterData($dataStr, $anoReferencia) {
            // $dataStr no formato dd-mm
            $partes = explode('-', $dataStr);
            $dia = $partes[0];
            $mes = $partes[1];
            return DateTime::createFromFormat('Y-m-d', "$anoReferencia-$mes-$dia");
        }

        // Ano fixo para comparação (usamos 2000, ano bissexto para evitar problemas)
        $anoReferencia = 2000;

        // Data de nascimento com ano fixo
        $dataNascimentoFix = DateTime::createFromFormat('Y-m-d', "$anoReferencia-$mesDia");

        $signoEncontrado = null;

        foreach ($signos->signo as $signo) {
            $dataInicio = converterData($signo->dataInicio, $anoReferencia);
            $dataFim = converterData($signo->dataFim, $anoReferencia);

            // Ajuste para signos que cruzam o ano (ex: Capricórnio 22-12 a 20-01)
            if ($dataFim < $dataInicio) {
                // Se a dataFim é menor que dataInicio, significa que o período passa de um ano para outro
                // Se a data de nascimento for menor que dataInicio, adicionamos 1 ano para comparação
                if ($dataNascimentoFix < $dataInicio) {
                    $dataNascimentoFix->modify('+1 year');
                }
                $dataFim->modify('+1 year');
            }

            if ($dataNascimentoFix >= $dataInicio && $dataNascimentoFix <= $dataFim) {
                $signoEncontrado = $signo;
                break;
            }
        }

        if ($signoEncontrado) {
            // Calcular idade
            $dataNascimentoObj = DateTime::createFromFormat('Y-m-d', $dataNascimento);
            $hoje = new DateTime();
            $idade = $hoje->diff($dataNascimentoObj)->y;
            
            // Obter ícone do signo
            $icones = [
                'Áries' => '♈',
                'Touro' => '♉',
                'Gêmeos' => '♊',
                'Câncer' => '♋',
                'Leão' => '♌',
                'Virgem' => '♍',
                'Libra' => '♎',
                'Escorpião' => '♏',
                'Sagitário' => '♐',
                'Capricórnio' => '♑',
                'Aquário' => '♒',
                'Peixes' => '♓'
            ];
            
            $icone = $icones[(string)$signoEncontrado->nome] ?? '⭐';
            
            echo '<div class="result-card">';
            echo '<div class="text-center">';
            echo '<div class="signo-icone mb-3" style="font-size: 4rem;">' . $icone . '</div>';
            echo '<h2 class="mb-3">' . $signoEncontrado->nome . '</h2>';
            echo '<div class="info-grid mb-4">';
            echo '<div class="info-item">';
            echo '<i class="fas fa-calendar-alt me-2"></i>';
            echo '<strong>Período:</strong> ' . $signoEncontrado->dataInicio . ' a ' . $signoEncontrado->dataFim;
            echo '</div>';
            echo '<div class="info-item">';
            echo '<i class="fas fa-birthday-cake me-2"></i>';
            echo '<strong>Idade:</strong> ' . $idade . ' anos';
            echo '</div>';
            echo '<div class="info-item">';
            echo '<i class="fas fa-calendar-check me-2"></i>';
            echo '<strong>Data:</strong> ' . $dataNascimentoObj->format('d/m/Y');
            echo '</div>';
            echo '<div class="info-item">';
            echo '<i class="fas fa-fire me-2"></i>';
            echo '<strong>Elemento:</strong> ' . (isset($signoEncontrado->elemento) ? $signoEncontrado->elemento : 'N/A');
            echo '</div>';
            echo '<div class="info-item">';
            echo '<i class="fas fa-globe me-2"></i>';
            echo '<strong>Planeta:</strong> ' . (isset($signoEncontrado->planeta) ? $signoEncontrado->planeta : 'N/A');
            echo '</div>';
            echo '<div class="info-item">';
            echo '<i class="fas fa-star me-2"></i>';
            echo '<strong>Qualidade:</strong> ' . (isset($signoEncontrado->qualidade) ? $signoEncontrado->qualidade : 'N/A');
            echo '</div>';
            echo '</div>';
            echo '<div class="descricao-signo">';
            echo '<h4 class="mb-3"><i class="fas fa-star me-2"></i>Características</h4>';
            echo '<p class="lead">' . $signoEncontrado->descricao . '</p>';
            echo '</div>';
            echo '<div class="mt-4">';
            echo '<a href="index.php" class="btn btn-secondary">';
            echo '<i class="fas fa-arrow-left me-2"></i>Nova Consulta';
            echo '</a>';
            echo '</div>';
            echo '</div></div>';
        } else {
            echo '<div class="alert alert-danger text-center" role="alert">';
            echo 'Não foi possível identificar seu signo. Por favor, verifique a data inserida.';
            echo '</div>';
            echo '<div class="text-center"><a href="index.php" class="btn btn-secondary">Voltar</a></div>';
        }
    } else {
        echo '<div class="alert alert-warning text-center" role="alert">';
        echo 'Por favor, insira uma data de nascimento válida.';
        echo '</div>';
        echo '<div class="text-center"><a href="index.php" class="btn btn-secondary">Voltar</a></div>';
    }
    ?>

</div>

</body>
</html>

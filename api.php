<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Resultado</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<?php


if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['q'])) {
    $termo = urlencode($_GET['q']);
    
    $url = "https://openlibrary.org/search.json?q={$termo}&limit=20";
    
    $resposta = @file_get_contents($url);
    
    if ($resposta === false) {
        echo '<div class="erro">';
        echo '<p>ERRO: Não foi possível conectar à API. Verifique sua conexão com a internet.</p>';
        echo '<br><br>';
        echo '</div>';
    }
    else {
        $dados = json_decode($resposta, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo '<div class="erro">';
            echo '<p>ERRO: Resposta inválida da API. Tente novamente mais tarde.</p>';
            echo '<br><br>';
            echo '</div>';
        }
        elseif (isset($dados['docs']) && count($dados['docs']) > 0) {
            $totalLivros = count($dados['docs']);
            
            echo '<div class="resultados">';
            echo '<h2>Resultados para: ' . htmlspecialchars($_GET['q']) . '</h2>';
            
            $contador = 0;
            for ($i = 0; $i < $totalLivros && $contador < 10; $i++) {
                
                $livro = $dados['docs'][$i];
                
                $titulo = isset($livro['title']) ? $livro['title'] : 'Título não disponível';
                
                $autor = isset($livro['author_name'][0]) ? $livro['author_name'][0] : 'Autor desconhecido';
                
                $ano = isset($livro['first_publish_year']) ? $livro['first_publish_year'] : 'Ano não disponível';
                
                $capa = '';
                if (isset($livro['cover_i'])) {
                    $capa = "https://covers.openlibrary.org/b/id/{$livro['cover_i']}-M.jpg";
                } 
                elseif (isset($livro['cover_edition_key'])) {
                    $capa = "https://covers.openlibrary.org/b/olid/{$livro['cover_edition_key']}-M.jpg";
                }
                
                $descricao = '';
                if (isset($livro['first_sentence'])) {
                    $descricao = is_array($livro['first_sentence']) ? $livro['first_sentence'][0] : $livro['first_sentence'];
                }
                
                echo '<div class="livro-card">';
                
                if ($capa) {
                    echo '<div class="capa-livro">';
                    echo '<img src="' . htmlspecialchars($capa) . '" alt="' . htmlspecialchars($titulo) . '">';
                    echo '</div>';
                }
                
                echo '<div class="info-livro">';
                echo '<h3>' . htmlspecialchars($titulo) . '</h3>';
                echo '<p class="autor">Autor: ' . htmlspecialchars($autor) . '</p>';
                echo '<p class="ano">Ano: ' . htmlspecialchars($ano) . '</p>';
                
                if ($descricao) {
                    echo '<p class="descricao">"' . htmlspecialchars($descricao) . '"</p>';
                }
                
                echo '</div>';
                echo '</div>';
                
                $contador++;
            }
            
            echo '</div>';
        }
        else {
            echo '<div class="erro">';
            echo '<p>Nenhum livro encontrado para o termo pesquisado.</p>';
            echo '<br><br>';
            echo '</div>';
        }
    }
}
else {
    echo '<div class="erro">';
    echo '<p>ERRO: Termo de busca inválido.</p>';
    echo '<br><br>';
    echo '<a href="index.html">Voltar</a>';
    echo '</div>';
}

?>

<br><br>
<a href="index.html" class="botao-voltar">Fazer nova busca</a>

</body>
</html>
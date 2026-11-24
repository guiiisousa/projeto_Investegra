<?php
$quizPendente = !\App\Core\Session::quizConcluido();
$perfilNavClasses = "perfil-nav";

if (!empty($perfilNavAtivo)) {
    $perfilNavClasses .= " ativo";
}

if ($quizPendente) {
    $perfilNavClasses .= " quiz-pendente";
}
?>
<link rel="stylesheet" href="css/tema.css?v=1">
<script src="js/tema.js?v=1" defer></script>
<a href="meu_perfil.php" class="<?= $perfilNavClasses ?>" aria-label="<?= $quizPendente ? "Perfil. Faça o quiz de investidor." : "Perfil" ?>"<?= !empty($perfilNavAtivo) ? ' aria-current="page"' : "" ?>>
    Perfil
    <?php if ($quizPendente && empty($perfilNavAtivo)): ?>
        <span class="quiz-tooltip" role="status">Faça seu quiz de investidor</span>
    <?php endif; ?>
</a>
<a href="chat.php" class="<?= !empty($chatNavAtivo) ? 'ativo' : '' ?>">
    Assistente IA
</a>
<?php unset($perfilNavAtivo, $quizPendente, $perfilNavClasses); ?>

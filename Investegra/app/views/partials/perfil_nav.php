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
<link rel="stylesheet" href="css/tema.css?v=13">
<script src="js/tema.js?v=2" defer></script>
<a href="meu_perfil.php" class="<?= $perfilNavClasses ?>" aria-label="<?= $quizPendente ? "Perfil. Faça o quiz de investidor." : "Perfil" ?>"<?= !empty($perfilNavAtivo) ? ' aria-current="page"' : "" ?>>
    Perfil
    <?php if ($quizPendente && empty($perfilNavAtivo)): ?>
        <span class="quiz-tooltip" role="status">Faça seu quiz de investidor</span>
    <?php endif; ?>
</a>
<?php $alertasNavClasses = !empty($alertasNavAtivo) ? "ativo" : ""; ?>
<a
    href="alertas.php"
    class="<?= $alertasNavClasses ?>"
    aria-label="Alertas"
    title="Alertas"
    <?= !empty($alertasNavAtivo) ? 'aria-current="page"' : "" ?>
>
    <span class="texto-acessivel">Alertas</span>
</a>
<a href="chat.php" class="<?= !empty($chatNavAtivo) ? 'ativo' : '' ?>">
    Assistente IA
</a>
<?php unset($perfilNavAtivo, $alertasNavAtivo, $alertasNavClasses, $quizPendente, $perfilNavClasses); ?>

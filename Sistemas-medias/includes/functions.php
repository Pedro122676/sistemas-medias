<?php
function conceito($media) {
    if ($media >= 9) return 'A';
    if ($media >= 7) return 'B';
    if ($media >= 4) return 'C';
    return 'D';
}

function mensagemConceito($conceito) {
    switch ($conceito) {
        case 'A': return "Aprovado, cara vai curtir a vida, ela não é só feita de compromissos e estudos, aproveite ";
        case 'B': return "Vai que é sua taffarel! ";
        case 'C': return "Recuperação, é a sua chance de ficar na média ";
        case 'D': return "Cara você esta ferrado vai ter que repetir a mesma matéria tudo de novo ";
    }
}
?>
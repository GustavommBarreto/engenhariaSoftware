<?php

namespace App\Enums;

enum ProjectStatus: string
{
    case Ativo = 'Ativo';
    case Pausado = 'Pausado';
    case Concluido = 'Concluído';
}

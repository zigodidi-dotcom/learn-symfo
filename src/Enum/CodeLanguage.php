<?php

namespace App\Enum;

enum CodeLanguage: string
{
    case Php = 'php';
    case Yaml = 'yaml';
    case Xml = 'xml';
    case Env = 'env';
    case Twig = 'twig';
}

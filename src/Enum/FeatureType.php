<?php

namespace App\Enum;

enum FeatureType: string
{
    case Component = 'component';
    case Attribute = 'attribute';
    case Command = 'command';
    case Concept = 'concept';
    case Configuration = 'configuration';
}

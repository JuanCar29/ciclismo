<?php

namespace App\Enums;

enum TipoEtapa: string
{
    case LLANO = 'llano';
    case MEDIA_MONTANA = 'media_montana';
    case ALTA_MONTANA = 'alta_montana';
    case CONTRARRELOJ = 'contrarreloj';
    case CONTRARRELOJ_POR_EQUIPOS = 'contrarreloj_por_equipos';

    public function label(): string
    {
        return match ($this) {
            self::LLANO => 'Llano',
            self::MEDIA_MONTANA => 'Media montaña',
            self::ALTA_MONTANA => 'Alta montaña',
            self::CONTRARRELOJ => 'CRI',
            self::CONTRARRELOJ_POR_EQUIPOS => 'CRE',
        };
    }

    public function selectLabel(): string
    {
        return match ($this) {
            self::LLANO => 'Llano',
            self::MEDIA_MONTANA => 'Media montaña',
            self::ALTA_MONTANA => 'Alta montaña',
            self::CONTRARRELOJ => 'Contrarreloj',
            self::CONTRARRELOJ_POR_EQUIPOS => 'CRE por equipos',
        };
    }

    public function badgeColor(): string
    {
        return match ($this) {
            self::LLANO => 'green',
            self::MEDIA_MONTANA => 'yellow',
            self::ALTA_MONTANA => 'red',
            self::CONTRARRELOJ => 'blue',
            self::CONTRARRELOJ_POR_EQUIPOS => 'purple',
        };
    }

    public function publicBadgeColor(): string
    {
        return match ($this) {
            self::LLANO => 'green',
            self::MEDIA_MONTANA => 'yellow',
            self::ALTA_MONTANA => 'red',
            self::CONTRARRELOJ => 'indigo',
            self::CONTRARRELOJ_POR_EQUIPOS => 'purple',
        };
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $case) {
            $options[$case->value] = $case->selectLabel();
        }

        return $options;
    }
}

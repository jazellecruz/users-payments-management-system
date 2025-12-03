<?php

    enum EnvironmentMode: string {
        case DEVELOPMENT = 'development';
        case PRODUCTION = 'production';

        public static function fromString(string $value): ?Environment {
            return match($value) {
                'development' => EnvironmentMode::DEVELOPMENT,
                'production' => EnvironmentMode::PRODUCTION,
                default => null,
            };
        }
    }
?>
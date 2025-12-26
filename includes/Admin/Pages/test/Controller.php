<?php
// Contrôleur OpenApi pour le module de test

class TestController
{
    public static function getHello($request)
    {
        return [
            'message' => 'Hello depuis l’API de test !',
            'timestamp' => time()
        ];
    }
}
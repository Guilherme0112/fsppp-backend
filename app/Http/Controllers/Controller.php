<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    title: "FSPPP API",
    version: "1.0.0",
    description: "API para o projeto FSPPP (Sistema de Gestão de Tarefas)",
    contact: new OA\Contact(email: "suporte@fsppp.com")
)]
#[OA\Server(
    url: "http://localhost:8000/api/v1",
    description: "Servidor de Desenvolvimento"
)]
#[OA\SecurityScheme(
    securityScheme: "bearerAuth",
    type: "http",
    name: "Authorization",
    in: "header",
    scheme: "bearer",
    bearerFormat: "JWT"
)]
abstract class Controller
{
    //
}

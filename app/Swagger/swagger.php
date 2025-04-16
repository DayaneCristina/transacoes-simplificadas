<?php

/**
 * @OA\Schema(
 *     schema="Error",
 *     required={"error"},
 *     @OA\Property(property="error", type="string", example="Erro detalhado")
 * )
 */

/**
 * @OA\SecurityScheme(
 *     securityScheme="userIdHeader",
 *     type="apiKey",
 *     name="user-id",
 *     in="header"
 * )
 */

/**
 * @OA\Tag(
 *     name="Accounts",
 *     description="Operações relacionadas a contas"
 * )
 *
 * @OA\Tag(
 *     name="Transactions",
 *     description="Operações relacionadas a transações"
 * )
 */

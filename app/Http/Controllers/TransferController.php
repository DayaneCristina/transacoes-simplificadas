<?php

namespace App\Http\Controllers;

use App\Services\TransferService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransferController extends Controller
{
    private TransferService $transferService;
    public function __construct(TransferService $transferService)
    {
        $this->transferService = $transferService;
    }
    /**
     * @OA\Post(
     *     path="/api/transfer",
     *     summary="Transfere um valor entre contas",
     *     tags={"Transfers"},
     *     @OA\Parameter(
     *         name="user-id",
     *         in="header",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"amount", "account_origin", "account_destination"},
     *             @OA\Property(property="amount", type="number", format="float", example=150.75),
     *             @OA\Property(property="account_origin", type="integer", example=123),
     *             @OA\Property(property="account_destination", type="integer", example=456)
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Transferência realizada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro na requisição",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Insufficient balance")
     *         )
     *     )
     * )
     */
    public function transfer(Request $request): Response
    {
        $data = $request->validate([
            'amount'              => 'required|numeric',
            'account_origin'      => 'required|int',
            'account_destination' => 'required|int|different:account_origin'
        ]);

        $accountOrigin = $data['account_origin'];
        $accountDestination = $data['account_destination'];
        $amount = $request->get('amount');

        $this->transferService->transfer($accountOrigin, $accountDestination, $amount);

        return response()->noContent();
    }
}

<?php

namespace App\Http\Controllers;

use App\Enums\AccountType;
use App\Enums\TransactionCode;
use App\Exceptions\Business\AccountTypeNotFoundException;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AccountController extends Controller
{
    private AccountService $accountService;

    public function __construct(
        AccountService $accountService
    ) {
        $this->accountService = $accountService;
    }

    /**
     * @OA\Get(
     *     path="/api/accounts/balance",
     *     summary="Obtém o saldo da conta do usuário",
     *     tags={"Accounts"},
     *     @OA\Parameter(
     *         name="user-id",
     *         in="header",
     *         required=true,
     *         description="ID do usuário",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="account_type",
     *         in="query",
     *         required=false,
     *         description="Tipo de conta (PAYMENT)",
     *         @OA\Schema(type="string", enum={"PAYMENT"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Saldo obtido com sucesso",
     *         @OA\JsonContent(
     *             @OA\Property(property="balance", type="number", format="float", example=125.50)
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro na requisição",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Account type not found")
     *         )
     *     )
     * )
     */
    public function balance(Request $request): JsonResponse
    {
        $userId = $request->header('user-id');

        $accountType = AccountType::tryFrom($request->get('account_type'));

        if ($request->get('account_type') && !$accountType) {
            throw new AccountTypeNotFoundException();
        }

        return response()->json([
            'balance' => $this->accountService->getAccountBalance($userId, $accountType)
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/accounts/credit",
     *     summary="Credita um valor na conta destino",
     *     tags={"Accounts"},
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
     *             required={"amount", "account_destination", "transaction_code"},
     *             @OA\Property(property="amount", type="number", format="float", example=100.50),
     *             @OA\Property(property="account_destination", type="integer", example=123),
     *             @OA\Property(property="transaction_code", type="integer", example=1)
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Operação realizada com sucesso"
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Erro na requisição",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Account not found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Operação não permitida",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Credit not allowed")
     *         )
     *     )
     * )
     */
    public function credit(Request $request): Response
    {
        $data = $request->validate([
            'amount'              => 'required|numeric',
            'account_destination' => 'required|numeric',
            'transaction_code'    => 'required|int'
        ]);

        $userId = $request->header('user-id');
        $destination = $data['account_destination'];
        $amount = $data['amount'];
        $transactionCode = TransactionCode::from($data['transaction_code']);

        $this->accountService->credit($destination, $amount, $transactionCode, $userId);

        return response()->noContent();
    }


}

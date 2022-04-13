<?php

namespace App\Repositories;

use App\Models\SellerAccount;

class SellerAccountRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new SellerAccount);
    }

    public function getSellerAccount()
    {
        $sinkAccountName = $this->model
            ->selectRaw('DISTINCT asinking_account_name AS account_name')
            ->where('is_a4_account', 1)
            ->where('active', 1);

        $erpNickName = $this->model
            ->selectRaw('DISTINCT erp_nick_name AS account_name')
            ->where('is_a4_account', 1)
            ->where('active', 1);

        return $this->model
            ->selectRaw('DISTINCT account_name')
            ->where('is_a4_account', 1)
            ->where('active', 1)
            ->union($erpNickName)
            ->union($sinkAccountName)
            ->pluck('account_name')
            ->toArray();
    }

    public function searchSellerAccountView(
        $platform,
        $isA4Account
    ) {
        return $this->model
            ->select(
                'is_a4_account',
                'platform',
                'account_name',
                'erp_nick_name',
                'asinking_account_name'
            )
            ->active()
            ->when($platform, fn ($q) => $q->where('platform', $platform))
            ->when(isset($isA4Account), fn ($q) => $q->where('is_a4_account', (int)$isA4Account))
            ->orderByDesc('is_a4_account')
            ->orderBy('platform')
            ->orderBy('account_name')
            ->paginate();
    }
}

<?php

namespace App\Repositories;

use App\Constants\RoleConstant;
use App\Models\CustomerRelation;
use Illuminate\Support\Facades\Auth;

class CustomerRelationRepository extends BaseRepository
{
    public function __construct()
    {
        parent::__construct(new CustomerRelation);
    }

    public function getClientCodeList()
    {
        return $this->model
            ->select('customer_relations.client_code')
            ->distinct('customer_relations.client_code')
            ->when(!(Auth::user()->roleAssignment->role_id === RoleConstant::MANAGER), function ($q) {
                return $q->join('users', 'users.id', '=', 'customer_relations.user_id')
                    ->where('customer_relations.active', 1)
                    ->where('users.id', Auth::id());
            })->pluck('client_code');
    }
}

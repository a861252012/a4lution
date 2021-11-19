<?php

namespace App\Console\Commands;

use App\Models\EmployeeCommission;
use App\Models\EmployeeCommissionEntry;
use App\Models\EmployeeCommissionRule;
use App\Models\ExchangeRate;
use App\Models\Customer;
use App\Models\EmployeeMonthlyFeeRule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\RoleAssignment;
use App\Models\CustomerRelation;
use App\Models\BillingStatement;
use Illuminate\Support\Facades\Log;

class CalculateCommission extends Command
{
    protected $signature = 'calculate_commission {--date}';
    protected $description = 'calculate a4lution employee commission';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $date = $this->option('date') ?? now()->subMonth()->firstOfMonth()->toDateString();
        $currentDate = now()->format('Y-m-d');
        $userID = Auth::id() ?? 999999999;

        DB::beginTransaction();
        try {
            //鎖定上個月份的帳
            BillingStatement::where('report_date', $date)
                ->active()
                ->update(
                    [
                        'cutoff_time' => now()
                    ]
                );

            EmployeeCommission::where('report_date', $date)
                ->active()
                ->update(
                    [
                        'active' => 0,
                        'deleted_at' => now(),
                        'deleted_by' => Auth::id()
                    ]
                );

            EmployeeCommissionEntry::where('report_date', $date)
                ->active()
                ->update(
                    [
                        'active' => 0,
                        'deleted_at' => now(),
                        'deleted_by' => Auth::id()
                    ]
                );

            //取得員工清單及員工類別(role)
            $roleAssignment = RoleAssignment::from('role_assignment as a')
                ->select(
                    'a.user_id',
                    'u.user_name',
                    'u.region',
                    'u.company_type',
                    'r.role_name',
                    'a.role_id',
                    'u.currency',
                )
                ->join('roles as r', 'a.role_id', '=', 'r.id')
                ->join('users as u', 'u.id', '=', 'a.user_id')
                ->where('a.active', '=', 1)
                ->where('u.active', '=', 1)
                ->where('r.active', '=', 1)
                ->whereIn('r.id', [1, 3, 4])//sales, operation , account_service
                ->get()
                ->toArray();

            foreach ($roleAssignment as $item) {
                //1.找出該員工負責的客人清單
                $customerListQuery = CustomerRelation::query()
                    ->from('customer_relations as x')
                    ->select(
                        'x.client_code',
                        'u.user_name',
                        'r.role_name'
                    )
                    ->join('users as u', 'x.user_id', '=', 'u.id')
                    ->join('roles as r', 'r.id', '=', 'x.role_id')
                    ->where('x.active', 1)
                    ->where('u.active', 1);

                if ($item['role_name'] !== 'operation') {
                    $customerListQuery->where('u.user_name', '=', $item['user_name']);
                }

                $customerList = $customerListQuery->get();

                //取得各個員工的客戶
                $clientCodeArr = collect($customerList)->unique('client_code')->map(function ($customer) {
                    return $customer->client_code;
                })->toArray();

                //2.計算當月佣金抽成的總額
                $hKDCommissionSum = (float)BillingStatement::select(DB::raw("SUM(avolution_commission) as 'total'"))
                    ->where('active', 1)
                    ->whereNotNull('cutoff_time')
                    ->where('report_date', $date)
                    ->whereIn('client_code', $clientCodeArr)
                    ->value('total');

                $RateOfTWD = (float)ExchangeRate::select('exchange_rate')
                    ->where('base_currency', 'TWD')
                    ->where('quoted_date', $date)
                    ->where('active', 1)
                    ->value('exchange_rate');

                //3.判斷是否有達標獎金
                $twdCommissionSum = $RateOfTWD ? $hKDCommissionSum / $RateOfTWD : 0;

                $employeeCommission = [];

                //employee_commissions.extra_monthly_fee
                if ($item['role_name'] === 'sales' && $item['region'] === 'TW' && (float)$twdCommissionSum >= 250000) {
                    $employeeCommission['extra_monthly_fee_amount'] = $hKDCommissionSum * 0.02;
                }

                //employee_commissions.extra_ops_commission
                if ($item['role_name'] === 'operation' && $item['region'] === 'HK') {
                    $employeeCommission['extra_ops_commission_rate'] = $this->getCommissionRate($hKDCommissionSum);
                    $employeeCommission['extra_ops_commission_amount'] = $employeeCommission['extra_ops_commission_rate']
                        ? $hKDCommissionSum * $employeeCommission['extra_ops_commission_rate'] : 0;
                }

                //4.新增一筆員工分配利潤紀錄
                $employeeCommission['report_date'] = $date;
                $employeeCommission['employee_user_id'] = $item['user_id'];
                $employeeCommission['role_id'] = $item['role_id'];
                $employeeCommission['role_name'] = $item['role_name'];
                $employeeCommission['currency'] = $item['currency'];
                $employeeCommission['region'] = $item['region'];
                $employeeCommission['company_type'] = $item['company_type'];
                $employeeCommission['extra_monthly_fee_rate'] = 0.02;
                $employeeCommission['total_billed_commissions_amount'] = $hKDCommissionSum;
                $employeeCommission['total_billed_commission_currency'] = 'HKD';
                $employeeCommission['customer_qty'] = count($customerList);
                $employeeCommission['created_at'] = gmdate('Y-m-d h:i:s');
                $employeeCommission['created_by'] = $userID;
                $employeeCommission['active'] = 1;

                $employeeComID = EmployeeCommission::insertGetId($employeeCommission);

                //分別計算各個員工負責的客人清單 (計算單件報酬)
                foreach ($clientCodeArr as $clientCode) {
                    $customerBilling = BillingStatement::select(
                        'id',
                        'report_date',
                        DB::raw("SUM(avolution_commission) as 'total'"),
                    )
                        ->where('active', 1)
                        ->whereNotNull('cutoff_time')
                        ->where('report_date', $date)
                        ->where('client_code', $clientCode)
                        ->first();

                    $contractLength = Customer::select('contract_date')
                        ->where('active', 1)
                        ->where('client_code', $clientCode)
                        ->value('contract_date');

                    $contractDate = Carbon::parse($contractLength);
                    $contractYears = ceil($contractDate->floatDiffInYears($currentDate));
                    $contractMonths = ceil($contractDate->floatDiffInMonths($currentDate));

                    //2.取得各用戶的月結費用
                    $employeeEntries = [];

                    $customerMonthlyFeeRate = $this->getCustomerMonthlyFeeRate(
                        (float)$customerBilling->total,
                        $clientCode,
                        $item['role_id'],
                        $contractYears
                    );

                    $calculationFormat = "%s = %d * %.2f HKD \n";

                    $employeeEntries['monthly_fee'] = (float)$customerBilling->total * $customerMonthlyFeeRate;
                    $employeeEntries['calculation_expression'] = sprintf(
                        $calculationFormat,
                        "monthly fee",
                        (float)$customerBilling->total,
                        $customerMonthlyFeeRate,
                    );

                    if ($item['company_type'] === 'Contin' && $item['role_name'] === 'sales') {
                        $isCrossSales = CustomerRelation::from('customer_relations as r')
                            ->join('users as u', 'r.user_id', '=', 'u.id')
                            ->where('r.active', 1)
                            ->where('r.user_id', '!=', $userID)
                            ->where('r.role_id', 1)
                            ->where('u.company_type', '!=', 'Contin')
                            ->exists();

                        if ($isCrossSales) {
                            $employeeEntries['cross_sales'] = (float)$customerBilling->total * 0.03;

                            $employeeEntries['calculation_expression'] .= sprintf(
                                $calculationFormat,
                                "cross sales",
                                (float)$customerBilling->total,
                                0.03,
                            );
                        }
                    }

                    if ($item['role_name'] === 'operation') {
                        $opsCommissionRate = $this->getOpsCommissionRate($contractMonths);
                        $employeeEntries['ops_commission'] = (float)$customerBilling->total * $opsCommissionRate;

                        $employeeEntries['calculation_expression'] .= sprintf(
                            $calculationFormat,
                            "ops commission",
                            (float)$customerBilling->total,
                            $opsCommissionRate
                        );
                    }

                    //新增利潤分攤計算結果
                    $employeeEntries['employee_commissions_id'] = $employeeComID;
                    $employeeEntries['billing_statement_id'] = $customerBilling->id;
                    $employeeEntries['report_date'] = $customerBilling->report_date;
                    $employeeEntries['contract_length'] = $contractMonths;
                    $employeeEntries['client_code'] = $clientCode;
                    $employeeEntries['created_at'] = gmdate('Y-m-d h:i:s');
                    $employeeEntries['created_by'] = $userID;
                    $employeeEntries['active'] = 1;

                    EmployeeCommissionEntry::insert($employeeEntries);
                }
            }

            DB::commit();
        } catch (\ErrorException $e) {
            Log::error("CalculateCommission error" . $e);

            DB::rollBack();
            return;
        }
    }

    private function getCommissionRate(float $amount): float
    {
        $setting = EmployeeCommissionRule::where('active', 1)->first();

        if (!$setting) {
            Log::error("CalculateCommission employee_ops_commission_rules is empty");
            return false;
        }

        if ($amount > $setting->total_commission_threshold_1 && $amount <= $setting->total_commission_threshold_2) {
            return (float)$setting->total_tier_1;
        }

        if ($amount > $setting->total_commission_threshold_2 && $amount <= $setting->total_commission_threshold_3) {
            return (float)$setting->total_tier_2;
        }

        if ($amount > $setting->total_commission_threshold_3) {
            return (float)$setting->total_tier_3;
        }

        return 0;
    }

    private function getCustomerMonthlyFeeRate(float $hKDCommission, string $clientCode, int $roleID, int $contractYears)
    {
        $employeeRule = EmployeeMonthlyFeeRule::where('active', 1)
            ->where('client_code', $clientCode)
            ->where('role_id', $roleID)
            ->first();

        if (!$employeeRule) {
            Log::error("CalculateCommission employee_monthly_fee_rules is empty");
            return false;
        }

        if ($employeeRule->is_tiered_rate === 'F') {
            $customerRate = ($contractYears <= 1) ? $employeeRule->rate_base : $employeeRule->rate;
        } else {
            //is_tiered_rate 為 true
            if ($contractYears <= 1) {
                //一年內的約
                $customerRate = ($hKDCommission <= $employeeRule->threshold) ? $employeeRule->tier_1_first_year
                    : $employeeRule->tier_2_first_year;
            } else {
                //大於一年的約
                $customerRate = ($hKDCommission <= $employeeRule->threshold) ? $employeeRule->tier_1_over_a_year
                    : $employeeRule->tier_2_over_a_year;
            }
        }

        return $customerRate;
    }

    private function getOpsCommissionRate(int $contractMonths)
    {
        $opRules = EmployeeCommissionRule::where('active', 1)->first();

        if ($contractMonths >= 1 && $contractMonths <= $opRules->month_threshold_1) {
            $rate = $opRules->tier_1;
        }

        if ($contractMonths > $opRules->month_threshold_1 && $contractMonths <= $opRules->month_threshold_2) {
            $rate = $opRules->tier_2;
        }

        if ($contractMonths > $opRules->month_threshold_2) {
            $rate = $opRules->tier_3;
        }

        return $rate ?? 0;
    }
}

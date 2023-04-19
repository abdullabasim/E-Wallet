<?php

namespace App\ServicesData\Voucher;


use App\Mail\VoucherEmail;
use App\ServicesData\FileCompress\FileCompressZipService as fileCompressZipService;
use App\ServicesData\ExcelExport\ExportExcelService as exportExcelService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Maatwebsite\Excel\Facades\Excel;
use Ramsey\Uuid\Uuid;
use App\Models\Voucher as voucherModel;

class VoucherService
{


    /**
     * @param $data
     * @return bool|void
     *
     * Generate vouchers for admin
     */
    public static function vouchersGenerator($data)
    {
        DB::beginTransaction();
        try {

            $faker = \Faker\Factory::create();
            $fileName = Uuid::uuid4() . '(' . Carbon::now()->format('YmdHs') . ')';
            foreach (range(1, $data['total']) as $index) {


                $pin = $faker->unique()->numerify('###############');
                $amount = $data['amount'];
                $startAt = isset($data['startDate']) ? Carbon::parse($data['startDate']) : null;
                $expiresAt = isset($data['endDate']) ? Carbon::parse($data['endDate']) : null;
                $uuid = $faker->unique()->numerify('##########');


                $exportedData[$index]['serialNumber'] = $uuid;
                $exportedData[$index]['pin'] = $pin;
                $exportedData[$index]['amount'] = $amount;


                voucherModel::create([
                    'pin' => $pin,
                    'amount' => $amount,
                    'starts_at' => $startAt,
                    'expires_at' => $expiresAt,
                    'is_enabled' => 1,
                    'is_used' => 0,
                    'uuid' => $uuid,
                    'batch' => $fileName
                ]);

            }


            $file = self::exportVoucherExcelAndCompress($exportedData, $fileName);


            if ($file) {

                Mail::to([['email' => 'abdulaleem.eduba@gmail.com', 'name' => 'Abdulaleem Eduba'],
                    ['email' => 'info.eduba7@gmail.com', 'name' => 'Info Eduba']])
                    ->cc('abdulla@edubaedu.com')
                    ->send(new VoucherEmail($file));

                File::delete($file);

                DB::commit();

                return true;
            }
            return false;

        } catch (\Exception $e) {

            DB::rollback();
        }
    }

    /**
     * @param $data
     * @param $fileName
     * @return string|null
     *
     * Export the vouchers to excel and compress
     */
    private  function exportVoucherExcelAndCompress($data, $fileName)
    {


        $storagePath = \Storage::disk()->getDriver()->getAdapter()->getPathPrefix() . 'VouchersFiles' . DIRECTORY_SEPARATOR;
        $excelPath = 'VouchersFiles' . DIRECTORY_SEPARATOR . $fileName . '.xlsx';
        $excelColumnNames = ['SerialNumber',
            'Pin',
            'Amount',
        ];

        $excel = Excel::store(new exportExcelService($data, $excelColumnNames), $excelPath);

        if ($excel)
            return fileCompressZipService::executeZipCompress($storagePath, $fileName, true);

        return null;


    }

    /**
     * @param $voucherPin
     * @param $user
     * @return string
     *
     * voucher redeem
     */
    public static function voucherRedeem($voucherPin, $user)
    {
        $voucher = voucherModel::whereEncrypted('pin', $voucherPin)->where('is_enabled', true)->first();


        if ($voucher && $voucher->is_used === false) {
            $user->deposit($voucher->amount);
            $voucher->is_used = true;
            $voucher->save();

            $voucher->transaction()->create([
                'user_id' => $user->id,
                'voucher_id' => $voucher->id
            ]);

            return 'done';

        } elseif ($voucher && $voucher->is_used === true)
            return 'used';

        return 'not_found';

    }


    /**
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * Get all vouchers with filter for admin
     */
    public static function vouchersFilterAdmin($request)
    {

        $vouchers = voucherModel::query()->with(['transaction.user:id,name,wallet_user,email,is_blocked,user_type', 'transaction.user.merchants:id,name,email,merchant_key,white_list,is_active']);
        if ($request->amount)
            $vouchers->where('amount', $request->amount);
        if ($request->start_at)
            $vouchers->whereDate('starts_at', Carbon::parse($request->start_at));


        if ($request->expires_at)
            $vouchers->whereDate('expires_at', Carbon::parse($request->expires_at));

        if ($request->has('is_enabled'))
            $vouchers->where('is_enabled', $request->is_enabled);


        if ($request->has('is_used'))
            $vouchers->where('is_used', $request->is_used);


        if ($request->serial_number)
            $vouchers->where('uuid', 'like', '%' . $request->serial_number . '%');
        if ($request->batch)
            $vouchers->where('batch', 'like', '%' . $request->batch . '%');
        if ($request->used_by) {
            $usedBy = $request->used_by;
            $vouchers->whereHas('transaction.user', function ($query) use ($usedBy) {
                $query->where('name', 'like', '%' . $usedBy . '%');
            });
        }
        if ($request->wallet_id) {
            $wallet = $request->wallet_id;
            $vouchers->whereHas('transaction.user', function ($query) use ($wallet) {
                $query->where('wallet_user', $wallet);
            });
        }
        if ($request->merchant) {
            $merchant = $request->merchant;
            $vouchers->whereHas('transaction.user.merchants', function ($query) use ($merchant) {
                $query->where('name', 'like', '%' . $merchant . '%');
            });
        }


        if ($request->sort)
            $vouchers->orderBy('id', $request->sort);


        return $vouchers->paginate(isset($request->limit) ? $request->limit : 30)->withQueryString();
    }

    /**
     * @param $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     * Get all used vouchers with filter for company
     */
    public static function vouchersFilterClient($request)
    {
        $merchantKey = $request->header('key');

        $vouchers = voucherModel::query()
            ->with(['transaction.user:id,name,wallet_user,email,is_blocked,user_type'])
            ->where('is_enabled', 1)
            ->where('is_used', 1)
            ->whereHas('transaction.user.merchants', function ($query) use ($merchantKey) {
                $query->where('merchant_key', $merchantKey);
            });

        if ($request->amount)
            $vouchers->where('amount', $request->amount);
        if ($request->start_at)
            $vouchers->whereDate('starts_at', Carbon::parse($request->start_at));


        if ($request->expires_at)
            $vouchers->whereDate('expires_at', Carbon::parse($request->expires_at));


        if ($request->serial_number)
            $vouchers->where('uuid', 'like', '%' . $request->serial_number . '%');
        if ($request->batch)
            $vouchers->where('batch', 'like', '%' . $request->batch . '%');
        if ($request->used_by) {
            $usedBy = $request->used_by;
            $vouchers->whereHas('transaction.user', function ($query) use ($usedBy) {
                $query->where('name', 'like', '%' . $usedBy . '%');
            });
        }
        if ($request->wallet_id) {
            $wallet = $request->wallet_id;
            $vouchers->whereHas('transaction.user', function ($query) use ($wallet) {
                $query->where('wallet_user', $wallet);
            });
        }


        if ($request->sort)
            $vouchers->orderBy('id', $request->sort);


        return $vouchers->paginate(isset($request->limit) ? $request->limit : 30)->withQueryString();
    }

    /**
     * @return array
     *
     * reporting section to get total vouchers , used vouchers   ,nor used vouchers and expired vouchers
     */
    public static function reportingAdmin()
    {
        $vouchers ['totalVoucher'] = voucherModel::count();
        $vouchers ['usedVoucher'] = voucherModel::where('is_used', true)->count();
        $vouchers ['notUsedVoucher'] = voucherModel::where('is_used', false)->count();
        $vouchers ['expiredVoucher'] = voucherModel::where('expires_at', '>=', Carbon::now())->count();

        return $vouchers;


    }


}

<?php
/**
 * Created by PhpStorm.
 * User: sajjad
 * Date: 19/1/18
 * Time: 2:50 PM
 */

namespace App\Repository\Services\Payment;


use Maatwebsite\Excel\Excel;

class PaymentService
{
    public function toExcel($invoices)
    {
        $excel = resolve(Excel::class);
        $res = [[
            '#',
            'تاریخ',
            'نام بسته',
            'تعداد اشیا',
            'تعداد پروژه',
            'مدت بسته',
            'مبلغ',
            'درگاه',
            'وضعیت',
            'تخفیف',
        ]];
        $res = array_merge($res, $invoices->map(function ($item, $key) {
            return [
                $key + 1,
                $item['updated_at'],
                $item['package']['name'],
                $item['package']['node_num'],
                $item['package']['project_num'],
                $item['package']['time'],
                $item['package']['price'],
                'زرین پال',
                $item['status'] ? 'موفق' : 'ناموفق',
            ];
        })->toArray());

        return response(
            $excel->create(
                'invoices.xls',
                function ($excel) use ($res) {
                    $excel->sheet(
                        'Invoices',
                        function ($sheet) use ($res) {
                            $sheet->fromArray($res, null, 'A1', false, false);
                        }
                    );
                }
            )->string('xls')
        )
            ->header('Content-Disposition', 'attachment; filename="things.xls"')
            ->header('Content-Type', 'application/vnd.ms-excel; charset=UTF-8');
    }
}
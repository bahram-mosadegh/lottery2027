<?php

namespace App\DataTables;

use App\Models\Coupon;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class CouponDataTable extends DataTable
{
    /**
     * Build DataTable class.
     *
     * @param mixed $query Results from query() method.
     * @return \Yajra\DataTables\DataTableAbstract
     */
    public function dataTable($query)
    {
        return datatables()
            ->eloquent($query)
            ->editColumn('type', function(Coupon $coupon) {
                return __('message.'.$coupon->type);
            })->addColumn('action', function (Coupon $coupon){
                return $coupon->id;
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Coupon $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Coupon $model)
    {
        return $model->newQuery();
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('coupon-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->initComplete("function () {
                            this.api().columns([2]).every(function () {
                                var column = this;
                                var select = $('<select class=\"form-control\"> <option value=\"\">-- ".__('message.select')." --</option><option value=\"percent\">".__('message.percent')."</option><option value=\"discount\">".__('message.discount')."</option></select>')
                                    .appendTo($(column.footer()).empty())
                                    .on('change', function () {
                                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                                    });
                            });

                            this.api().columns([0,1,3]).every(function () {
                                var column = this;
                                var input = document.createElement(\"input\");
                                input.className = 'form-control';
                                input.placeholder = '".__('message.search')."';
                                $(input).appendTo($(column.footer()).empty())
                                .on('keyup', delay(function () {
                                        column.search($(this).val(), false, false, true).draw();
                                    }, 500));
                            });

                            this.api().columns([4]).every(function () {
                                var column = this;
                                $('<button class=\"w-100 btn btn-outline-secondary\" style=\"margin-bottom: 0px;\">".__('message.clear_filters')."</button>')
                                    .appendTo($(column.footer()).empty())
                                    .on('click', function () {
                                        window.location.href = \"".url('coupons')."\";
                                    });
                            });
                    }")
                    ->dom('lBfrtip')
                    ->buttons(
                        Button::make('excel')->attr(['class' => 'btn btn-outline-secondary btn-md']),
                    )
                    ->parameters([
                        'pageLength' => 10,
                        'searchDelay' => '500',
                        'paging' => true,
                        'searching' => true,
                        'info' => true,
                        'responsive' => true,
                        'language' => [
                            'url' => url('../assets/data-tables/fa.json')
                        ],
                    ])
                    ->orderBy(0, 'desc');
    }

    /**
     * Get columns.
     *
     * @return array
     */
    protected function getColumns()
    {
        return [
            Column::make('code')->title(__('message.code'))->className('dt-body-center')->width('5%')->responsivePriority('1'),
            Column::make('ammount')->title(__('message.amount'))->render('number_separator(data)')->className('dt-body-center')->width('5%')->responsivePriority('3'),
            Column::make('type')->title(__('message.type'))->className('dt-body-center')->width('5%')->responsivePriority('4'),
            Column::make('expire_date')->title(__('message.expire_date'))->className('dt-body-center')->width('5%'),
            Column::computed('action')->title(__('message.action'))->render('\'<a href="/edit_coupon/\'+data+\'" class="mx-3"><i class="fa fa-pencil text-secondary"></i></a><span onclick="delete_coupon(\'+data+\', \\\'\'+full.code+\'\\\');"><i class="cursor-pointer fas fa-trash text-secondary"></i></span>\'')->responsivePriority('2')->className('dt-body-center')->width('5%'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'Coupons_' . date('YmdHis');
    }
}

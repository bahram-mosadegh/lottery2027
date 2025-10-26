<?php

namespace App\DataTables;

use App\Models\Applicant;
use Yajra\DataTables\Html\Button;
use Yajra\DataTables\Html\Column;
use Yajra\DataTables\Html\Editor\Editor;
use Yajra\DataTables\Html\Editor\Fields;
use Yajra\DataTables\Services\DataTable;

class ApplicantDataTable extends DataTable
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
            ->addColumn('link', function(Applicant $applicant) {
                return url('step_zero?mobile='.$applicant->mobile.'&registration_type='.$applicant->registration_type);
            })->addColumn('has_rejected', function(Applicant $applicant) {
                $has_rejected = 0;
                if ($applicant->payment_status == 'paid') {
                    $has_rejected = $applicant->face_image_status == 'rejected' || $applicant->passport_image_status == 'rejected' ? 1 : 0;
            
                    if (!$has_rejected) {
                        if ($applicant->spouse) {
                            $has_rejected = $applicant->spouse->face_image_status == 'rejected' || $applicant->spouse->passport_image_status == 'rejected' ? 1 : 0;
                        }

                        if (!$has_rejected) {
                            foreach ($applicant->adult_children as $adult_child) {
                                if ($adult_child->face_image_status == 'rejected' || $adult_child->passport_image_status == 'rejected') {
                                    $has_rejected = 1;
                                    break;
                                }
                            }
                        }

                        if (!$has_rejected) {
                            foreach ($applicant->children as $child) {
                                if ($child->face_image_status == 'rejected' || $child->passport_image_status == 'rejected') {
                                    $has_rejected = 1;
                                    break;
                                }
                            }
                        }
                    }
                }
                
                return $has_rejected;
            })->editColumn('gender', function(Applicant $applicant) {
                return __('message.'.$applicant->gender);
            })->editColumn('registration_type', function(Applicant $applicant) {
                return __('message.'.$applicant->registration_type);
            })->editColumn('marital', function(Applicant $applicant) {
                return __('message.'.$applicant->marital);
            })->editColumn('marital_status', function(Applicant $applicant) {
                return __('message.'.$applicant->marital_status);
            })->editColumn('payment_status', function(Applicant $applicant) {
                return __('message.'.$applicant->payment_status);
            })->editColumn('payment_status_class', function(Applicant $applicant) {
                if ($applicant->payment_status == 'paid') {
                    $class = 'success';
                } else {
                    $class = 'danger';
                }
                return $class;
            })->editColumn('sms_status', function(Applicant $applicant) {
                return __('message.'.$applicant->sms_status);
            })->editColumn('sms_status_class', function(Applicant $applicant) {
                if ($applicant->sms_status == 'success') {
                    $class = 'success';
                } elseif ($applicant->sms_status == 'not_sent') {
                    $class = 'secondary';
                } else {
                    $class = 'danger';
                }
                return $class;
            })->editColumn('created_at', function(Applicant $applicant) {
                $time = strtotime($applicant->created_at->format("Y-m-d H:i:s"). ' +3 hours +30 minutes');
                return \Helper::gregorian_to_jalali(date('Y', $time), date('m', $time), date('d', $time), '-').' '.date('H:i:s', $time);
            });
    }

    /**
     * Get query source of dataTable.
     *
     * @param \App\Models\Applicant $model
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(Applicant $model)
    {
        $q = $model->newQuery();
        if (auth()->user()->role == 'agent') {
            $q->where('user_id', auth()->user()->id);
        }
        return $q;
    }

    /**
     * Optional method if you want to use html builder.
     *
     * @return \Yajra\DataTables\Html\Builder
     */
    public function html()
    {
        return $this->builder()
                    ->setTableId('applicant-table')
                    ->columns($this->getColumns())
                    ->minifiedAjax()
                    ->initComplete("function () {
                            this.api().columns([1]).every(function () {
                                var column = this;
                                var select = $('<select class=\"form-control\"> <option value=\"\">-- ".__('message.select')." --</option><option value=\"online\">".__('message.online')."</option><option value=\"onsite\">".__('message.onsite')."</option><option value=\"agent\">".__('message.agent')."</option></select>')
                                    .appendTo($(column.footer()).empty())
                                    .on('change', function () {
                                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                                    });
                            });

                            this.api().columns([5]).every(function () {
                                var column = this;
                                var select = $('<select class=\"form-control\"> <option value=\"\">-- ".__('message.select')." --</option><option value=\"single\">".__('message.single')."</option><option value=\"married\">".__('message.married')."</option></select>')
                                    .appendTo($(column.footer()).empty())
                                    .on('change', function () {
                                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                                    });
                            });

                            this.api().columns([9]).every(function () {
                                var column = this;
                                var select = $('<select class=\"form-control\"> <option value=\"\">-- ".__('message.select')." --</option><option value=\"paid\">".__('message.paid')."</option><option value=\"unpaid\">".__('message.unpaid')."</option></select>')
                                    .appendTo($(column.footer()).empty())
                                    .on('change', function () {
                                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                                    });
                            });

                            this.api().columns([10]).every(function () {
                                var column = this;
                                var select = $('<select class=\"form-control\"> <option value=\"\">-- ".__('message.select')." --</option><option value=\"not_sent\">".__('message.not_sent')."</option><option value=\"success\">".__('message.success')."</option><option value=\"fail\">".__('message.fail')."</option></select>')
                                    .appendTo($(column.footer()).empty())
                                    .on('change', function () {
                                        var val = $.fn.dataTable.util.escapeRegex($(this).val());
                                        column.search(val ? '^' + val + '$' : '', true, false).draw();
                                    });
                            });

                            this.api().columns([11]).every(function () {
                                var column = this;
                                var input = document.createElement(\"input\");
                                input.className = 'form-control';
                                input.placeholder = '".__('message.search')."';
                                input.disabled = 'true';
                                $(input).appendTo($(column.footer()).empty());
                            });

                            this.api().columns([0,2,3,4,6,7,8,12]).every(function () {
                                var column = this;
                                var input = document.createElement(\"input\");
                                input.className = 'form-control';
                                input.placeholder = '".__('message.search')."';
                                $(input).appendTo($(column.footer()).empty())
                                .on('keyup', delay(function () {
                                        column.search($(this).val(), false, false, true).draw();
                                    }, 500));
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
            Column::make('id')->title(__('message.id'))->render('\'<a href="\'+full.link+\'" target="_blank">\'+data+\'</a>\'')->responsivePriority('1')->className('dt-body-center')->width('5%'),
            Column::make('registration_type')->title(__('message.registration_type'))->className('dt-body-center')->width('5%'),
            Column::make('name')->title(__('message.name'))->responsivePriority('4')->className('dt-body-center')->width('5%'),
            Column::make('last_name')->title(__('message.last_name'))->responsivePriority('5')->className('dt-body-center')->width('5%'),
            Column::make('mobile')->title(__('message.mobile'))->responsivePriority('3')->className('dt-body-center')->width('5%'),
            Column::make('marital')->title(__('message.marital'))->responsivePriority('6')->className('dt-body-center')->width('5%'),
            Column::make('adult_children_count')->title(__('message.adult_children_count'))->responsivePriority('7')->className('dt-body-center')->width('5%'),
            Column::make('children_count')->title(__('message.children_count'))->responsivePriority('8')->className('dt-body-center')->width('5%'),
            Column::make('price')->title(__('message.price'))->render('number_separator(data)')->responsivePriority('9')->className('dt-body-center')->width('5%'),
            Column::make('payment_status')->title(__('message.payment_status'))->responsivePriority('2')->render('\'<button type="button" class="btn bg-gradient-\'+full.payment_status_class+\' btn-sm mt-0 mb-0">\'+data+\'</button>\'')->className('dt-body-center')->width('5%'),
            Column::make('sms_status')->title(__('message.sms_status'))->render('\'<button type="button" class="btn bg-gradient-\'+full.sms_status_class+\' btn-sm mt-0 mb-0">\'+data+\'</button>\'')->className('dt-body-center')->width('5%'),
            Column::computed('has_rejected')->title(__('message.has_rejected'))->render('data == 1 ? \'<i class="fa fa-circle btn-outline-danger" aria-hidden="true"></i>\' : \'\'')->className('dt-body-center')->responsivePriority('10')->width('5%'),
            Column::make('created_at')->title(__('message.created_at'))->render('\'<div dir="ltr">\'+data+\'</div>\'')->className('dt-body-center')->width('5%'),
        ];
    }

    /**
     * Get filename for export.
     *
     * @return string
     */
    protected function filename()
    {
        return 'applicants_' . date('YmdHis');
    }
}

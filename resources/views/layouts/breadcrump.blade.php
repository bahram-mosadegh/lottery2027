<div class="row">
    <div class="col-12">
    <breadcrumb>
        <nav>
            <ol class="cd-breadcrumb triangle custom-icons">
                <li @if(\Request::is('step_one*')) class="current" @endif>{!!$steps['step_one'] ? '<a href="'.url('step_one/'.($applicant ? $applicant->id : '')).'">' : '<em>'!!}<i class="fa fa-users" aria-hidden="true"></i> <span class="breadcrumb-title">{{__('message.step_one')}}</span>{!!$steps['step_one'] ? '</a>' : '</em>'!!}</li>

                <li @if(\Request::is('step_two*')) class="current" @endif>{!!$steps['step_two'] ? '<a href="'.url('step_two/'.($applicant ? $applicant->id : '')).'">' : '<em>'!!}<i class="fa fa-map-marker" aria-hidden="true"></i> <span class="breadcrumb-title">{{__('message.step_two')}}</span>{!!$steps['step_two'] ? '</a>' : '</em>'!!}</li>
                
                <li @if(\Request::is('step_three*')) class="current" @endif>{!!$steps['step_three'] ? '<a href="'.url('step_three/'.($applicant ? $applicant->id : '')).'">' : '<em>'!!}<i class="fa fa-file-image" aria-hidden="true"></i> <span class="breadcrumb-title">{{__('message.step_three')}}</span>{!!$steps['step_three'] ? '</a>' : '</em>'!!}</li>

                <li @if(\Request::is('step_four*') && \Request::isMethod('get')) class="current" @endif>{!!$steps['step_four'] ? '<a href="'.url('step_four/'.($applicant ? $applicant->id : '')).'">' : '<em>'!!}<i class="fa fa-id-card" aria-hidden="true"></i> <span class="breadcrumb-title">{{__('message.step_four')}}</span>{!!$steps['step_four'] ? '</a>' : '</em>'!!}</li>

                <li @if(\Request::is('step_four*') && \Request::isMethod('post')) class="current" @endif>{!!$steps['step_five'] ? '<a href="'.url('step_four/'.($applicant ? $applicant->id : '')).'">' : '<em>'!!}<i class="fa fa-money" aria-hidden="true"></i> <span class="breadcrumb-title">{{__('message.step_five')}}</span>{!!$steps['step_five'] ? '</a>' : '</em>'!!}</li>

                <li @if(\Request::is('step_six*')) class="current" @endif>{!!$steps['step_six'] ? '<a href="'.url('step_six/'.($applicant ? $applicant->id : '')).'">' : '<em>'!!}<i class="fa fa-flag-checkered" aria-hidden="true"></i> <span class="breadcrumb-title">{{__('message.step_six')}}</span>{!!$steps['step_six'] ? '</a>' : '</em>'!!}</li>
            </ol>
        </nav>
    </breadcrumb>
    </div>
</div>
<div id="kt_app_content_container" class="app-container container-xxl">
    <!--begin::Layout-->
    <div class="d-flex flex-column flex-xl-row">
        <!--begin::Sidebar-->
        @include('hr::Archive.Emp')
        <!--end::Sidebar-->
        <!--begin::Content-->
        <div class="flex-lg-row-fluid ms-lg-15">
            <!--begin:::Tabs-->
            <ul class="nav nav-custom nav-tabs nav-line-tabs nav-line-tabs-2x border-0 fs-4 fw-semibold mb-8"
                role="tablist">
                <!--begin:::Tab item-->
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary pb-4 @if ($page == 'profile')active @endif"
                        href="{{ route('hr.Archive.show', $Employee->id) }}"> معلومات العقد</a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary pb-4   @if ($page == 'penalties') active @endif " href="{{ route('hr.Archive.penalties', $Employee->id) }}">
                        @lang('hr::models/hr_penalties.plural') </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary pb-4   @if ($page == 'advances') active @endif " href="{{ route('hr.Archive.advances', $Employee->id) }}">
                        @lang('hr::models/hr_advances.plural') </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary pb-4   @if ($page == 'rewards') active @endif " href="{{ route('hr.Archive.rewards', $Employee->id) }}">
                        @lang('hr::models/hr_rewards.plural') </a>
                </li>
                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary pb-4   @if ($page == 'custodies') active @endif " href="{{ route('hr.Archive.custodies', $Employee->id) }}">
                        @lang('hr::models/hr_custodies.plural') </a>
                </li>

                <li class="nav-item" role="presentation">
                    <a class="nav-link text-active-primary pb-4   @if ($page == 'holidays') active @endif " href="{{ route('hr.Archive.holidays', $Employee->id) }}">
                        @lang('hr::models/hr_holidays.plural') /@lang('hr::models/hr_absentrequest.plural') </a>
                </li>


            </ul>

            <!--end:::Tabs-->
            <!--begin:::Tab content-->
            <div class="tab-content" id="myTabContent">
                <!--begin:::Tab pane-->
                <div class="tab-pane fade show active" id="kt_customer_view_overview_tab" role="tabpanel">
                    <!--begin::Card-->

                    @if ($page == 'profile')
                        @include('hr::Archive.bagfrist')
                    @elseif($page == 'penalties')
                        @include('hr::Archive.penalties')
                    @elseif($page == 'advances')
                        @include('hr::Archive.advances')
                    @elseif($page == 'rewards')
                        @include('hr::Archive.rewards')
                    @elseif($page == 'custodies')
                        @include('hr::Archive.custodies')
                    @elseif($page == 'holidays')
                        @include('hr::Archive.holidays')
                    @endif




                </div>

            </div>
            <!--end:::Tab content-->
        </div>
        <!--end::Content-->
    </div>

</div>

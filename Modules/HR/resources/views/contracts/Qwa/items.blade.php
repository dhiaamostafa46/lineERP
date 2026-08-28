

@if (count($contract->Contractitem) > 0)
    <div class="row">
        <div class="col-6 Arabic_section">
            <h3 class="headtext"> {{ trans('hr::models/hr_contract_items.plural', [], 'ar') }} </h3>
            <div class="lh-lg textprograf ">
                <ul>
                    @foreach ($contract->Contractitem as $item)
                        <li>
                            {{ $item->Desc_ar }}
                        </li>
                    @endforeach
                </ul>
            </div>


        </div>
        <div class="col-6 English_section">

            <h3 class="headtext"> {{ trans('hr::models/hr_contract_items.plural', [], 'en') }}</h3>
            <div class="lh-lg textprograf ">
                <ul>
                    @foreach ($contract->Contractitem as $item)
                        <li>
                            {{ $item->Desc_En }}
                        </li>
                    @endforeach
                </ul>
            </div>


        </div>
    </div>
@endif

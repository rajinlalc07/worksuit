@php
    $currentDay = \Carbon\Carbon::parse(now(company()->timezone)->toDateTimeString())
        ->startOfDay()
        ->setTimezone('UTC');
@endphp
<div class="col-sm-12 mt-3">
    <x-cards.data class="e-d-info mb-3" :title="__('modules.dashboard.contractDate')" padding="false">
        <x-table>
            @if (in_array('admin', user_roles()))
                @foreach ($contract as $contract)
                    @php
                        $contractDate = Carbon\carbon::parse($contract->contract_end_date);
                        $diffInDays = $contractDate->copy()->diffForHumans($currentDay);
                    @endphp
                    <tr>
                        <td class="pl-20">
                            <x-employee :user="$contract->user" />
                        </td>


                        <td class="pr-20 text-right">
                            @if ($contractDate->setTimezone(company()->timezone)->isToday())
                                <span class="badge badge-light text-light p-2">@lang('app.today')</span>
                            @elseif($diffInDays == '1 week after')
                                <span class="badge badge-light text-warning p-2">{{ $diffInDays }}</span>
                            @else
                                <span class="badge badge-light p-2">{{ $diffInDays }}</span>
                            @endif

                            <br>
                            @if ($contractDate->setTimezone(company()->timezone)->isToday())
                                <span class="text-success f-12">@lang('messages.contractMessage')
                                    {{ $contractDate->format($company->date_format) }}</span>
                            @elseif($diffInDays == '1 week after')
                                <span class="text-warning f-12">@lang('messages.contractMessage')
                                    {{ $contractDate->format($company->date_format) }}</span>
                            @else
                                <span class="f-12">@lang('messages.contractMessage')
                                    {{ $contractDate->format($company->date_format) }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    @php
                        $contractDate = Carbon\carbon::parse($contract->contract_end_date);
                        $diffInDays = $contractDate->copy()->diffForHumans($currentDay);
                    @endphp
                    <td class="pl-20">
                        <span @class([
                            'f-12',
                            'text-success' => $contractDate->setTimezone(company()->timezone)->isToday(),
                            'text-warning' => $diffInDays == '1 week after',
                        ])>{{ $contractDate->format($company->date_format) }}</span>
                    </td>

                    <td class="pr-20 text-right">
                        @if ($contractDate->setTimezone(company()->timezone)->isToday())
                            <span class="badge badge-light text-light p-2">@lang('app.today')</span>
                        @elseif($diffInDays == '1 week after')
                            <span class="badge badge-light text-warning p-2">{{ $diffInDays }}</span>
                        @else
                            <span class="badge badge-light p-2">{{ $diffInDays }}</span>
                        @endif
                    </td>
                </tr>
            @endif
        </x-table>
    </x-cards.data>
</div>

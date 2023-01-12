@php
    $currentDay = \Carbon\Carbon::parse(now(company()->timezone)->toDateTimeString())
        ->startOfDay()
        ->setTimezone('UTC');
@endphp
<div class="col-sm-12 mt-2">
    <x-cards.data class="e-d-info mb-3" :title="__('modules.dashboard.probationDate')" padding="false">
        <x-table>
            @if (in_array('admin', user_roles()))
                @foreach ($probation as $probation)
                    @php
                        $probationDate = Carbon\carbon::parse($probation->probation_end_date);
                        $diffInDays = $probationDate->copy()->diffForHumans($currentDay);
                    @endphp
                    <tr>
                        <td class="pl-20">
                            <x-employee :user="$probation->user" />
                        </td>

                        <td class="pr-20 text-right">
                            @if ($probationDate->setTimezone(company()->timezone)->isToday())
                                <span class="badge badge-light text-success p-2">@lang('app.today')</span>
                            @elseif($diffInDays == '1 week after')
                                <span class="badge badge-light text-warning p-2">{{ $diffInDays }}</span>
                            @else
                                <span class="badge badge-light p-2">{{ $diffInDays }}</span>
                            @endif

                            <br>
                            @if ($probationDate->setTimezone(company()->timezone)->isToday())
                                <span class="text-success f-11">@lang('messages.probationMessage')
                                    {{ $probationDate->format($company->date_format) }}</span>
                            @elseif($diffInDays == '1 week after')
                                <span class="text-warning f-11">@lang('messages.probationMessage')
                                    {{ $probationDate->format($company->date_format) }}</span>
                            @else
                                <span class="f-11">@lang('messages.probationMessage')
                                    {{ $probationDate->format($company->date_format) }}</span>
                            @endif

                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    @php
                        $probationDate = Carbon\carbon::parse($probation->probation_end_date);
                        $diffInDays = $probationDate->copy()->diffForHumans($currentDay);
                    @endphp
                    <td class="pl-20">
                        @if ($probationDate->setTimezone(company()->timezone)->isToday())
                            <span class="text-success f-12">@lang('messages.probationMessage')
                                {{ $probationDate->format($company->date_format) }}</span>
                        @elseif($diffInDays == '1 week after')
                            <span class="text-warning f-12">@lang('messages.probationMessage')
                                {{ $probationDate->format($company->date_format) }}</span>
                        @else
                            <span class="f-12">@lang('messages.probationMessage')
                                {{ $probationDate->format($company->date_format) }}</span>
                        @endif
                    </td>

                    <td class="pr-20 text-right">
                        @if ($probationDate->setTimezone(company()->timezone)->isToday())
                            <span class="badge badge-light text-success p-2">@lang('app.today')</span>
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

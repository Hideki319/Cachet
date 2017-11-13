@if($component_groups->isNotEmpty())
@foreach($component_groups as $componentGroup)
<ul class="list-group components">
    @if($componentGroup->enabled_components->isNotEmpty())
    <li class="list-group-item group-name">
        <i class="{{ $componentGroup->collapse_class }} group-toggle"></i>
        <strong>{{ $componentGroup->name }}</strong>

        <div class="pull-right">
            <!-- <i class="ion ion-ios-circle-filled text-component-{{ $componentGroup->lowest_status }} {{ $componentGroup->lowest_status_color }}" data-toggle="tooltip" title="{{ $componentGroup->lowest_human_status }}"></i> -->
            <i class="ion {{ $componentGroup->lowest_status_icon }} text-component-{{ $componentGroup->lowest_status }} {{ $componentGroup->lowest_status_color }}" data-toggle="tooltip" title="{{ $componentGroup->lowest_human_status }}"></i>
        </div>
    </li>

    <div class="group-items {{ $componentGroup->is_collapsed ? "hide" : null }}">
        @each('partials.component', $componentGroup->enabled_components()->orderBy('order')->get(), 'component')
    </div>
    @endif
</ul>
@endforeach
@endif

@if($ungrouped_components->isNotEmpty())
<ul class="list-group components">
    <li class="list-group-item group-name">
        <strong>{{ trans('cachet.components.group.other') }}</strong>

        <div class="pull-right">
            <i class="ion ion-ios-circle-filled text-component-{{ $ungrouped_components->max('status') }} {{ $ungrouped_components->sortByDesc('status')->first()->status_color }}" data-toggle="tooltip" title="{{ $ungrouped_components->sortByDesc('status')->first()->human_status }}"></i>
        </div>
    </li>

    @each('partials.component', $ungrouped_components, 'component')
</ul>
@endif

<div style="display: flex; justify-content: space-around; padding: 0 15px;">
    <div>
        <i class="ion ion-ios-help-outline text-component-0"></i>
        <small class="text-component-0">Unknown</small>
    </div>
    <div>
        <i class="ion ion-android-done text-component-1"></i>
        <small class="text-component-1">Operational</small>
    </div>
    <div>
        <i class="ion ion-ios-minus text-component-2"></i>
        <small class="text-component-2">Performance Issues</small>
    </div>
    <div>
        <i class="ion ion-android-warning text-component-3"></i>
        <small class="text-component-3">Partial Outage</small>
    </div>
    <div>
        <i class="ion ion-close-round text-component-4"></i>
        <small class="text-component-4">Major Outage</small>
    </div>
</div>
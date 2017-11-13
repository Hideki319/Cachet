@if($component_groups->count() > 0)
@foreach($component_groups as $componentGroup)
<ul class="list-group components">
    @if($componentGroup->enabled_components->count() > 0)
    <li class="list-group-item group-name">
        <i class="{{ $componentGroup->collapse_class }} group-toggle"></i>
        <strong>{{ $componentGroup->name }}</strong>

        <div class="pull-right">
            <!-- <i class="ion ion-ios-circle-filled text-component-{{ $componentGroup->lowest_status }} {{ $componentGroup->lowest_status_color }}" data-toggle="tooltip" title="{{ $componentGroup->lowest_human_status }}"></i> -->
            <i class="ion {{ $componentGroup->lowest_status_icon }} text-component-{{ $componentGroup->lowest_status }} {{ $componentGroup->lowest_status_color }}" data-toggle="tooltip" title="{{ $componentGroup->lowest_human_status }}"></i>
        </div>
    </li>

    <div class="group-items {{ $componentGroup->is_collapsed ? "hide" : null }}">
        @foreach($componentGroup->enabled_components()->orderBy('order')->get() as $component)
        @include('partials.component', compact($component))
        @endforeach
    </div>
    @endif
</ul>
@endforeach
@endif

@if($ungrouped_components->count() > 0)
<ul class="list-group components">
    <li class="list-group-item group-name">
        <strong>{{ trans('cachet.components.group.other') }}</strong>
    </li>
    @foreach($ungrouped_components as $component)
    @include('partials.component', compact($component))
    @endforeach
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

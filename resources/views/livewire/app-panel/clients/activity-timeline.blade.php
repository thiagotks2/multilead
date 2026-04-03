<div class="timeline-container">
    <div class="timeline-line"></div>

    @foreach($activities as $activity)
        <div class="timeline-item group">
            <div class="timeline-dot group-hover:bg-primary-50"></div>

            <div class="timeline-content">
                <div class="timeline-header">
                    <p class="timeline-title">
                        @if($activity->event === 'created')
                            <span class="timeline-user">{{ $activity->causer?->name ?? 'System' }}</span> created this record.
                        @else
                            <span class="timeline-user">{{ $activity->causer?->name ?? 'System' }}</span> performed an action: {{ $activity->description }}.
                        @endif
                    </p>
                    <time class="timeline-time" datetime="{{ $activity->created_at->toIso8601String() }}">
                        {{ $activity->created_at->format('d/m/Y H:i') }}
                    </time>
                </div>

                @if(!empty($activity->properties['attributes']))
                    <div class="timeline-data-box">
                        @if($activity->event === 'created')
                            <p class="timeline-data-title">Initial Data</p>
                        @elseif($activity->event === 'updated')
                            <p class="timeline-data-title">Changes</p>
                        @else
                            <p class="timeline-data-title">Event Data</p>
                        @endif
                        
                        <div class="timeline-grid">
                            @php
                                $attributes = \Illuminate\Support\Arr::dot($activity->properties['attributes'] ?? []);
                                $oldAttributes = \Illuminate\Support\Arr::dot($activity->properties['old'] ?? []);
                            @endphp
                            
                            @foreach($attributes as $key => $value)
                                @if(!in_array($key, ['id', 'created_at', 'updated_at']))
                                    @php
                                        $hasOldValue = array_key_exists($key, $oldAttributes);
                                        $oldValue = $hasOldValue ? $oldAttributes[$key] : null;
                                        $isChanged = $hasOldValue ? ($oldValue != $value) : true;
                                    @endphp

                                    @if($activity->event !== 'updated' || $isChanged)
                                        <div class="data-group">
                                            <span class="data-label">{{ (string) str($key)->replace('.', ' ')->headline() }}</span>
                                            
                                            @if($activity->event === 'updated' && $hasOldValue)
                                                <div class="data-diff-container">
                                                    <span class="data-diff-old">{{ is_bool($oldValue) ? ($oldValue ? 'True' : 'False') : ($oldValue ?: 'Empty') }}</span>
                                                    <span class="data-diff-arrow">→</span>
                                                    <span class="data-diff-new">{{ is_bool($value) ? ($value ? 'True' : 'False') : ($value ?: 'Empty') }}</span>
                                                </div>
                                            @else
                                                <span class="data-value">{{ is_bool($value) ? ($value ? 'True' : 'False') : ($value ?: 'Empty') }}</span>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endforeach

    @if($activities->isEmpty())
        <div class="timeline-empty">
            No activity recorded yet.
        </div>
    @endif
</div>

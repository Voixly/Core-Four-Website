@php
    $source = $source ?? 'website';
    $type = $type ?? 'residential';
@endphp
@if(session('success'))
    <div class="flash">{{ session('success') }}</div>
@endif
<form class="lead-form" method="post" action="{{ route('leads.store') }}">
    @csrf
    <input type="hidden" name="source" value="{{ $source }}">
    <input type="hidden" name="type" value="{{ $type }}">
    @if(!empty($cityName))
        <input type="hidden" name="city" value="{{ $cityName }}">
    @endif
    <p class="form-note">"<span>*</span>" indicates required fields</p>
    <fieldset class="field-group">
        <legend>Name</legend>
        <div class="field-row">
            <label class="sub">
                <input name="name" required value="{{ old('name') }}">
                <span class="sub-label">First</span>
            </label>
            <label class="sub">
                <input name="last_name" value="{{ old('last_name') }}">
                <span class="sub-label">Last</span>
            </label>
        </div>
    </fieldset>
    <div class="field-row">
        <label>Email <span>*</span>
            <input name="email" type="email" required value="{{ old('email') }}">
        </label>
        <label>Phone <span>*</span>
            <input name="phone" required value="{{ old('phone') }}">
        </label>
    </div>
    <div class="field-row">
        <label>Select a Service <span>*</span>
            <select name="need">
                <option value="">Please Select a Service</option>
                @if(($type ?? 'residential') === 'commercial')
                    <option value="survey" @selected(old('need')==='survey')>Roof survey / condition report</option>
                    <option value="leak" @selected(old('need')==='leak')>Commercial leak</option>
                    <option value="maintenance" @selected(old('need')==='maintenance')>Maintenance program</option>
                    <option value="replacement" @selected(old('need')==='replacement')>Roof replacement</option>
                    <option value="coating" @selected(old('need')==='coating')>Coating / restoration</option>
                @else
                    <option value="inspection" @selected(old('need')==='inspection')>Free inspection</option>
                    <option value="leak" @selected(old('need')==='leak')>Leak repair</option>
                    <option value="storm_damage" @selected(old('need')==='storm_damage')>Storm / hail damage</option>
                    <option value="replacement" @selected(old('need')==='replacement')>Roof replacement</option>
                    <option value="financing" @selected(old('need')==='financing')>Financing options</option>
                @endif
            </select>
        </label>
        <fieldset class="field-group">
            <legend>Address</legend>
            <label class="sub">
                <input name="zip" value="{{ old('zip') }}">
                <span class="sub-label">ZIP Code</span>
            </label>
        </fieldset>
    </div>
    <label>Comments
        <textarea name="message" maxlength="600" placeholder="Please let us know what's on your mind. Have a question for us? Ask away."></textarea>
    </label>
    <button class="btn" type="submit">{{ $cta ?? 'Submit' }} <i class="fas fa-arrow-right"></i></button>
</form>

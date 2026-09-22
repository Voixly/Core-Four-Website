@if(session('success'))
    <div class="flash">{{ session('success') }}</div>
@endif
<form class="lead-form" method="post" action="{{ route('careers.store') }}">
    @csrf
    @if($errors->any())
        <p class="flash is-error">{{ $errors->first() }}</p>
    @endif
    <div class="lead-hp" aria-hidden="true">
        <label>Website <input name="website" tabindex="-1" autocomplete="off"></label>
    </div>
    <p class="form-note">"<span>*</span>" indicates required fields</p>
    <fieldset class="field-group">
        <legend>Name</legend>
        <div class="field-row">
            <label class="sub">
                <input name="name" required value="{{ old('name') }}" autocomplete="given-name">
                <span class="sub-label">First <span>*</span></span>
            </label>
            <label class="sub">
                <input name="last_name" value="{{ old('last_name') }}" autocomplete="family-name">
                <span class="sub-label">Last</span>
            </label>
        </div>
    </fieldset>
    <div class="field-row">
        <label>Email <span>*</span>
            <input name="email" type="email" required value="{{ old('email') }}" autocomplete="email">
        </label>
        <label>Phone <span>*</span>
            <input name="phone" required value="{{ old('phone') }}" autocomplete="tel">
        </label>
    </div>
    <div class="field-row">
        <label>Role <span>*</span>
            <select name="role" required>
                <option value="">Choose a role</option>
                @foreach($roles as $role)
                    <option value="{{ $role }}" @selected(old('role') === $role)>{{ $role }}</option>
                @endforeach
            </select>
        </label>
        <label>City
            <input name="city" value="{{ old('city') }}" autocomplete="address-level2">
        </label>
    </div>
    <label>Experience
        <textarea name="message" maxlength="2000" placeholder="Years on a roof, licenses, and the kind of work you want.">{{ old('message') }}</textarea>
    </label>
    <button class="btn" type="submit">Send application <i class="fas fa-arrow-right"></i></button>
</form>

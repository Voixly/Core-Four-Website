<form class="lead-form" method="post" action="{{ route('guides.download', $guide->slug) }}">
    @csrf
    @if($errors->any())
        <p class="flash is-error">{{ $errors->first() }}</p>
    @endif
    <label>Name
        <input name="name" required value="{{ old('name') }}" autocomplete="name">
    </label>
    <label>Email
        <input type="email" name="email" required value="{{ old('email') }}" autocomplete="email">
    </label>
    <label>Phone
        <input name="phone" required value="{{ old('phone') }}" autocomplete="tel">
    </label>
    <label>ZIP
        <input name="zip" value="{{ old('zip') }}" autocomplete="postal-code">
    </label>
    <input type="hidden" name="type" value="{{ $guide->audience === 'commercial' ? 'commercial' : 'residential' }}">
    <button class="btn" type="submit">{{ $guide->ctaLabel() }} <i class="fas fa-arrow-right"></i></button>
    <p class="form-note">We email the PDF and start the matching 12-month sequence. Unsubscribe anytime.</p>
</form>

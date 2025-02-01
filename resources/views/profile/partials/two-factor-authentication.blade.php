<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('Two Factor Authentication') }}</h3>
                </div>
                <div class="card-body">
                    <p class="card-text">{{ __('Add additional security to your account using two factor authentication.') }}</p>
                    <h3 class="text-lg font-medium text-dark">
                        @if ($user->two_factor_secret && $user->two_factor_confirmed_at)
                            @if ($showingConfirmation)
                                {{ __('Finish enabling two factor authentication.') }}
                            @else
                                {{ __('You have enabled two factor authentication.') }}
                            @endif
                        @else
                            {{ __('You have not enabled two factor authentication.') }}
                        @endif
                    </h3>
                    <div class="mt-3">
                        <p class="text-secondary">
                            {{ __('When two factor authentication is enabled, you will be prompted for a secure, random token during authentication. You may retrieve this token from your phone\'s Google Authenticator application.') }}
                        </p>
                    </div>
                    @if (!$user->two_factor_secret || !$user->two_factor_confirmed_at)
                        <div class="mt-5">
                            <button id="enable-2fa" class="btn btn-primary">{{ __('Enable') }}</button>
                        </div>
                    @else
                        @if ($showingQrCode)
                            <div class="mt-4">
                                <p class="font-weight-bold text-secondary">
                                    @if ($showingConfirmation)
                                        {{ __('To finish enabling two factor authentication, scan the following QR code using your phone\'s authenticator application or enter the setup key and provide the generated OTP code.') }}
                                    @else
                                        {{ __('Two factor authentication is now enabled. Scan the following QR code using your phone\'s authenticator application or enter the setup key.') }}
                                    @endif
                                </p>
                            </div>
                            <div class="mt-4">
                                {!! $qrCode !!}
                            </div>
                            <div class="mt-4">
                                <p class="font-weight-bold text-secondary">
                                    {{ __('Setup Key') }}: {{ $setupKey }}
                                </p>
                            </div>
                        @endif
                        @if ($showingConfirmation)
                            <div class="mt-4">
                                <form id="confirm-2fa-form">
                                    @csrf
                                    <div class="form-group">
                                        <label for="code">{{ __('Code') }}</label>
                                        <input id="code" type="text" name="code" class="form-control w-50" inputmode="numeric" autofocus autocomplete="one-time-code" required>
                                        @if ($errors->has('code'))
                                            <span class="text-danger">{{ $errors->first('code') }}</span>
                                        @endif
                                    </div>
                                    <button type="submit" class="btn btn-primary">{{ __('Confirm') }}</button>
                                </form>
                            </div>
                        @endif
                        @if ($showingRecoveryCodes)
                            <div class="mt-4">
                                <p class="font-weight-bold text-secondary">
                                    {{ __('Store these recovery codes in a secure password manager. They can be used to recover access to your account if your two factor authentication device is lost.') }}
                                </p>
                            </div>
                            <div class="grid gap-1 max-w-xl mt-4 px-4 py-4 font-monospace text-secondary bg-light rounded-lg">
                                @foreach (json_decode(decrypt($recoveryCodes), true) as $code)
                                    <div>{{ $code }}</div>
                                @endforeach
                            </div>
                        @endif
                        <div class="mt-4">
                            <button id="disable-2fa" class="btn btn-danger">{{ __('Disable') }}</button>
                        </div>
                        <div class="mt-4">
                            <button id="regenerate-recovery-codes" class="btn btn-warning">{{ __('Regenerate Recovery Codes') }}</button>
                        </div>
                        <div class="mt-4">
                            <button id="get-recovery-codes" class="btn btn-info">{{ __('Get Recovery Codes') }}</button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@section('scripts')
@parent
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Enable 2FA
    document.getElementById('enable-2fa').addEventListener('click', function () {
        fetch('/api/two-factor-authentication', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.message) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            alert('Failed to enable two-factor authentication.');
        });
    });

    // Confirm 2FA
    document.getElementById('confirm-2fa-form').addEventListener('submit', function (event) {
        event.preventDefault();
        const code = document.getElementById('code').value;
        fetch('/api/two-factor-authentication/confirm', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ code: code })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.message) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            alert('The provided code was invalid.');
        });
    });

    // Disable 2FA
    document.getElementById('disable-2fa').addEventListener('click', function () {
        fetch('/api/two-factor-authentication', {
            method: 'DELETE',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.message) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            alert('Failed to disable two-factor authentication.');
        });
    });

    // Regenerate Recovery Codes
    document.getElementById('regenerate-recovery-codes').addEventListener('click', function () {
        fetch('/api/two-factor-authentication/recovery-codes', {
            method: 'POST',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token'),
                'Content-Type': 'application/json'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.message) {
                alert(data.message);
                location.reload();
            }
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            alert('Failed to regenerate recovery codes.');
        });
    });

    // Get Recovery Codes
    document.getElementById('get-recovery-codes').addEventListener('click', function () {
        fetch('/api/two-factor-authentication/recovery-codes', {
            method: 'GET',
            headers: {
                'Authorization': 'Bearer ' + localStorage.getItem('token')
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            if (data.recovery_codes) {
                alert('Recovery Codes: ' + data.recovery_codes.join(', '));
            }
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
            alert('Failed to get recovery codes.');
        });
    });
});
</script>
@endsection

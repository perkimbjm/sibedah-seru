<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Logout dari Browser dan Perangkat Lainnya</div>

                @if (!empty($sessions))
                    <div class="card-body">
                        <!-- Other Browser Sessions -->
                        @foreach ($sessions as $session)
                            <div class="mb-4 d-flex align-items-center">
                                <div class="mr-2">
                                    @if ($session->agent->isDesktop())
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 17.25v1.007a3 3 0 01-.879 2.122L7.5 21h9l-.621-.621A3 3 0 0115 18.257V17.25m6-12V15a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 15V5.25m18 0A2.25 2.25 0 0018.75 3H5.25A2.25 2.25 0 003 5.25m18 0V12a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 12V5.25" />
                                        </svg>
                                    @else
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8 text-gray-500">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" />
                                        </svg>
                                    @endif
                                </div>

                                <div>
                                    <div class="text-sm text-gray-600">
                                        {{ $session->agent->platform() ? $session->agent->platform() : __('Unknown') }} - {{ $session->agent->browser() ? $session->agent->browser() : __('Unknown') }}
                                    </div>

                                    <div class="text-xs text-gray-500">
                                        {{ $session->ip_address }},

                                        @if ($session->is_current_device)
                                            <span class="font-semibold text-green-500">{{ __('This device') }}</span>
                                        @else
                                            {{ __('Last active') }} {{ $session->last_active }}
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif


                <div class="card-body">
                    <div class="mb-0 form-group row">
                        <div class="col-md-6 offset-md-4">
                            <button
                                class="btn btn-success"
                                data-toggle="modal"
                                data-target="#logoutOtherBrowserSessionsModal"
                            >
                                {{ __('Log Out') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Out Other Devices Confirmation Modal -->
    <div class="modal fade" id="logoutOtherBrowserSessionsModal" tabindex="-1" role="dialog" aria-labelledby="logoutOtherBrowserSessionsModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="logoutOtherBrowserSessionsModalLabel">{{ __('Log Out Other Browser Sessions') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <form method="post" action="{{ route('other-browser-sessions.destroy') }}">
                        @csrf
                        @method('delete')

                        <p class="mb-4 text-sm text-gray-600">
                            {{ __('Please enter your password to confirm you would like to log out of your other browser sessions across all of your devices.') }}
                        </p>

                        <div class="mb-4">
                            <label for="password" class="sr-only">{{ __('Password') }}</label>
                            <input
                                id="password"
                                name="password"
                                type="password"
                                class="form-control"
                                placeholder="{{ __('Password') }}"
                            />
                            @error('password')
                                <div class="mt-2 text-sm text-danger">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="ml-3 btn btn-primary">
                                {{ __('Log Out Other Browser Sessions') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

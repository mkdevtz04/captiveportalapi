<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('MikroTik Management') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('status'))
                <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded">
                    {{ session('status') }}
                </div>
            @endif

            @if($error)
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                    {{ $error }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div class="lg:col-span-1 space-y-6">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm font-bold text-gray-900 mb-4">MikroTik Settings</div>
                        <form method="POST" action="{{ route('mikrotik.settings') }}">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">IP Address</label>
                                <input type="text" name="ip" value="{{ old('ip', $settings->ip ?? '') }}" class="w-full border rounded px-3 py-2 text-sm" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Username</label>
                                <input type="text" name="username" value="{{ old('username', $settings->username ?? '') }}" class="w-full border rounded px-3 py-2 text-sm" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password</label>
                                <input type="password" name="password" value="{{ old('password', $settings->password ?? '') }}" class="w-full border rounded px-3 py-2 text-sm" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Port</label>
                                <input type="number" name="port" value="{{ old('port', $settings->port ?? 8728) }}" class="w-full border rounded px-3 py-2 text-sm" required>
                            </div>
                            <button type="submit" class="w-full bg-gray-900 text-black text-sm font-bold py-2 rounded hover:bg-gray-800">Save Settings</button>
                        </form>
                        <button id="testConnectionBtn" class="w-full mt-3 border border-gray-300 text-gray-700 text-sm font-bold py-2 rounded hover:bg-gray-50">
                            Test Connection
                        </button>
                        <div id="testResult" class="mt-3 text-xs hidden"></div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm font-bold text-gray-900 mb-4">Create Voucher</div>
                        <form id="createVoucherForm">
                            @csrf
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Username / Voucher Code</label>
                                <input type="text" name="username" id="voucherUsername" class="w-full border rounded px-3 py-2 text-sm" required>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password</label>
                                <input type="text" name="password" id="voucherPassword" class="w-full border rounded px-3 py-2 text-sm" required>
                                <button type="button" id="generatePassword" class="text-xs text-gray-500 mt-1 hover:text-gray-900">Generate random</button>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Profile</label>
                                <select name="profile" id="voucherProfile" class="w-full border rounded px-3 py-2 text-sm" required>
                                    <option value="">Select profile</option>
                                    @foreach($profiles as $profile)
                                        <option value="{{ $profile['name'] ?? $profile['id'] ?? '' }}">
                                            {{ $profile['name'] ?? $profile['id'] ?? 'Unknown' }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <button type="submit" class="w-full bg-gray-900 text-black text-sm font-bold py-2 rounded hover:bg-gray-800">Create Voucher</button>
                        </form>
                        <div id="voucherResult" class="mt-3 text-xs hidden"></div>
                    </div>
                </div>

                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="text-sm font-bold text-gray-900 mb-4">Existing Vouchers</div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr class="border-b">
                                        <th class="text-left py-2 px-3 text-xs font-bold text-gray-500 uppercase">ID</th>
                                        <th class="text-left py-2 px-3 text-xs font-bold text-gray-500 uppercase">Username</th>
                                        <th class="text-left py-2 px-3 text-xs font-bold text-gray-500 uppercase">Profile</th>
                                        <th class="text-left py-2 px-3 text-xs font-bold text-gray-500 uppercase">Disabled</th>
                                        <th class="text-left py-2 px-3 text-xs font-bold text-gray-500 uppercase">Uptime</th>
                                        <th class="text-left py-2 px-3 text-xs font-bold text-gray-500 uppercase">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                        <tr class="border-b last:border-0">
                                            <td class="py-2 px-3 font-mono text-xs">{{ $user['.id'] ?? $user['id'] ?? '-' }}</td>
                                            <td class="py-2 px-3">{{ $user['name'] ?? '-' }}</td>
                                            <td class="py-2 px-3">{{ $user['profile'] ?? '-' }}</td>
                                            <td class="py-2 px-3">{{ $user['disabled'] ?? 'no' }}</td>
                                            <td class="py-2 px-3">{{ $user['uptime'] ?? '-' }}</td>
                                            <td class="py-2 px-3">
                                                <form method="POST" action="{{ route('mikrotik.voucher.destroy', $user['.id'] ?? $user['id']) }}" onsubmit="return confirm('Delete this voucher?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="text-red-600 hover:text-red-900 text-xs font-bold">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="py-6 text-center text-gray-500">No vouchers found or not connected.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.getElementById('generatePassword').addEventListener('click', function () {
        const chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';
        let out = '';
        for (let i = 0; i < 8; i++) out += chars[Math.floor(Math.random() * chars.length)];
        document.getElementById('voucherPassword').value = out;
    });

    document.getElementById('testConnectionBtn').addEventListener('click', function () {
        const btn = this;
        const result = document.getElementById('testResult');
        btn.disabled = true;
        btn.textContent = 'Testing...';
        result.classList.remove('hidden', 'text-red-600', 'text-green-600');

        fetch('{{ route('mikrotik.test') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: new URLSearchParams({
                ip: document.querySelector('input[name="ip"]').value,
                username: document.querySelector('input[name="username"]').value,
                password: document.querySelector('input[name="password"]').value,
                port: document.querySelector('input[name="port"]').value,
            })
        })
        .then(r => r.json())
        .then(data => {
            result.classList.remove('hidden');
            if (data.success) {
                result.classList.add('text-green-600');
                result.textContent = data.message + (data.profiles?.length ? ' Profiles: ' + data.profiles.map(p => p.name || p.id).join(', ') : '');
            } else {
                result.classList.add('text-red-600');
                result.textContent = data.message;
            }
        })
        .catch(() => {
            result.classList.remove('hidden');
            result.classList.add('text-red-600');
            result.textContent = 'Request failed.';
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = 'Test Connection';
        });
    });

    document.getElementById('createVoucherForm').addEventListener('submit', function (e) {
        e.preventDefault();
        const result = document.getElementById('voucherResult');
        result.classList.remove('hidden', 'text-red-600', 'text-green-600');

        const formData = new FormData(this);
        fetch('{{ route('mikrotik.voucher') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            result.classList.remove('hidden');
            if (data.success) {
                result.classList.add('text-green-600');
                result.textContent = data.message;
                setTimeout(() => location.reload(), 1000);
            } else {
                result.classList.add('text-red-600');
                result.textContent = data.message;
            }
        })
        .catch(() => {
            result.classList.remove('hidden');
            result.classList.add('text-red-600');
            result.textContent = 'Request failed.';
        });
    });
    </script>
</x-app-layout>

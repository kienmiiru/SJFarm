<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SJFarm - Pengaturan Akun</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="grid grid-cols-5 bg-ccream">
        @include('components.distributor-sidebar')
        <div class="col-span-full md:col-span-4 min-h-screen">
            <div class="border-1 justify-between bg-clgreen m-8 rounded-4xl p-8 pt-2">
                <div id="formResult" class="bg-caqua border-1 mx-auto w-full max-w-md rounded-xl text-center p-2 text-xl hidden">
                    Notifikasi sukses atau error
                </div>
                <form id="distributorForm" class="my-4 text-xl">
                    @php $d = $distributor; @endphp
                    @foreach (['name' => 'Nama', 'address' => 'Alamat', 'email' => 'Email', 'phone_number' => 'Nomor Telepon', 'username' => 'Username'] as $field => $label)
                    <div class="bg-white border-none rounded-4xl my-2 px-4 py-2">
                        <label>{{ $label }}:</label>
                        <input type="text" id="{{ $field }}" class="border-none p-2 focus:outline-none w-1/2" value="{{ $d->$field }}">
                    </div>
                    @endforeach
                    <div class="bg-white border-none rounded-4xl my-2 px-4 py-2">
                        <label>Password Baru:</label>
                        <input type="password" id="password" class="border-none p-2 focus:outline-none w-1/2" placeholder="Kosongi jika tidak diubah">
                    </div>
                    <div class="bg-white border-none rounded-4xl my-2 px-4 py-2">
                        <label>Konfirmasi Password Baru:</label>
                        <input type="password" id="passwordConfirmation" class="border-none p-2 focus:outline-none w-1/2">
                    </div>
                    <button id="submitButton" class="bg-botan3 px-2 py-1 min-w-24 rounded-4xl">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const formResult = document.querySelector('#formResult');
        const distributorForm = document.querySelector('#distributorForm');
        const fields = ['name', 'address', 'email', 'phone_number', 'username'];
        const password = document.querySelector('#password');
        const passwordConfirmation = document.querySelector('#passwordConfirmation');

        function showResult(message, isError = false) {
            formResult.classList.remove('hidden', 'bg-red-600', 'bg-caqua');
            formResult.classList.add(isError ? 'bg-red-600' : 'bg-caqua');
            formResult.innerText = message;
        }

        distributorForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const data = {};
            let hasError = false;

            fields.forEach(f => {
                const val = document.querySelector('#' + f).value.trim();
                if (val === '') {
                    showResult(`Kolom ${f.replace('_', ' ')} tidak boleh kosong.`, true);
                    hasError = true;
                } else {
                    data[f] = val;
                }
            });

            if (hasError) return;

            if (password.value !== '') {
                if (password.value.length < 8) {
                    showResult('Password minimal 8 karakter.', true);
                    return;
                }
                if (password.value !== passwordConfirmation.value) {
                    showResult('Konfirmasi password tidak cocok.', true);
                    return;
                }
                data.password = password.value;
                data.password_confirmation = passwordConfirmation.value;
            }

            try {
                const response = await fetch('/distributor/api/account', {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(data)
                });

                const result = await response.json();

                if (response.ok) {
                    showResult('Perubahan berhasil disimpan.');
                    password.value = '';
                    passwordConfirmation.value = '';
                } else {
                    if (result.data) {
                        const messages = Object.values(result.data).flat().join('\n');
                        showResult(messages, true);
                    } else if (result.message) {
                        showResult(result.message, true);
                    } else {
                        showResult('Terjadi kesalahan.', true);
                    }
                }
            } catch (error) {
                console.error(error);
                showResult('Gagal menghubungi server.', true);
            }
        });
    </script>
</body>

</html>
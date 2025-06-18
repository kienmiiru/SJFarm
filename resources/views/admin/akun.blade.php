<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pengaturan Akun - SJFarm</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="grid grid-cols-5 bg-ccream">
        @include('components.admin-sidebar')
        <div class="col-span-full md:col-span-4 min-h-screen">
            <div class="border-1 justify-between bg-clgreen m-8 rounded-4xl p-8 pt-2">
                <div id="formResult" class="bg-caqua border-1 mx-auto w-full max-w-md rounded-xl text-center p-2 text-md md:text-xl hidden">
                    tulis error atau sukses di sini
                </div>
                <form id="profileForm" class="my-4 text-md md:text-xl">
                    <div class="bg-white border-none rounded-4xl my-2 px-4 py-2">
                        <label>Username:</label>
                        <input type="text" id="username" class="w-full border-none p-2 focus:outline-none md:w-1/2" value="{{ $admin->username }}">
                    </div>
                    <div class="bg-white border-none rounded-4xl my-2 px-4 py-2">
                        <label>Password Baru:</label>
                        <input type="password" id="password" class="w-full border-none p-2 focus:outline-none md:w-1/2" placeholder="Kosongi jika tidak diubah">
                    </div>
                    <div class="bg-white border-none rounded-4xl my-2 px-4 py-2">
                        <label>Konfirmasi Password Baru:</label>
                        <input type="password" id="passwordConfirmation" class="w-full border-none p-2 focus:outline-none md:w-1/2">
                    </div>
                    <button id="submitButton" class="bg-botan3 px-2 py-1 min-w-24 rounded-4xl">Simpan</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const formResult = document.querySelector('#formResult');
        const profileForm = document.querySelector('#profileForm');
        const username = document.querySelector('#username');
        const password = document.querySelector('#password');
        const passwordConfirmation = document.querySelector('#passwordConfirmation');

        function showResult(message, red = false) {
            formResult.classList.remove('hidden', 'bg-red-600', 'bg-caqua');
            formResult.classList.add(red ? 'bg-red-600' : 'bg-caqua');
            formResult.innerText = message
        }

        profileForm.addEventListener('submit', async (e) => {
            e.preventDefault();

            const userVal = username.value.trim();
            const passVal = password.value;
            const passConfVal = passwordConfirmation.value;

            if (userVal === '') {
                showResult('Username tidak boleh kosong.', true);
                return;
            }

            if (passVal !== '') {
                if (passVal.length < 8) {
                    showResult('Password minimal harus 8 karakter.', true);
                    return;
                }
                if (passVal !== passConfVal) {
                    showResult('Konfirmasi password tidak cocok.', true);
                    return;
                }
            }

            const data = { username: userVal };
            if (passVal !== '') {
                data.password = passVal;
                data.password_confirmation = passConfVal;
            }

            try {
                const response = await fetch('/admin/api/account', {
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
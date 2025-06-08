<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SJFarm - Distributor</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="grid grid-cols-5 bg-ccream">
        @include('components.admin-sidebar')
        <div class="col-span-full md:col-span-4 min-h-screen p-8">
            <div id="successBox" class="bg-caqua mx-auto w-full max-w-md rounded-4xl text-center p-2 hidden text-xl">
                Data berhasil ditambahkan
            </div>
            <div id="confirmBox" class="bg-white mx-auto w-full max-w-md rounded-4xl text-center p-2 hidden">
                Hapus data distributor?
                <div class="flex space-x-2 justify-center">
                    <button id="btnConfirmOk" class="px-8 py-1 bg-red-600 rounded-4xl hover:bg-red-700 font-semibold">Konfirmasi</button>
                    <button id="btnConfirmCancel" class="px-8 py-1 bg-red-600 rounded-4xl hover:bg-red-700 font-semibold">Batal</button>
                </div>
            </div>
            <button id="btnAdd" class="mb-6 px-4 py-2 bg-white rounded-4xl hover:bg-gray-300">
                Tambah
            </button>

            <div id="distributorList" class="md:flex md:flex-wrap justify-center">Memuat...</div>
        </div>
    </div>

    <div id="formModal" class="fixed inset-0 bg-black/40 hidden flex flex-col items-center justify-center z-50">
        <div id="formError" class="bg-red-600 w-full max-w-md rounded-4xl text-center p-2 invisible text-xl">
            Isi data dengan benar
        </div>
        <div class="bg-lime-500/90 p-6 rounded-lg w-full max-w-md mt-2">
            <form id="distributorForm" class="space-y-4" novalidate>
                <input type="hidden" id="distributorId">
                <div>
                    <input id="distributorName" class="w-full p-2 border-2 bg-white" placeholder="Nama Distributor" required>
                </div>
                <div>
                    <input id="distributorUsername" class="w-full p-2 border-2 bg-white" placeholder="Username" required>
                </div>
                <div>
                    <input id="distributorPassword" type="password" class="w-full p-2 border-2 bg-white">
                </div>
                <div>
                    <input id="distributorEmail" type="email" class="w-full p-2 border-2 bg-white" placeholder="Email" required>
                </div>
                <div>
                    <input id="distributorPhonenum" type="tel" class="w-full p-2 border-2 bg-white" placeholder="Nomor Telepon" required>
                </div>
                <div>
                    <input id="distributorAddress" class="w-full p-2 border-2 bg-white" placeholder="Alamat" required>
                </div>
                {{-- <p id="formError" class="text-red-600 text-sm hidden">Isi data dengan benar.</p> --}}
                <div class="flex justify-start space-x-2 mt-4">
                    <button type="submit" class="px-8 py-2 rounded-4xl bg-white hover:bg-gray-300">Kirim</button>
                    <button type="button" id="btnCancel" class="px-8 py-2 rounded-4xl bg-white hover:bg-gray-300">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const distributorList = document.querySelector('#distributorList');
        const formModal = document.getElementById('formModal');
        const confirmBox = document.getElementById('confirmBox');
        const form = document.getElementById('distributorForm');
        const formError = document.getElementById('formError');
        const btnAdd = document.getElementById('btnAdd');
        const btnCancel = document.getElementById('btnCancel');
        const btnConfirmCancel = document.getElementById('btnConfirmCancel');
        const btnConfirmOk = document.getElementById('btnConfirmOk');
        const distributorIdInput = document.getElementById('distributorId');
        const successBox = document.getElementById('successBox');
        const distributorName = document.getElementById('distributorName');
        const distributorUsername = document.getElementById('distributorUsername');
        const distributorPassword = document.getElementById('distributorPassword');
        const distributorEmail = document.getElementById('distributorEmail');
        const distributorPhonenum = document.getElementById('distributorPhonenum');
        const distributorAddress = document.getElementById('distributorAddress');

        function showSuccess(message) {
            successBox.innerText = message;
            successBox.classList.remove('hidden');
            setTimeout(() => successBox.classList.add('hidden'), 2000);
        }

        function showForm(isEdit = false, data = {}) {
            distributorPassword.placeholder = isEdit ? 'Password (kosongi jika tdk diubah, minimal 8 karakter)' : 'Password (minimal 8 karakter)'
            distributorIdInput.value = data.id || '';
            distributorName.value = data.name || '';
            distributorUsername.value = data.username || '';
            distributorEmail.value = data.email || '';
            distributorPhonenum.value = data.phone_number || '';
            distributorAddress.value = data.address || '';
            formModal.classList.remove('hidden');
        }

        function hideForm() {
            formModal.classList.add('hidden');
            form.reset();
            formError.classList.add('invisible');
        }

        let currentDeleteId = null;

        function showConfirm(id) {
            currentDeleteId = id;
            confirmBox.classList.remove('hidden');
        }

        function hideConfirm() {
            currentDeleteId = null;
            confirmBox.classList.add('hidden');
        }

        function renderDistributors() {
            distributorList.innerHTML = '';
            fetch('/admin/api/distributors')
                .then(res => res.json())
                .then(json => {
                    json.data.forEach(item => {
                        const card = document.createElement('div');
                        card.className = 'bg-clgreen p-4 rounded-3xl border-2 md:w-2/7 m-4 flex flex-col';
                        card.innerHTML = `
                            <div class="font-semibold">${item.name}</div>
                            <div class="text-cdlime2">${item.address}</div>
                            <div class="font-semibold text-center my-8">Total Permintaan: ${item.request_count}</div>
                            <div class="mt-4 flex space-x-2 justify-between mt-auto">
                                <button class="editBtn px-3 py-1 bg-cdlime hover:bg-cdlime/80 rounded-4xl w-full" data-id="${item.id}">Edit</button>
                                <button class="deleteBtn px-3 py-1 bg-cdlime hover:bg-cdlime/80 rounded-4xl w-full" data-id="${item.id}">Hapus</button>
                            </div>`;
                        distributorList.appendChild(card);
                    });

                    document.querySelectorAll('.editBtn').forEach(btn => {
                        btn.addEventListener('click', e => {
                            const id = btn.dataset.id;
                            const data = json.data.find(item => item.id == id);
                            showForm(true, data);
                        });
                    });

                    document.querySelectorAll('.deleteBtn').forEach(btn => {
                        btn.addEventListener('click', () => showConfirm(btn.dataset.id));
                    });
                });
        }

        form.addEventListener('submit', e => {
            e.preventDefault();
            const id = distributorIdInput.value;
            const body = {
                name: distributorName.value,
                address: distributorAddress.value,
                email: distributorEmail.value,
                phone_number: distributorPhonenum.value,
                username: distributorUsername.value,
            };

            if (distributorPassword.value) {
                body.password = distributorPassword.value;
            }

            if (!distributorName.value || !distributorUsername.value || !distributorEmail.value || !distributorPhonenum.value || !distributorAddress.value) {
                formError.innerText = 'Data tidak boleh kosong';
                formError.classList.remove('invisible');
                return;
            }

            if (!form.checkValidity() || (distributorPassword.value && distributorPassword.value.length < 8)) {
                formError.innerText = 'Masukkan data dengan benar';
                formError.classList.remove('invisible');
                return;
            }

            const url = id ? `/admin/api/distributors/${id}` : '/admin/api/distributors';
            const method = id ? 'PUT' : 'POST';

            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(body)
            })
                .then(res => res.json())
                .then(json => {
                    if (json.status === 'error') {
                        if (json.data) {
                            const messages = Object.values(json.data).flat().join('\n');
                            formError.innerText = messages;
                        } else {
                            formError.innerText = json.message || 'Isi data dengan benar.';
                        }
                        formError.classList.remove('invisible');
                    } else {
                        showSuccess(id ? 'Data Berhasil Diperbaharui' : 'Data Berhasil Ditambahkan');
                        hideForm();
                        renderDistributors();
                    }
                });
        });

        btnAdd.addEventListener('click', () => showForm(false));

        btnCancel.addEventListener('click', hideForm);
        btnConfirmCancel.addEventListener('click', hideConfirm);

        btnConfirmOk.addEventListener('click', () => {
            fetch(`/admin/api/distributors/${currentDeleteId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(res => res.json())
                .then(() => {
                    hideConfirm();
                    showSuccess('Data Berhasil dihapus');
                    renderDistributors();
                });
        });

        renderDistributors();
    </script>
</body>

</html>
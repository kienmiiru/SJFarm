<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SJFarm - Panen</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="grid grid-cols-5 bg-ccream">
        @include('components.admin-sidebar')
        <div class="col-span-full md:col-span-4 min-h-screen p-8">
            <div id="form-success" class="bg-caqua mx-auto w-full max-w-md rounded-4xl text-center p-2 hidden text-xl">
                Data berhasil ditambahkan
            </div>
            <h1 class="text-2xl font-semibold text-center mb-4">Riwayat Hasil Data Panen</h1>
            <button id="btnAdd" class="mb-6 px-4 py-2 bg-cpink rounded hover:bg-cpink/80">
                Tambah Data Panen
            </button>

            <div id="harvestList" class="md:flex md:flex-wrap justify-between">
                <!-- Kartu panen akan dimuat di sini -->
            </div>
        </div>
    </div>

    <!-- Popup Tambah/Edit -->
    <div id="formModal" class="fixed inset-0 bg-black/40 hidden flex flex-col items-center justify-center z-50">
        <div id="formError" class="bg-red-600 w-full max-w-md rounded-4xl text-center p-2 hidden text-xl">
            Isi data dengan benar
        </div>
        <div class="bg-white p-6 rounded-lg w-full max-w-md mt-2">
            <h2 id="formTitle" class="text-lg font-semibold mb-4">Form Panen</h2>
            <form id="harvestForm" class="space-y-4">
                <input type="hidden" id="harvestId">
                <div>
                    <label for="fruitSelect" class="block font-medium">Buah</label>
                    <select id="fruitSelect" class="w-full p-2 border rounded"></select>
                </div>
                <div>
                    <label for="harvestDate" class="block font-medium">Tanggal Panen</label>
                    <input type="date" id="harvestDate" class="w-full p-2 border rounded">
                </div>
                <div>
                    <label for="amountKg" class="block font-medium">Jumlah (kg)</label>
                    <input type="number" step="0.01" id="amountKg" class="w-full p-2 border rounded">
                </div>
                {{-- <p id="formError" class="text-red-600 text-sm hidden">Isi data dengan benar.</p> --}}
                <div class="flex justify-start space-x-2 mt-4">
                    <button type="button" id="btnCancel" class="px-4 py-2 rounded-4xl bg-botan hover:bg-botan/80">Batal</button>
                    <button type="submit" class="px-4 py-2 rounded-4xl bg-botan hover:bg-botan/80">Kirim</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Popup Konfirmasi Hapus -->
    <div id="confirmModal" class="fixed inset-0 bg-black/40 hidden flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-sm">
            <p class="text-lg font-medium mb-4 text-center">Konfirmasi dihapus?</p>
            <div class="flex space-x-2 justify-center">
                <button id="btnConfirmOk" class="px-4 py-2 bg-red-600 rounded-4xl hover:bg-red-700">OK</button>
                <button id="btnConfirmCancel" class="px-4 py-2 bg-caqua rounded-4xl">Batal</button>
            </div>
        </div>
    </div>

    <!-- Notification -->
    {{-- <div id="notification" class="fixed bottom-6 right-6 bg-green-500 text-white px-4 py-2 rounded hidden z-50"></div> --}}
    <div id="notification" class="fixed top-1/2 left-1/2 -translate-1/2 bg-caqua mx-auto w-full max-w-md rounded-4xl text-center p-2 hidden text-xl"></div>

    <script>
        const harvestList = document.getElementById('harvestList');
        const formModal = document.getElementById('formModal');
        const confirmModal = document.getElementById('confirmModal');
        const formTitle = document.getElementById('formTitle');
        const form = document.getElementById('harvestForm');
        const fruitSelect = document.getElementById('fruitSelect');
        const harvestDate = document.getElementById('harvestDate');
        const amountKg = document.getElementById('amountKg');
        const formError = document.getElementById('formError');
        const btnAdd = document.getElementById('btnAdd');
        const btnCancel = document.getElementById('btnCancel');
        const btnConfirmCancel = document.getElementById('btnConfirmCancel');
        const btnConfirmOk = document.getElementById('btnConfirmOk');
        const harvestIdInput = document.getElementById('harvestId');
        const notification = document.getElementById('notification');

        let currentDeleteId = null;

        function showNotification(message) {
            notification.innerText = message;
            notification.classList.remove('hidden');
            setTimeout(() => notification.classList.add('hidden'), 2000);
        }

        function showForm(isEdit = false, data = {}) {
            formTitle.innerText = isEdit ? 'Edit Data Panen' : 'Tambah Data Panen';
            harvestIdInput.value = data.id || '';
            harvestDate.value = data.harvest_date || '';
            amountKg.value = data.amount_in_kg || '';
            fruitSelect.innerHTML = '';
            fetch('/admin/api/fruits')
                .then(res => res.json())
                .then(json => {
                    json.data.forEach(fruit => {
                        const opt = document.createElement('option');
                        opt.value = fruit.id;
                        opt.textContent = fruit.name;
                        if (fruit.name === data.fruit) opt.selected = true;
                        fruitSelect.appendChild(opt);
                    });
                    fruitSelect.disabled = isEdit;
                    formModal.classList.remove('hidden');
                });
        }

        function hideForm() {
            formModal.classList.add('hidden');
            form.reset();
            formError.classList.add('hidden');
        }

        function showConfirm(id) {
            currentDeleteId = id;
            confirmModal.classList.remove('hidden');
        }

        function hideConfirm() {
            currentDeleteId = null;
            confirmModal.classList.add('hidden');
        }

        function renderHarvests() {
            harvestList.innerHTML = '';
            fetch('/admin/api/harvests')
                .then(res => res.json())
                .then(json => {
                    json.data.forEach(item => {
                        const card = document.createElement('div');
                        card.className = 'bg-white p-4 rounded shadow md:w-5/11 m-1';
                        card.innerHTML = `
                            <div class="font-semibold">${item.harvest_date}</div>
                            <div>Buah: ${item.fruit}</div>
                            <div>Hasil Panen: ${item.amount_in_kg} kg</div>
                            <div class="mt-4 flex space-x-2">
                                <button class="editBtn px-3 py-1 bg-botan hover:bg-botan/80 rounded-4xl" data-id="${item.id}">Edit</button>
                                <button class="deleteBtn px-3 py-1 bg-botan hover:bg-botan/80 rounded-4xl" data-id="${item.id}">Hapus</button>
                            </div>`;
                        harvestList.appendChild(card);
                    });

                    document.querySelectorAll('.editBtn').forEach(btn => {
                        btn.addEventListener('click', e => {
                            const id = btn.dataset.id;
                            fetch(`/admin/api/harvests`)
                                .then(res => res.json())
                                .then(json => {
                                    const data = json.data.find(item => item.id == id);
                                    showForm(true, data);
                                });
                        });
                    });

                    document.querySelectorAll('.deleteBtn').forEach(btn => {
                        btn.addEventListener('click', () => showConfirm(btn.dataset.id));
                    });
                });
        }

        form.addEventListener('submit', e => {
            e.preventDefault();
            const id = harvestIdInput.value;
            const body = {
                fruit_id: fruitSelect.value,
                amount_in_kg: amountKg.value,
                harvest_date: harvestDate.value,
            };

            if (!body.fruit_id || !body.harvest_date || !body.amount_in_kg) {
                formError.innerText = 'Data harus diisi';
                formError.classList.remove('hidden');
                return;
            }

            const url = id ? `/admin/api/harvests/${id}` : '/admin/api/harvests';
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
                        formError.innerText = json.message || 'Isi data dengan benar.';
                        formError.classList.remove('hidden');
                    } else {
                        showNotification(id ? 'Data Berhasil Diedit' : 'Data Berhasil Ditambahkan');
                        hideForm();
                        renderHarvests();
                    }
                });
        });

        btnCancel.addEventListener('click', hideForm);
        btnConfirmCancel.addEventListener('click', hideConfirm);

        btnConfirmOk.addEventListener('click', () => {
            fetch(`/admin/api/harvests/${currentDeleteId}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(res => res.json())
                .then(() => {
                    hideConfirm();
                    showNotification('Data Berhasil Dihapus');
                    renderHarvests();
                });
        });

        btnAdd.addEventListener('click', () => showForm(false));

        // Load initial data
        renderHarvests();
    </script>
</body>

</html>
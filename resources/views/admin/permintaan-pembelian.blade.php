<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SJFarm - Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="grid grid-cols-5 bg-ccream">
        @include('components.admin-sidebar')
        <div class="col-span-4">
            <div id="successBox" class="bg-caqua mx-auto w-full max-w-md rounded-4xl text-center p-2 hidden text-xl">
                Data berhasil ditambahkan
            </div>
            <div id="requestList" class="flex flex-wrap justify-between bg-clgreen m-8 rounded-4xl p-8">
                <div class="bg-white p-4 rounded-4xl shadow w-5/11 m-1">
                    <p class="my-4 text-xl">PD Budiyanto Permadi (Persero) Tbk</p>
                    <hr>
                    <p class="my-4">Buah: Jeruk</p>
                    <hr>
                    <p class="my-4">Permintaan: 200 kg</p>
                    <hr>
                    <p class="my-4">Harga: Rp4.455.000</p>
                    <hr>
                    <p class="my-4">Tanggal Diajukan: Kamis, 22 Mei 2025 07.58.43 WIB</p>
                    <hr>
                    <p class="my-4">Status: Menunggu persetujuan</p>
                    <hr>
                    <div class="flex justify-around mt-4">
                        <button class="bg-botan2 px-2 py-1 rounded-4xl">Terima</button>
                        <button class="bg-botan2 px-2 py-1 rounded-4xl">Tolak</button>
                    </div>
                </div>
                <div class="bg-white p-4 rounded-4xl shadow w-5/11 m-1">
                    <p class="my-4 text-xl">PD Budiyanto Permadi (Persero) Tbk</p>
                    <hr>
                    <p class="my-4">Buah: Jeruk</p>
                    <hr>
                    <p class="my-4">Permintaan: 200 kg</p>
                    <hr>
                    <p class="my-4">Harga: Rp4.455.000</p>
                    <hr>
                    <p class="my-4">Tanggal Diajukan: Kamis, 22 Mei 2025 07.58.43 WIB</p>
                    <hr>
                    <p class="my-4">Status: Menunggu persetujuan</p>
                    <hr>
                    <div class="flex justify-around mt-4">
                        <button class="bg-botan2 px-2 py-1 rounded-4xl">Terima</button>
                        <button class="bg-botan2 px-2 py-1 rounded-4xl">Tolak</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="promptModal" class="fixed inset-0 bg-black/40 flex hidden flex-col items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md mt-2">
            <p>Yakin ingin menerima permintaan ini?</p>
            <input class="w-full m-2 p-2 border-2 bg-white" type="text" name="msg" id="msg" placeholder="Pesan untuk distributor... (opsional)">
            <div id="confirmButtons" class="flex space-x-2 justify-center">
                <button class="bg-botan2 px-2 py-1 rounded-4xl">Konfirmasi</button>
                <button class="bg-botan2 px-2 py-1 rounded-4xl">Batak</button>
            </div>
        </div>
    </div>

    <script>
        const requestList = document.getElementById('requestList');
        const successBox = document.getElementById('successBox');

        function prompt(message) {
            const promptModal = document.getElementById('promptModal');
            promptModal.classList.remove('hidden');
            const confirmButtons = document.getElementById('confirmButtons');
            const p = promptModal.querySelector('p');
            p.innerText = message;
            const msgInput = promptModal.querySelector('#msg');
            msgInput.value = '';

            confirmButtons.innerHTML = '';
            const button1 = document.createElement('button');
            const button2 = document.createElement('button');
            button1.className = 'bg-botan2 px-2 py-1 rounded-4xl';
            button1.innerText = 'Konfirmasi';
            button2.className = 'bg-botan2 px-2 py-1 rounded-4xl';
            button2.innerText = 'Batal';

            confirmButtons.append(button1, button2);

            return new Promise(res => {
                button1.addEventListener('click', () => {
                    promptModal.classList.add('hidden');
                    res([true, msgInput.value]);
                })
                button2.addEventListener('click', () => {
                    promptModal.classList.add('hidden');
                    res([false]);
                })
            });
        }

        function showSuccess(message) {
            successBox.innerText = message;
            successBox.classList.remove('hidden');
            setTimeout(() => successBox.classList.add('hidden'), 2000);
        }

        async function confirmRequest(id, what) {
            const url = '/admin/api/requests/' + id + '/' + what;
            const body = {};

            const [yesClicked, message] = await prompt('Yakin ingin ' + (what == 'approve' ? 'menerima' : 'menolak') + ' permintaan ini?');
            if (!yesClicked) return;
            if (message) {
                body.message = message;
            }

            fetch(url, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(body)
            })
                .then(res => res.json())
                .then(json => {
                    if (json.status === 'error') {
                        
                    } else {
                        showSuccess(json.message);
                        renderRequests();
                    }
                });
        }
        
        function renderRequests() {
            requestList.innerHTML = 'Memuat...';
            fetch('/admin/api/requests')
                .then(res => res.json())
                .then(json => {
                    requestList.innerHTML = '';
                    json.data.forEach(item => {
                        const requestedDate = new Date(item.requested_date);
                        const dateString = requestedDate.toLocaleDateString('id-ID', {
                          weekday: "long",
                          year: "numeric",
                          month: "long",
                          day: "2-digit",
                        });
                        const timeString = requestedDate.toLocaleTimeString('id-ID', { timeStyle: 'long' });

                        const card = document.createElement('div');
                        card.className = 'bg-white p-4 rounded-4xl shadow w-5/11 m-1';
                        card.innerHTML = `
                            <p class="my-4 text-xl">${item.distributor?.name || '-'}</p>
                            <hr>
                            <p class="my-4">Buah: ${item.fruit.name}</p>
                            <hr>
                            <p class="my-4">Permintaan: ${parseFloat(item.requested_stock_in_kg).toLocaleString('id-ID')} kg</p>
                            <hr>
                            <p class="my-4">Harga: Rp${item.total_price.toLocaleString('id-ID')}</p>
                            <hr>
                            <p class="my-4">Tanggal Diajukan: ${dateString} ${timeString}</p>
                            <hr>
                            <p class="my-4">Status: -</p>
                            <hr>`;
                        const statusP = card.children.item(10);
                        switch (item.status?.status) {
                            case 'pending': {
                                statusP.innerText = 'Status: Menunggu persetujuan';
                                break;
                            }
                            case 'approved': {
                                statusP.innerText = `Status: Disetujui (${item.status_changed_date})`;
                                if (item.status_changed_message) {
                                    statusP.innerText += `\nCatatan: ${item.status_changed_message}`;
                                }
                                break;
                            }
                            case 'rejected': {
                                statusP.innerText = `Status: Ditolak (${item.status_changed_date})`;
                                if (item.status_changed_message) {
                                    statusP.innerText += `\nCatatan: ${item.status_changed_message}`;
                                }
                                break;
                            }
                        }

                        if (item.status?.status === 'pending') {
                            const divButton = document.createElement('div');
                            divButton.className = 'flex justify-around mt-4';
                            divButton.innerHTML = `
                                <button class="bg-botan2 px-2 py-1 rounded-4xl" onclick="confirmRequest(${item.id}, 'approve')">Terima</button>
                                <button class="bg-botan2 px-2 py-1 rounded-4xl" onclick="confirmRequest(${item.id}, 'reject')">Tolak</button>`;
                            card.append(divButton);
                        }

                        requestList.appendChild(card);
                    });
                });
        }

        renderRequests();
    </script>
</body>

</html>
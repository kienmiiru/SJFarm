<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Permintaan dan Pembelian - SJFarm</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="grid grid-cols-5 bg-ccream">
        @include('components.admin-sidebar')
        <div class="col-span-full md:col-span-4 min-h-screen mb-2">
            <div id="successBox" class="bg-caqua mx-auto w-full max-w-md rounded-4xl text-center mt-2 p-2 hidden text-xl">
                Data berhasil ditambahkan
            </div>
            <div class="flex flex-col md:flex-row justify-between m-4 md:m-8 space-y-2 md:space-y-0">
                <div class="flex flex-col md:flex-row space-y-2 md:space-y-0 md:space-x-4">
                    <input id="filterDistributor" class="p-2 border-2 rounded w-full md:w-auto" type="text" placeholder="Nama Distributor">
                    <select id="filterStatus" class="p-2 border-2 rounded w-full md:w-auto">
                        <option value="">Semua Status</option>
                        <option value="pending">Menunggu persetujuan</option>
                        <option value="approved">Disetujui</option>
                        <option value="rejected">Ditolak</option>
                    </select>
                    <button id="applyFilters" class="bg-botan2 hover:bg-botan2/80 px-4 py-2 rounded-4xl w-full md:w-auto">Terapkan</button>
                </div>
                <button id="importButton" class="bg-botan2 hover:bg-botan2/80 px-4 py-2 rounded-4xl w-full md:w-auto"><i class="fa fa-upload"></i> Import Data</button>
            </div>
            <div id="requestList" class="md:flex md:flex-wrap justify-between bg-clgreen m-8 rounded-4xl p-8"></div>
            <div id="paginationControls" class="flex flex-col md:flex-row justify-center items-center mt-4 space-y-2 md:space-y-0 md:space-x-4">
                <button id="prevPage" class="bg-botan2 hover:bg-botan2/80 px-2 py-1 rounded-4xl w-full md:w-auto" disabled>Sebelumnya</button>
                <span id="paginationInfo" class="text-lg w-full md:w-auto text-center"></span>
                <div class="flex items-center w-full md:w-auto justify-center space-x-2">
                    <input id="pageInput" type="number" class="w-16 p-1 border-2 rounded text-center" min="1" value="1">
                    <span id="totalPages" class="text-lg"></span>
                </div>
                <button id="nextPage" class="bg-botan2 hover:bg-botan2/80 px-2 py-1 rounded-4xl w-full md:w-auto" disabled>Berikutnya</button>
            </div>
        </div>
    </div>

    <div id="promptModal" class="fixed inset-0 bg-black/40 flex hidden flex-col items-center justify-center z-50">
        <div id="formError" class="bg-red-600 w-full max-w-md rounded-4xl text-center p-2 hidden text-xl"></div>
        <div class="bg-white p-6 rounded-lg w-full max-w-md mt-2">
            <p>Yakin ingin menerima permintaan ini?</p>
            <input class="w-full m-2 p-2 border-2 bg-white" type="text" name="msg" id="msg" placeholder="Pesan untuk distributor...">
            <div id="confirmButtons" class="flex space-x-2 justify-center">
                <button class="bg-botan2 hover:bg-botan2/80 px-2 py-1 rounded-4xl">Konfirmasi</button>
                <button class="bg-botan2 hover:bg-botan2/80 px-2 py-1 rounded-4xl">Batal</button>
            </div>
        </div>
    </div>

    <div id="importModal" class="fixed inset-0 bg-black/40 flex hidden flex-col items-center justify-center z-50">
        <div class="bg-white p-6 rounded-lg w-full max-w-md mt-2">
            <p class="text-lg font-bold">Instruksi Import Data</p>
            <p class="mt-2">Silakan pilih file Excel (.xlsx atau .xls) yang ingin diunggah untuk mengimport data permintaan. Sebelum itu, pastikan beberapa hal berikut:</p>
            <ol class="list-decimal ml-6 mt-2">
                <li>Pastikan data yang akan diimport sudah sesuai dengan format yang telah ditentukan. Format tersebut bisa didownload <a class="text-blue-500" href="/storage/templates/template_import_penjualan.xlsx" target="_blank">di sini</a>.</li>
                <li>Pastikan setiap kolom diisi dengan benar sesuai dengan jenis data yang diminta. Misalnya, kolom "Buah" dan "Distributor" harus berisi nama buah dan distributor yang valid, dan kolom "Permintaan" harus berisi angka yang menunjukkan jumlah permintaan dalam kilogram.</li>
                <li>Jika ada kesalahan dalam format atau data yang diisi, sistem akan memberikan pesan kesalahan yang menjelaskan baris mana yang bermasalah.</li>
            </ol>
            <input id="importFile" type="file" class="hidden" accept=".xlsx,.xls">
            <div id="errorBox" class="text-red-500 mt-2 hidden overflow-y-scroll max-h-32"></div>
            <div class="flex justify-center mt-4">
                <button id="uploadButton" class="bg-botan2 hover:bg-botan2/80 px-4 py-2 rounded-4xl">Upload</button>
                <button id="closeImportModal" class="bg-botan2 hover:bg-botan2/80 px-4 py-2 rounded-4xl ml-2">Batal</button>
            </div>
        </div>
    </div>

    <script>
        const requestList = document.getElementById('requestList');
        const successBox = document.getElementById('successBox');
        const formError = document.getElementById('formError');
        
        function hideFormError() {
            formError.classList.add('hidden');
            formError.innerText = '';
        }

        function showFormError(message) {
            formError.classList.remove('hidden');
            formError.innerText = message;
        }

        function hidePrompt() {
            const promptModal = document.getElementById('promptModal');
            promptModal.classList.add('hidden');
        }

        function prompt(message, hideAfter = true) {
            hideFormError();
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
            button1.className = 'bg-botan2 hover:bg-botan2/80 px-2 py-1 rounded-4xl';
            button1.innerText = 'Konfirmasi';
            button2.className = 'bg-botan2 hover:bg-botan2/80 px-2 py-1 rounded-4xl';
            button2.innerText = 'Batal';

            confirmButtons.append(button1, button2);

            return new Promise(res => {
                button1.addEventListener('click', () => {
                    if (hideAfter) {
                        hidePrompt();
                    }
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

            const [yesClicked, message] = await prompt('Yakin ingin ' + (what == 'approve' ? 'menerima' : 'menolak') + ' permintaan ini?', false);
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
                        showFormError(json.message);
                    } else {
                        showSuccess(json.message);
                        renderRequests();
                        hidePrompt();
                    }
                });
        }
        
        let currentPage = 1;
        let totalPages = 1;

        function renderRequests() {
            requestList.innerHTML = 'Memuat...';
            const distributorName = document.getElementById('filterDistributor').value;
            const status = document.getElementById('filterStatus').value;

            const params = new URLSearchParams({
                page: currentPage,
                distributor_name: distributorName,
                status: status
            });

            fetch('/admin/api/requests?' + params.toString())
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
                        card.className = 'bg-white p-4 rounded-4xl shadow md:w-5/11 m-1';
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
                                const approvedDate = new Date(item.status_changed_date);
                                const approvedDateString = approvedDate.toLocaleDateString('id-ID', {
                                    weekday: "long",
                                    year: "numeric",
                                    month: "long",
                                    day: "2-digit",
                                });
                                const approvedTimeString = approvedDate.toLocaleTimeString('id-ID', { timeStyle: 'long' });
                                statusP.innerText = `Status: Disetujui (${approvedDateString} ${approvedTimeString})`;
                                if (item.status_changed_message) {
                                    statusP.innerText += `\nCatatan: ${item.status_changed_message}`;
                                }
                                break;
                            }
                            case 'rejected': {
                                const rejectedDate = new Date(item.status_changed_date);
                                const rejectedDateString = rejectedDate.toLocaleDateString('id-ID', {
                                    weekday: "long",
                                    year: "numeric",
                                    month: "long",
                                    day: "2-digit",
                                });
                                const rejectedTimeString = rejectedDate.toLocaleTimeString('id-ID', { timeStyle: 'long' });
                                statusP.innerText = `Status: Ditolak (${rejectedDateString} ${rejectedTimeString})`;
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
                                <button class="bg-botan2 hover:bg-botan2/80 px-2 py-1 rounded-4xl" onclick="confirmRequest(${item.id}, 'approve')">Terima</button>
                                <button class="bg-botan2 hover:bg-botan2/80 px-2 py-1 rounded-4xl" onclick="confirmRequest(${item.id}, 'reject')">Tolak</button>`;
                            card.append(divButton);
                        }

                        requestList.appendChild(card);
                    });

                    updatePagination(json.pagination);
                });
        }

        function updatePagination(pagination) {
            currentPage = pagination.current_page;
            totalPages = pagination.last_page;

            const paginationInfo = document.getElementById('paginationInfo');
            const pageInput = document.getElementById('pageInput');
            const totalPagesSpan = document.getElementById('totalPages');
            const prevPageButton = document.getElementById('prevPage');
            const nextPageButton = document.getElementById('nextPage');

            paginationInfo.innerText = `${(pagination.current_page - 1) * pagination.per_page + 1}-${Math.min(pagination.current_page * pagination.per_page, pagination.total)} dari ${pagination.total}`;
            pageInput.value = pagination.current_page;
            totalPagesSpan.innerText = `dari ${pagination.last_page}`;
            prevPageButton.disabled = pagination.current_page === 1;
            nextPageButton.disabled = pagination.current_page === pagination.last_page;
        }

        document.getElementById('prevPage').addEventListener('click', () => {
            if (currentPage > 1) {
                currentPage--;
                renderRequests();
            }
        });

        document.getElementById('nextPage').addEventListener('click', () => {
            if (currentPage < totalPages) {
                currentPage++;
                renderRequests();
            }
        });

        document.getElementById('pageInput').addEventListener('change', (e) => {
            const page = parseInt(e.target.value);
            if (page >= 1 && page <= totalPages) {
                currentPage = page;
                renderRequests();
            } else {
                e.target.value = currentPage;
            }
        });

        const importButton = document.getElementById('importButton');
        const importFile = document.getElementById('importFile');
        const importModal = document.getElementById('importModal');
        const uploadButton = document.getElementById('uploadButton');
        const closeImportModal = document.getElementById('closeImportModal');
        const errorBox = document.getElementById('errorBox');

        importButton.addEventListener('click', () => {
            importModal.classList.remove('hidden');
            errorBox.classList.add('hidden');
            errorBox.innerText = '';
        });

        closeImportModal.addEventListener('click', () => {
            importModal.classList.add('hidden');
        });

        uploadButton.addEventListener('click', async () => {
            importFile.click();
        });

        importFile.addEventListener('change', async () => {
            const file = importFile.files[0];
            if (!file) {
                return;
            }

            const formData = new FormData();
            formData.append('file', file);

            try {
                const validateResponse = await fetch('/admin/api/requests/validate-xlsx', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                const validateResult = await validateResponse.json();

                if (validateResult.status === 'error') {
                    errorBox.innerText = validateResult.errors.map(e => `Row ${e.row}: ${e.error}`).join('\n');
                    errorBox.classList.remove('hidden');
                    importFile.value = '';
                    return;
                }

                const importResponse = await fetch('/admin/api/requests/import-xlsx', {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: formData
                });
                const importResult = await importResponse.json();

                if (importResult.status === 'error') {
                    errorBox.innerText = importResult.message;
                    errorBox.classList.remove('hidden');
                    return;
                }

                showSuccess(importResult.message);
                renderRequests();
                importModal.classList.add('hidden');
            } catch (error) {
                errorBox.innerText = 'Terjadi kesalahan saat mengimport data.';
                errorBox.classList.remove('hidden');
            }
        });

        document.getElementById('applyFilters').addEventListener('click', () => {
            currentPage = 1;
            renderRequests();
        });

        renderRequests();
    </script>
</body>

</html>
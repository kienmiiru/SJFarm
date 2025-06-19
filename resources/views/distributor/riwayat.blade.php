<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pemesanan - SJFarm</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="grid grid-cols-5 bg-ccream">
        @include('components.distributor-sidebar')
        <div class="col-span-full md:col-span-4 min-h-screen p-8">
            <h1 class="text-2xl font-semibold text-center mb-4">Riwayat Permintaan</h1>

            <div class="flex mb-4">
                <select id="filterStatus" class="p-2 border-2 rounded">
                    <option value="">Semua Status</option>
                    <option value="pending">Menunggu persetujuan</option>
                    <option value="approved">Disetujui</option>
                    <option value="rejected">Ditolak</option>
                </select>
                <button id="applyFilters" class="bg-botan2 hover:bg-botan2/80 px-4 py-2 mx-2 rounded-4xl">Terapkan</button>
            </div>
            <div id="requestList" class="md:flex md:flex-wrap justify-between">
            </div>
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

    <script>
        const requestList = document.getElementById('requestList');

        let currentPage = 1;
        let totalPages = 1;

        function renderRequests() {
            requestList.innerHTML = 'Memuat...';
            const status = document.getElementById('filterStatus').value;

            const params = new URLSearchParams({
                page: currentPage,
                status: status
            });

            fetch('/distributor/api/requests?' + params.toString())
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
                        card.className = 'bg-white p-4 rounded shadow md:w-5/11 m-1';
                        card.innerHTML = `
                            <p>Buah:</p>
                            <p class="border-b-1 my-4">Buah ${item.fruit.name}</p>
                            <p>Jumlah Permintaan:</p>
                            <p class="border-b-1 my-4">${parseFloat(item.requested_stock_in_kg).toLocaleString('id-ID')} kg</p>
                            <p>Total Harga:</p>
                            <p class="border-b-1 my-4">Rp${item.total_price.toLocaleString('id-ID')}</p>
                            <p>Tanggal Diajukan:</p>
                            <p class="border-b-1 my-4">${dateString} ${timeString}</p>
                            <p>Status:</p>
                            <p class="border-b-1 my-4"></p>`;
                        const statusP = card.lastElementChild;
                        switch (item.status.status) {
                            case 'pending': {
                                statusP.innerText = 'Menunggu persetujuan';
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
                                statusP.innerText = `Disetujui (${approvedDateString} ${approvedTimeString})`;
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
                                statusP.innerText = `Ditolak (${rejectedDateString} ${rejectedTimeString})`;
                                if (item.status_changed_message) {
                                    statusP.innerText += `\nCatatan: ${item.status_changed_message}`;
                                }
                                break;
                            }
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

        document.getElementById('applyFilters').addEventListener('click', () => {
            currentPage = 1;
            renderRequests();
        });

        renderRequests();
    </script>
</body>

</html>
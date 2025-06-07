<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SJFarm - Riwayat Pemesanan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="grid grid-cols-5 bg-ccream">
        @include('components.distributor-sidebar')
        <div class="col-span-full md:col-span-4 min-h-screen p-8">
            <h1 class="text-2xl font-semibold text-center mb-4">Riwayat Permintaan</h1>

            <div id="requestList" class="md:flex md:flex-wrap justify-between">
            </div>
        </div>
    </div>

    <script>
        const requestList = document.getElementById('requestList');

        function renderRequests() {
            requestList.innerHTML = 'Memuat...';
            fetch('/distributor/api/requests')
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
                                statusP.innerText = `Diterima (${item.status_changed_date})`;
                                if (item.status_changed_message) {
                                    statusP.innerText += `\nCatatan: ${item.status_changed_message}`;
                                }
                                break;
                            }
                            case 'rejected': {
                                statusP.innerText = `Ditolak (${item.status_changed_date})`;
                                if (item.status_changed_message) {
                                    statusP.innerText += `\nCatatan: ${item.status_changed_message}`;
                                }
                                break;
                            }
                        }
                        requestList.appendChild(card);
                    });
                });
        }

        renderRequests();
    </script>
</body>

</html>
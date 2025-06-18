<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Pemesanan Buah - SJFarm</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="grid grid-cols-5 bg-ccream">
        @include('components.distributor-sidebar')
        <div class="col-span-full md:col-span-4 min-h-screen p-6">
            <div id="form-result" class="mx-auto w-full max-w-md rounded-4xl text-center p-2 text-xl hidden">
                {{-- Permintaan Buah Berhasil Diajukan --}}
                Permintaan Gagal Diajukan<br>
                Isi data dengan benar!
            </div>

            <div class="m-4 bg-white p-0 rounded-2xl shadow">
                <div class="bg-ccgreen p-4 rounded-t-2xl font-medium text-2xl">Permintaan</div>
                <form novalidate id="requestForm" action="/tk" class="m-4">
                    <div>
                        <label for="fruitSelect" class="block font-medium">Buah</label>
                        <select id="fruitSelect" class="w-full p-2 border rounded">
                            <option value="">Memuat...</option>
                        </select>
                    </div>
                    <div>
                        <label for="requestedStockKg" class="block font-medium">Jumlah Permintaan (kg)</label>
                        <input type="number" id="requestedStockKg" min="1" value="1" class="w-full p-2 border rounded">
                    </div>
                    <p id="totalPrice">Harga: </p>
                    <button class="px-4 py-2 my-2 rounded-4xl bg-botan hover:bg-botan/80">Ajukan</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        const formResult = document.getElementById('form-result');
        const requestForm = document.getElementById('requestForm');
        const fruitSelect = document.getElementById('fruitSelect');
        const requestedStockKg = document.getElementById('requestedStockKg');
        const totalPrice = document.getElementById('totalPrice');

        function showResult(message, red = false) {
            formResult.classList.remove('hidden', 'bg-red-600', 'bg-caqua');
            formResult.classList.add(red ? 'bg-red-600' : 'bg-caqua');
            formResult.innerText = message
        }

        function loadFruits() {
            fetch('/distributor/api/fruits')
                .then(res => res.json())
                .then(json => {
                    fruitSelect.innerHTML = ''

                    json.data.forEach(fruit => {
                        const opt = document.createElement('option');
                        opt.value = fruit.id;
                        opt.textContent = fruit.name + ` (stok: ${parseFloat(fruit.stock_in_kg).toLocaleString('id-ID')} kg)`;
                        opt.setAttribute('stock-in-kg', fruit.stock_in_kg);
                        opt.setAttribute('price-per-kg', fruit.price_per_kg);
                        fruitSelect.appendChild(opt);
                    });

                    const stockInKg = parseFloat(json.data[0].stock_in_kg);
                    requestedStockKg.max = stockInKg;
                    calculatePrice();
                });
        }

        loadFruits()

        function calculatePrice() {
            const pricePerKg = parseInt(fruitSelect.selectedOptions[0].getAttribute('price-per-kg'));
            const requestedStockInKg = parseInt(requestedStockKg.value);

            const price = pricePerKg * requestedStockInKg;
            totalPrice.innerText = `Harga: Rp${pricePerKg.toLocaleString('id-ID')} x ${requestedStockInKg.toLocaleString('id-ID')} = Rp${price.toLocaleString('id-ID')}`
            return price;
        }

        fruitSelect.addEventListener('change', e => {
            const stockInKg = parseInt(fruitSelect.selectedOptions[0].getAttribute('stock-in-kg'));
            requestedStockKg.max = stockInKg;
        })

        requestedStockKg.addEventListener('change', e => {
            requestedStockKg.value = parseInt(requestedStockKg.value);
            calculatePrice();
        })

        requestForm.addEventListener('submit', e => {
            e.preventDefault();
            const body = {
                fruit_id: fruitSelect.value,
                requested_stock_in_kg: parseInt(requestedStockKg.value),
            };

            if (!body.fruit_id || !body.requested_stock_in_kg) {
                showResult('Permintaan Gagal Diajukan\ndata harus diisi!', true);
                return;
            }

            if (body.requested_stock_in_kg < 1 || body.requested_stock_in_kg > parseInt(requestedStockKg.max)) {
                showResult('Permintaan Gagal Diajukan\nIsi data dengan benar!', true);
                return;
            }
            
            const url = '/distributor/api/requests';
            const method = 'POST';

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
                        showResult(`Permintaan Gagal Diajukan\n${json.message || 'Isi data dengan benar!'}`, true);
                    } else {
                        showResult('Permintaan Buah Berhasil Diajukan');
                        requestForm.reset();
                    }
                });
        })
    </script>
</body>

</html>
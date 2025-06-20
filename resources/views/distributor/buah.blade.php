<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Buah - SJFarm</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="grid grid-cols-5 bg-ccream">
        @include('components.distributor-sidebar')
        <div id="fruit-list" class="col-span-full md:col-span-4 min-h-screen">
            Memuat...
        </div>
    </div>

    <script>
        async function fetchFruits() {
            const res = await fetch('/distributor/api/fruits');
            const data = await res.json();
            const container = document.getElementById('fruit-list');
            container.innerHTML = '';
            data.data.forEach(fruit => {
                const row = document.createElement('div');
                row.className = 'm-4 bg-white p-0 pb-4 rounded-2xl shadow';
                row.innerHTML = `
                    <div class="bg-ccgreen p-4 rounded-t-2xl font-medium text-2xl">Stok Buah</div>
                    <p class="m-4">Buah:</p>
                    <p class="border-b-1 m-4 w-1/2">Buah ${fruit.name}</p>
                    <p class="m-4">Jumlah Stok:</p>
                    <p class="border-b-1 m-4 w-1/2">${parseFloat(fruit.stock_in_kg).toLocaleString('id-ID')} kg</p>
                    <p class="m-4">Harga:</p>
                    <p class="border-b-1 m-4 w-1/2">Rp${fruit.price_per_kg.toLocaleString('id-ID')}/kg</p>
                `;
                container.appendChild(row);
            });
        }

        document.addEventListener('DOMContentLoaded', fetchFruits);
    </script>
</body>

</html>
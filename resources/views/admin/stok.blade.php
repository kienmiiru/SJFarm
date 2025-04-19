<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SJFarm - Buah & Stok</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="grid grid-cols-5 bg-ccream">
        @include('components.admin-sidebar')
        <div class="col-span-4 p-6">
            <div id="form-success" class="bg-caqua mx-auto w-full max-w-md rounded-4xl text-center p-2 hidden text-xl">
                Data berhasil ditambahkan
            </div>
            <h1 class="text-2xl font-bold mb-4">Stok Buah Saat Ini</h1>

            <div id="fruit-list" class="space-y-4 bg-white p-4 rounded shadow">
                Memuat...
                <!-- Daftar buah akan dimuat di sini -->
            </div>

            <button id="add-fruit-btn" class="mt-4 px-4 py-2 bg-botan rounded-4xl hover:bg-botan/80">
                Tambah Buah
            </button>
        </div>
    </div>

    <!-- Modal -->
    <div id="fruit-modal" class="flex flex-col fixed inset-0 bg-black/50 hidden justify-center items-center">
        <div id="form-error" class="bg-red-600 w-full max-w-md rounded-4xl text-center p-2 hidden text-xl">
            Isi data dengan benar
        </div>
        <div class="bg-white p-6 rounded shadow w-full max-w-md mt-2">
            <h2 id="modal-title" class="text-xl font-semibold mb-4">Edit Buah</h2>
            <form id="fruit-form" class="space-y-4">
                <div>
                    <label class="block font-medium">Nama Buah</label>
                    <input type="text" id="fruit-name" class="w-full border p-2 rounded">
                </div>
                <div>
                    <label class="block font-medium">Stok (kg)</label>
                    <input type="number" id="fruit-stock" class="w-full border p-2 rounded" min="0">
                </div>
                <div>
                    <label class="block font-medium">Harga per kg</label>
                    <input type="number" id="fruit-price" class="w-full border p-2 rounded" min="0">
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" id="cancel-btn" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                        Kirim
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let editMode = false;
        let editId = null;

        async function fetchFruits() {
            const res = await fetch('/admin/api/fruits');
            const data = await res.json();
            const container = document.getElementById('fruit-list');
            container.innerHTML = '';
            data.data.forEach(fruit => {
                const row = document.createElement('div');
                row.className = 'flex justify-between items-center border-b py-2';
                row.innerHTML = `
                    <span>Buah ${fruit.name}: ${fruit.stock_in_kg} kg</span>
                    <button class="px-3 py-1 bg-botan rounded-4xl hover:bg-botan/80" onclick="openModal(${fruit.id}, '${fruit.name}', ${fruit.stock_in_kg}, ${fruit.price_per_kg})">Edit</button>
                `;
                container.appendChild(row);
            });
        }

        function openModal(id = null, name = '', stock = '', price = '') {
            editMode = id !== null;
            editId = id;
            document.getElementById('fruit-name').value = name;
            document.getElementById('fruit-stock').value = stock;
            document.getElementById('fruit-price').value = price;
            document.getElementById('modal-title').textContent = editMode ? 'Edit Buah' : 'Tambah Buah';
            document.getElementById('form-error').classList.add('hidden');
            document.getElementById('fruit-modal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('fruit-modal').classList.add('hidden');
            document.getElementById('form-success').classList.remove('hidden');
        }

        document.getElementById('add-fruit-btn').addEventListener('click', () => openModal());
        document.getElementById('cancel-btn').addEventListener('click', closeModal);

        document.getElementById('fruit-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const name = document.getElementById('fruit-name').value.trim();
            const stock = document.getElementById('fruit-stock').value;
            const price = document.getElementById('fruit-price').value;

            if (!name || stock === '' || price === '') {
                document.getElementById('form-error').textContent = 'Data harus diisi';
                document.getElementById('form-error').classList.remove('hidden');
                return;
            }

            if (isNaN(stock) || isNaN(price) || stock < 0 || price < 0) {
                document.getElementById('form-error').textContent = 'Isi data dengan benar';
                document.getElementById('form-error').classList.remove('hidden');
                return;
            }

            const payload = {
                name,
                stock_in_kg: parseFloat(stock),
                price_per_kg: parseInt(price)
            };

            const url = editMode ? `/admin/api/fruits/${editId}` : '/admin/api/fruits';
            const method = editMode ? 'PUT' : 'POST';

            await fetch(url, {
                method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify(payload)
            });

            closeModal();
            fetchFruits();
        });

        document.addEventListener('DOMContentLoaded', fetchFruits);
    </script>
</body>

</html>
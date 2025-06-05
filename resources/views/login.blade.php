<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SJFarm - Login</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <div class="bg-ccream h-screen">
        <div class="mx-auto w-fit text-center p-8">
            <div id="message" @class([ 'bg-red-500', 'w-full', 'rounded-4xl', 'text-center', 'p-2', 'text-xl', 'invisible' => !($message ?? null)])>
                {{ $message ?? '' }}
            </div>
            <h1 class="text-3xl font-bold">SJFarm</h1>
            <p class="text-lg font-bold text-gray-400">Manage Kebunmu Dengan Mudah Dan Effortsly</p>
            <form novalidate id="login" class="mt-16" action="/" method="post">
                @csrf
                <input class="bg-white p-1 m-1 rounded-sm border-1 border-gray-400 w-full" type="text" name="login" placeholder="Username/Email" required><br>
                <input class="bg-white p-1 m-1 rounded-sm border-1 border-gray-400 w-full" type="password" name="password" placeholder="Password" required><br>
                <button class="mt-8 rounded-sm bg-black text-white w-1/2 py-2" type="submit">Login</button>
            </form>
        </div>
    </div>
</body>

<script>
    const message = document.getElementById('message');
    const form = document.getElementById('login');
    
    form.addEventListener('submit', e => {
        if (!form.checkValidity()) {
            e.preventDefault();
            message.classList.remove('invisible')
            message.innerText = 'Data Wajib Diisi!';
        }
    });
</script>

</html>
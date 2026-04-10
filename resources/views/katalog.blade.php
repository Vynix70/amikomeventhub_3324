<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Halaman Katalog</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-slate-100 min-h-screen flex flex-col items-center justify-center p-4 gap-6">

    <div class="bg-white p-8 rounded-xl shadow-lg border border-slate-200 text-center max-w-sm w-full">
        <h1 class="text-2xl font-bold text-slate-800 mb-2">Katalog Event</h1>
        <p class="text-slate-500 mb-6">
            Daftar event yang akan datang akan ditampilkan di sini.
        </p>

        <a href="/" 
           class="inline-block bg-indigo-600 text-white font-semibold py-2 px-6 rounded-lg hover:bg-indigo-700 hover:shadow-md transition duration-300">
            Kembali ke Home
        </a>
    </div>

   
    <div class="bg-white p-8 rounded-xl shadow-lg border border-slate-200 text-center max-w-sm w-full">
        <h2 class="text-xl font-bold text-slate-800 mb-4">Event Mendatang</h2>

        <ul class="text-slate-500 mb-6 space-y-2">
            <li>Event 1: Deskripsi singkat tentang event 1.</li>
            <li>Event 2: Deskripsi singkat tentang event 2.</li>
            <li>Event 3: Deskripsi singkat tentang event 3.</li>
        </ul>

      
        <div class="flex flex-col gap-3">
            <a href="/" 
               class="bg-indigo-600 text-white font-semibold py-2 rounded-lg hover:bg-indigo-700 transition">
                Kembali ke Home
            </a>

            <a href="/contact" 
               class="bg-gray-600 text-white font-semibold py-2 rounded-lg hover:bg-gray-700 transition">
                Hubungi Saya
            </a>

            <a href="/bantuan" 
               class="bg-yellow-500 text-white font-semibold py-2 rounded-lg hover:bg-yellow-600 transition">
                Bantuan
            </a>
        </div>
    </div>

</body>
</html>
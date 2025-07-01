
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HR Dashboard</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50 min-h-screen">
    <nav class="shadow mb-8">
    <div class="container mx-auto px-4 py-4">
        <span class="font-bold text-xl">Aktina HR</span>
    </div>
</nav>
    <main>
        {{ $slot }}
    </main>
</body>
</html>

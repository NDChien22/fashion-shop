<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title')</title>
    <link rel="shortcut icon" href="/images/logo/logo.jpg" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    @vite('resources/css/app.css')
    
</head>
<body class="bg-[#f8f9fa] min-h-screen flex flex-col">
    <header id="header" class="h-16 flex-none bg-white border-b border-gray-100 flex items-center justify-between px-3 sm:px-6 md:px-8 gap-6 sticky top-0 z-40">
        <div class="flex items-center gap-3">
            <img 
            src="/images/logo/logo.jpg"
            alt="FastFashion Logo"
            class="h-10 sm:h-12 md:h-16 ml-4 md:ml-7 object-contain">
        </div>
    </header>

    <div class=" flex-grow flex items-center justify-center p-4">
        @yield('content')
    </div>

</body>
</html>


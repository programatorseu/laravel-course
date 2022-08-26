<!doctype html>

<title>Laravel-course</title>
<link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&display=swap" rel="stylesheet">

<body style="font-family: Open Sans, sans-serif">
    <section class="px-6 py-8">
        <nav class="md:flex md:justify-between md:items-center">
            <div>
                <a href="/">
                    <img src="./images/logo.jpg" alt="Logo" width="165" height="16">
                </a>
            </div>

            <div class="mt-8 md:mt-0">
                @auth
                <span class="text-xs font-bold uppercase">
                    Welcome, {{auth()->user()->name }} !
                </span>
                <form action="/logout" method="POST" class="text-xs font-semibold text-blue-500 ml-6">
                    @csrf
                    <button type="submit">Log Out</button>
                </form>
                @else
                    <a href="/register" class="text-xs font-bold uppercase">Register</a>
                @endauth

          
            </div>
        </nav>



        {{$slot}}

        <footer class="bg-gray-100 border border-black border-opacity-5 rounded-xl text-center py-16 px-10 mt-16">

            <h5 class="text-3xl">Just Footer</h5>
            <p class="text-sm mt-3">Footer copyright shit information</p>
        </footer>
    </section>
    <x-flash/>
</body>
<script defer src="https://unpkg.com/alpinejs@3.8.1/dist/cdn.min.js"></script>

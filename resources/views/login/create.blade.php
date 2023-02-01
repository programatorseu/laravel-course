<x-layout>
    <section class="px-6 py-8">
        <main class="max-w-lg mx-auto mt-10 bg-gray-100 border border-gray-200 p-6 rounded-xl">
            <h1 class="text-center font-bold text-xl">Login!</h1>

            <form method="post" action="/login" class="mt-10">
                @csrf

    
                <x-form.input name="email" />
                <x-form.input name="password" type="password" />

                <button>Login</button>
            </form>
            @foreach($errors->all() as $error)
            <li>{{$error}}</li>
        @endforeach
        </main>
    </section>

</x-layout>
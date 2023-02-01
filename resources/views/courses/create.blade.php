<x-layout>
    <section class="px-6 py-8">
        <main class="max-w-lg mx-auto mt-10 bg-gray-100 border border-gray-200 p-6 rounded-xl">

            <form method="POST" action="/admin/courses" enctype="multipart/form-data">
                @csrf

                <x-form.input name="title" />
                <x-form.input name="slug" />
                <x-form.input name="thumbnail" type="file" />
                <x-form.textarea name="excerpt" />
                <x-form.textarea name="body" />
                <x-form.field>
                    <x-form.label name="type" />

                    <select name="type_id" id="type_id">
                        @foreach (\App\Models\Type::all() as $type)
                            <option
                                value="{{ $type->id }}"
                                {{ old('type_id') == $type->id ? 'selected' : '' }}
                            >{{ ucwords($type->name) }}</option>
                        @endforeach
                    </select>

                    <x-form.error name="type" />
                </x-form.field>
                <x-form.button>Publish</x-form.button>
            </form>
        </main>
    </section>
</x-layout>



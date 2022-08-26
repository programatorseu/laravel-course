<x-layout>
    <x-setting heading="Publish New Course">
        <form method="POST" action="/admin/courses" enctype="multipart/form-data">
            @csrf

            <x-form.input name="title" required />
            <x-form.input name="url" required />
            <x-form.input name="thumbnail" type="file" required />
            <x-form.input name="date" required />
            <x-form.textarea name="body" required />

            <x-form.field>
                <x-form.label name="type"/>

                <select name="type_id" id="type_id" required>
                    @foreach (\App\Models\Type::all() as $type)
                        <option
                            value="{{ $type->id }}"
                            {{ old('type_id') == $type->id ? 'selected' : '' }}
                        >{{ ucwords($type->name) }}</option>
                    @endforeach
                </select>

                <x-form.error name="type"/>
            </x-form.field>

            <x-form.button>Publish</x-form.button>
        </form>
    </x-setting>
</x-layout>

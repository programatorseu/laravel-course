<x-layout>
    <x-setting :heading="'Edit Course: ' . $course->title">
        <form method="post" action="/admin/courses/{{ $course->id }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            <x-form.input name="title" :value="old('title', $course->title)" required />
            <x-form.input name="url" :value="old('url', $course->url)" required />

            <div class="flex mt-6">
                <div class="flex-1">
                    <x-form.input name="thumbnail" type="file" :value="old('thumbnail', $course->thumbnail)" />
                </div>

    
                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="" class="rounded-xl ml-6" width="100">
            </div>

            <x-form.input name="date" required>{{ old('date', $course->date) }}</x-form.textarea>
            <x-form.textarea name="body" required>{{ old('body', $course->body) }}</x-form.textarea>

            <x-form.field>
                <x-form.label name="type"/>

                <select name="type_id" id="type_id" required>
                    @foreach (\App\Models\Type::all() as $type)
                        <option
                            value="{{ $type->id }}"
                            {{ old('type_id', $course->type_id) == $type->id ? 'selected' : '' }}
                        >{{ ucwords($type->name) }}</option>
                    @endforeach
                </select>

                <x-form.error name="type"/>
            </x-form.field>

            <x-form.button>Update</x-form.button>
        </form>
    </x-setting>
</x-layout>

<x-layout>
@include('_header')
<main class="max-w-6xl mx-auto mt-6 lg:mt-20 space-y-6">
    @if($courses->count())
    <div class="lg:grid lg:grid-cols-6">
        @foreach($courses as $course)
            <x-course-item :course="$course" class="{{$loop->iteration < 3 ? 'col-span-3' : 'col-span-2'}}"></x-course-item>
        @endforeach
    </div>
    @else
    <p>Brak szkoleń</p>
    @endif
</main>

</x-layout>
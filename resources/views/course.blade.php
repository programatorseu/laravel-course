<x-layout>

    <article class ='transition-colors duration-300 hover:bg-gray-100 border border-black border-opacity-0 hover:border-opacity-5 rounded-xl'
    >
                        <div class="py-6 px-5">
                            <div>
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="Blog Post illustration" class="rounded-xl">

                            </div>
    
                            <div class="mt-8 flex flex-col justify-between">
                                <header>
                                    <div class="space-x-2">
                                        <x-type-button :type="$course->type"/>
                                    </div>
    
                                    <div class="mt-4">
                                        <h1 class="text-3xl">
                                            <?= $course->title; ?>
                                        </h1>
    
                                        <span class="mt-2 block text-gray-400 text-xs">
                                            {{$course->date}}
                                        </span>
                                    </div>
                                </header>
    
                                <div class="text-sm mt-4">
                                    <p>
                                        {{$course->body}}
                                    </p>
    
    
                                </div>
    
                                <footer class="flex justify-between items-center mt-8">
                                    <div class="flex items-center text-sm">
                                        <div class="ml-3">
                                            <h5 class="font-bold">
                                                <a href="/creators/{{$course->creator->username}}">{{$course->creator->name}}</a>
                                            </h5>
                     
                                        </div>
                                    </div>
    
                                    <div>
                                        <a href="/"
                                           class="transition-colors duration-300 text-xs font-semibold bg-gray-500 hover:bg-gray-600 rounded-full py-2 px-8"
                                        >Back</a>
                                    </div>
                                </footer>
                            </div>
                        </div>
                    </article>
                    <section class="col-span-8 col-start-5 mt-10 space-y-6">
                       @auth 
                       <form action="/courses/{{$course->url}}/comments" class="border border-gray-200 p-6 rounded-xl" method="post">

                            @csrf
                            <header class="flex items-center">
                                <img src="https://i.pravatar.cc/60?u={{auth()->id()}}" alt="" width="40" height="40" class="rounded-full" />
                                <h2 class="ml-4">Want to comment ?</h2>
                            </header>
                            <div class="mt-6">
                                <textarea name="body" rows="5" class="w-full text-sm focus:outline-none focus:ring" rows="5" placeholder="Quick, thing of something to say"></textarea>
                            </div>
                            <div class="flex justify-end mt-6 pt-6 border-t border-gray-200">
                                <button type="submit" class="bg-blue-500 text-white uppercase font-semibold text-xs py-2 px-10 rounded-2xl hover:bg-blue-600">Comment it</button>
                            </div>
                        </form>
                        @else
                            <p>
                                <a href="/login">Log in to post a comment</a>
                            </p>
                        @endauth    
                        @foreach ($course->comments as $comment)
                            <x-comment :comment="$comment" />
                        @endforeach
                    </section>

</x-layout>
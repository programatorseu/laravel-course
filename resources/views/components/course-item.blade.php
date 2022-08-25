
@props(['course'])
<article
{{$attributes->merge(['class' => 'transition-colors duration-300 hover:bg-gray-100 border border-black border-opacity-0 hover:border-opacity-5 rounded-xl'])}}>
                    <div class="py-6 px-5">
                        <div>
                            <img src="./images/szolenie-1.jpg" alt="szkolenie" class="rounded-xl">
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
                                    <a href="/courses/<?= $course->url; ?>"
                                       class="transition-colors duration-300 text-xs font-semibold bg-gray-200 hover:bg-gray-300 rounded-full py-2 px-8"
                                    >Read More</a>
                                </div>
                            </footer>
                        </div>
                    </div>
                </article>
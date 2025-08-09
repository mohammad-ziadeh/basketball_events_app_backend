<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid flex-row gap-6">

                <div style="display: flex; justify-content: space-between;">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <h2 class="text-lg font-semibold mb-4">User Gender Distribution</h2>
                        <div style="max-width: 300px; max-height: 300px;">
                            {!! $genderChart->container() !!}
                        </div>
                    </div>
                </div>



                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h2 class="text-lg font-semibold mb-4">Events by Month</h2>
                    <div style="max-width: 100%; max-height: 300px;">
                        {!! $eventChart->container() !!}
                    </div>
                </div>


            </div>
        </div>
    </div>


    {{-- Chart.js CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {!! $genderChart->script() !!}
    {!! $eventChart->script() !!}
</x-app-layout>

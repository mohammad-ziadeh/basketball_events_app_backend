<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid flex-row gap-6">

                <div style="display: flex; gap: 1rem; flex-wrap: wrap;">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                        style="flex: 1; min-width: 280px; display: flex; flex-direction: column; align-items: center;">
                        <h2 class="text-lg font-semibold mb-4">User Gender Distribution</h2>
                        <div style="width: 100%; max-width: 300px; height: 300px;">
                            {!! $genderChart->container() !!}
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6"
                        style="flex: 2; min-width: 280px;">
                        <h2 class="text-lg font-semibold mb-4">User Age Distribution</h2>
                        <div style="width: 100%; height: 300px;">
                            {!! $ageChart->container() !!}
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {!! $genderChart->script() !!}
    {!! $ageChart->script() !!}
    {!! $eventChart->script() !!}
</x-app-layout>

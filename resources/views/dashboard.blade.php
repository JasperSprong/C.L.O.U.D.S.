<?php 
use App\Models\User;
// Use For Debuggin Only
// \Log::info(print_r($var, true));
?>
<x-app-layout>
    <div class="py-12">
            <!-- Display success message -->
            @if(session('success'))
                <div id="successMessage" class="bg-green-500 text-white p-2 rounded mb-4 text-center relative">
                    {{ session('success') }}
                    <!-- Close Button -->
                    <button onclick="closeSuccessMessage()" class="absolute top-0 right-0 mt-1 mr-2 text-white font-bold">&times;</button>
                </div>
            @endif

            <!--it should display Error message -->
            @if(session('error'))
                <div class="bg-red-500 text-white p-2 rounded mb-4 text-center">
                    {{ session('error') }}
                </div>
            @endif

        <!-- Chart Container -->
        <div class="container-fluid bg-blue">
            <div class="row">
                <div class="col-lg-6 w-25">
                    <canvas id="pieChart"></canvas> 
                </div>

                <div class="col-lg-6 d-flex flex-column w-25 h-50" id="chart-div">
                    <!-- Files Types Chart -->
                    <div class=" mb-3" style="width: 100%;">
                            <canvas id="filetypes"></canvas>
                    </div>

                    <!-- Files per Day Chart -->
                    <div class="mb-3" style="width: 100%;">
                            <canvas id="fileperday"></canvas>
                    </div>
                </div>
                <!-- Files per Year Chart  -->
                <div class="col-lg-6 d-flex flex-column w-25 h-50" id="chart-div">
                    <div class="mb-3" style="width: 100%">
                        <canvas id="filesperyear"></canvas>
                    </div>

                </div>
            </div>
        </div>



        <div className="h-100 d-flex align-self-center justify-content-center">
            <x-slot name="header">
                <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight text-center" id="primary-header">
                    {{ __('C.L.O.U.D.S.') }}
                </h2>
                <h4 class="font-semibold text-xl text-gray-750 dark:text-gray-300 leading-tight text-center" id="primary-text">
                    {{ __('Centralized Location for Online User Data Storage') }}
                </h4>
            </x-slot>
        </div>


            
            <div class="py-12">
                <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div class="bg-blue dark:bg-gray-800 shadow-xl sm:rounded-lg p-6" id="scrollable-container">
                            @if($uploads->isEmpty())
                                <p class="bg-orange-500 text-white p-2 rounded mb-4 text-center">No files uploaded yet.</p>
                            @else
                                <ul>
                                    @foreach($uploads as $upload)
                                        <li class="mb-2">
                                            <p class="text-white font-semibold file-name" id="primary-text"> File Name: {{ $upload->filename }}</p>
                                            <p class="text-white font-semibold" id="primary-text"> File ID: {{ $upload->id }}</p>
                                            <p class="text-white font-semibold" id="primary-text"> Original Owner: {{Auth::user()->name}}</p>
                                            <p class="text-white font-semibold" id="primary-text"> Original Owner email: {{Auth::user()->email}}</p>
                                            <p class="text-white font-semibold" id="primary-text"> Date of Upload: {{ $upload->created_at->format('d-m-y H:i:s') }} </p>
                                            <a href="{{ asset('storage/' . $upload->path) }}" target="_blank" class="text-blue-500" id="primary-text">Download</a>
                                            <p class="text-white font-semibold" >    
                                            <!-- Button to trigger modal -->
                                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" data-file-id="{{ $upload->id }}">Share</button>
                                            <!-- Form to delete the upload -->
                                            <form action="{{ route('upload.destroy') }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <!-- Hidden inputs to send the necessary IDs -->
                                                <input type="hidden" name="customer_id" value="{{ $upload->id }}">                                                
                                                <!-- Delete button -->
                                                <button type="submit" class="btn btn-danger">DELETE</button>
                                            </form>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                    </div>
                </div>
            </div>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-blue dark:bg-gray-800 shadow-xl sm:rounded-lg p-6" id="scrollable-container">
                    <!-- Display shared files -->
                    @if($sharedFiles->isEmpty())
                        <p class="bg-orange-500 text-white p-2 rounded mb-4 text-center">No One Has Shared A file With you yet ):</p>
                        @else 
                    <h3 class="text-white" id="primary-header">Files Shared With You</h3>
                    <ul>
                        @foreach($sharedFiles as $sharedFile) 
                            <li class="mb-2">
                                <!-- display the information from each file -->
                                <p class="text-white font-semibold" id="primary-text"> File Name: {{ $sharedFile->file->filename }}</p>
                                <p class="text-white font-semibold" id="primary-text"> Shared By: {{ $sharedFile->email }}</p>
                                <p class="text-white font-semibold" id="primary-text"> Date of Upload: {{ $sharedFile->file->created_at->format('d-m-y H:i:s') }} </p>
                                <a href="{{ asset('storage/' . $sharedFile->file->path) }}" target="_blank" class="text-blue-500" id="primary-text">Download</a>
                            </li>
                        @endforeach
                    </ul>
                @endif 
                </div>
            </div>
        </div>
        <!-- File Upload Form -->
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-blue dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <!-- routing to send it to the correct controller/model in web.php -->
                    <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div>
                            <!-- Chosing the file to be uploaded part of the form -->
                            <label for="file" class="block text-white">Choose a file to upload:</label>
                            <input type="file" name="file" id="file" class="block w-full text-white mt-2">
                            <!-- if it is a bad file it shouldt get uploaded and displays the message -->
                            @error('file')
                                <span class="text-red-500">{{ $message }}</span>
                            @enderror
                        </div>
                        <!-- Simple Upload Button -->
                        <div class="mt-4">
                            <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                                Upload
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <!-- Main Header for the pop-up Modal Title -->
                        <h1 class="modal-title fs-5" id="exampleModalLabel">Share With Other Users</h1>
                        <!-- Close button -->
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Routing Setup for Web.php to get it to the correct controller -->
                        <form action="{{ route('file.share') }}" method="POST">
                        @csrf
                        <input type="hidden" name="file_id" id="fileIdInput">
                        <select class="form-select" name="user_email" id="userSelect" aria-label="Select a user">
                            <option>Select a user's email</option>
                            <!-- Displays the user emails that are registerd to the site, except for themselfs -->
                            @foreach ($users as $user)
                                <option value="{{ $user->email }}">{{ $user->email }}</option>
                            @endforeach
                        </select>

                        <div class="modal-footer">
                            <!--Cancle And Share Button -->
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-primary">Share</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- scripts for total storage -->
        <script>
            var ctx = document.getElementById('pieChart').getContext('2d');
            var myPieChart = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: @json($data['labels']),
                    datasets: [{
                        label: 'Storage Usage',
                        data: @json($data['data']),
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.2)',
                            'rgba(255, 99, 132, 0.2)'
                        ],
                        borderColor: [
                            'rgba(54, 162, 235, 1)',
                            'rgba(255, 99, 132, 1)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    plugins: {
                        title: {
                            display: true,
                            text: 'Storage Usage',
                            color: 'white',
                            font: {
                                size: 30
                            }
                        },
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    let label = context.label || '';
                                    let value = context.raw.toFixed(2);
                                    return label + ': ' + value + '%';
                                }
                            }
                        }
                    }
                }
            });
        </script>

       
    <!-- Bar Chart Script -->
    <script>
        var ctx = document.getElementById('filetypes').getContext('2d');
        var fileTypeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($fietyypelabels),
                datasets: [{
                    label: 'Number of Files',
                    data: @json($fietyypedata),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'File Types Distribution for Your acount',
                        color: 'white',
                        font: {
                            size: 20
                        }
                    },
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
    <!-- Files per Day global chart js -->
    <script>
        var ctx = document.getElementById("fileperday").getContext('2d');
        var filesperdagchart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($upload_dates),
                datasets: [{
                    label: 'Files Per Day',
                    data: @json($upload_counts),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Files Uploaded Per Day Globally of the year 2024',
                        color: 'white',
                        font: {
                            size: 20
                        }
                    },
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>

    <!-- Files per year script -->

    <script>
        var ctx = document.getElementById('filesperyear').getContext('2d');
        var filesperjaarchart = new Chart(ctx, {
            type:'bar',
            data:{
                labels: @json($uplaod_file_year),
                datasets:[{
                    label: 'file per year',
                    data: @json($upload_year_files_amount),
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }],
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                },
                plugins: {
                    title: {
                        display: true,
                        text: 'Files Uploaded Per Year Globally',
                        color: 'white',
                        font: {
                            size: 20
                        }
                    },
                    legend: {
                        display: false
                    }
                }
            }

        });
    </script>




    </div>
</x-app-layout>
<!-- javascript to redirect the select id of the file for the hidden input of the share feautre -->
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var exampleModal = document.getElementById('exampleModal');
        exampleModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var fileId = button.getAttribute('data-file-id');
            var fileIdInput = exampleModal.querySelector('#fileIdInput');
            fileIdInput.value = fileId;
        });
    });
</script>

<script>
    function closeSuccessMessage() {
        var successMessage = document.getElementById('successMessage');
        successMessage.style.display = 'none';
    }
</script>


{{-- getting the development server started 
php -S 127.0.0.1:8000 -t public --}}
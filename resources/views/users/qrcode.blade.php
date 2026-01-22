@extends('layouts.sideBar')

@section('content')
    <div class="container-fluid px-3 px-lg-4 mt-4">
        <!-- User QR Code Card -->
        <div class="card shadow-sm mb-4 rounded-4">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">{{ __('messages.user_qr_code') }}</h4>
            </div>
            <div class="card-body">
                <!-- User Information -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5 class="text-primary">{{ __('messages.user_details') }}</h5>
                        <table class="table table-borderless">
                            <tr>
                                <td class="fw-bold">{{ __('messages.name') }}:</td>
                                <td>{{ $user->name }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">{{ __('messages.email') }}:</td>
                                <td>{{ $user->email }}</td>
                            </tr>
                            <tr>
                                <td class="fw-bold">{{ __('messages.membership_code') }}:</td>
                                <td>{{ $user->membership_code }}</td>
                            </tr>
                        </table>
                    </div>
                </div>

                <!-- QR Code Display -->
                <div class="row">
                    <div class="col-12 text-center">
                        <h5 class="text-primary mb-3">{{ __('messages.qr_code') }}</h5>
                        <div class="d-flex justify-content-center">
                            <div id="qrcode" class="border border-2 border-primary p-3 rounded"></div>
                        </div>
                        <div class="mt-3">
                            <p class="text-muted">{{ __('messages.qr_code_data') }}: <code id="qr-data">{{ $user->qrCode() }}</code></p>
                        </div>

                        <!-- Action Buttons -->
                        <div class="mt-4">
                            <button class="btn btn-primary" onclick="downloadQRCode()">
                                <i class="fas fa-download me-2"></i>{{ __('messages.download_qr') }}
                            </button>
                            <button class="btn btn-secondary" onclick="printQRCode()">
                                <i class="fas fa-print me-2"></i>{{ __('messages.print_qr') }}
                            </button>
                            <a href="{{ route('users.show', $user->id) }}" class="btn btn-outline-primary">
                                <i class="fas fa-arrow-left me-2"></i>{{ __('messages.back_to_user') }}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include QRCode.js library from CDN -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>

    <script>
        // Generate QR Code
        const qrCodeData = "{{ $user->qrCode() }}";
        const qrcode = new QRCode(document.getElementById("qrcode"), {
            text: qrCodeData,
            width: 256,
            height: 256,
            colorDark: "#000000",
            colorLight: "#ffffff",
            correctLevel: QRCode.CorrectLevel.H
        });

        // Download QR Code as PNG
        function downloadQRCode() {
            const canvas = document.querySelector('#qrcode canvas');
            if (canvas) {
                const url = canvas.toDataURL('image/png');
                const link = document.createElement('a');
                link.download = 'qrcode_{{ $user->membership_code }}.png';
                link.href = url;
                link.click();
            }
        }

        // Print QR Code
        function printQRCode() {
            const printWindow = window.open('', '_blank');
            const canvas = document.querySelector('#qrcode canvas');
            if (canvas) {
                const imgData = canvas.toDataURL('image/png');
                printWindow.document.write(`
                    <html>
                        <head>
                            <title>QR Code - {{ $user->name }}</title>
                            <style>
                                body {
                                    display: flex;
                                    flex-direction: column;
                                    align-items: center;
                                    justify-content: center;
                                    height: 100vh;
                                    margin: 0;
                                    font-family: Arial, sans-serif;
                                }
                                img {
                                    border: 2px solid #000;
                                    padding: 10px;
                                }
                                .info {
                                    margin-top: 20px;
                                    text-align: center;
                                }
                            </style>
                        </head>
                        <body>
                            <img src="${imgData}" alt="QR Code">
                            <div class="info">
                                <h3>{{ $user->name }}</h3>
                                <p>{{ __('messages.membership_code') }}: {{ $user->membership_code }}</p>
                            </div>
                        </body>
                    </html>
                `);
                printWindow.document.close();
                printWindow.focus();
                setTimeout(() => {
                    printWindow.print();
                    printWindow.close();
                }, 500);
            }
        }
    </script>
@endsection

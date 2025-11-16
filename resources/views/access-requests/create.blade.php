<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Request Access - {{ $company->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex items-center justify-center p-6">
        <div class="max-w-md w-full">
            <div class="text-center mb-8">
                @if ($company->logo_path)
                    <img src="{{ Storage::url($company->logo_path) }}" alt="{{ $company->name }}" class="h-12 mx-auto mb-4">
                @endif
                <h1 class="text-2xl font-bold text-gray-900">Request Access</h1>
                <p class="text-sm text-gray-600 mt-2">{{ $company->name }}</p>
            </div>

            @if (session('success'))
                <div class="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                    <p class="text-sm text-green-800">{{ session('success') }}</p>
                </div>
            @endif

            @if ($errors->any())
                <div class="mb-6 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <ul class="list-disc list-inside text-sm text-red-800">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('access-requests.store', $company->id) }}" class="bg-white shadow-sm rounded-lg p-6 space-y-4">
                @csrf

                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                        First Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        value="{{ old('first_name') }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                        Last Name <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        value="{{ old('last_name') }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                        Email Address <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="{{ old('email') }}"
                        required
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <div>
                    <label for="message" class="block text-sm font-medium text-gray-700 mb-1">
                        Message <span class="text-xs text-gray-500">(Optional)</span>
                    </label>
                    <textarea
                        id="message"
                        name="message"
                        rows="4"
                        placeholder="Tell us why you'd like access..."
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >{{ old('message') }}</textarea>
                </div>

                <button
                    type="submit"
                    class="w-full px-5 py-2 bg-blue-600 text-white hover:bg-blue-700 font-medium rounded-lg transition-all"
                >
                    Submit Access Request
                </button>
            </form>

            <p class="text-xs text-gray-500 text-center mt-4">
                The company owner will review your request and contact you via email.
            </p>
        </div>
    </div>
</body>
</html>

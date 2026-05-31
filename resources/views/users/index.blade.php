<x-app-layout>
    <div class="p-6">
        <div class="mb-4 text-right">
            <a href="{{ route('users.create') }}" class="bg-gray-600 text-white px-4 py-2 rounded font-bold hover:bg-gray-700">
                New User
            </a>
        </div>
        <h2 class="text-xl font-bold mb-4">प्रयोगकर्ता सूची</h2>
        <table class="w-full border">
            <thead>
                <tr class="bg-gray-200">
                    <th class="p-2">नाम</th>
                    <th class="p-2">इमेल</th>
                    <th class="p-2">रोल</th>
                    <th class="p-2">एक्सन</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                <tr>
                    <td class="p-2 border">{{ $user->name }}</td>
                    <td class="p-2 border">{{ $user->email }}</td>
                    <td class="p-2 border">{{ $user->role }}</td>
                    <td class="p-2 border">
                        <a href="{{ route('users.edit', $user->id) }}" lass="inline"><button type="submit"
            class="bg-green-600 text-white px-3 py-1 rounded">
        Edit
    </button></a>
                        <form action="{{ route('users.destroy', $user->id) }}"
      method="POST"
      class="inline"
      onsubmit="return confirm('के तपाईं यो युजर हटाउन निश्चित हुनुहुन्छ?');">

    @csrf
    @method('DELETE')

    <button type="submit"
            class="bg-red-600 text-white px-3 py-1 rounded">
        Delete
    </button>
</form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>